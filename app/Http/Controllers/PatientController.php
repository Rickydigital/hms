<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Setting;          
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view patients')->only('index');
        $this->middleware('permission:create patient')->only('store');
        $this->middleware('permission:reactivate patient')->only('reactivate');
    }

    public function index(Request $request)
{
    $query = Patient::query();

    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function ($q) use ($search) {
            $q->where('patient_id', 'LIKE', "%{$search}%")
              ->orWhere('name', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%");
        });
    }

    // Avoid N+1 for today visit check
    $patients = $query
        ->latest()
        ->paginate(20)
        ->withQueryString();

    // ✅ If request is AJAX, return JSON (same route, same controller)
    if ($request->ajax() || $request->wantsJson()) {
        $today = today();

        $data = $patients->getCollection()->map(function ($p) use ($today) {
            $expired = $p->expiry_date && $p->expiry_date->lt($today);

            $hasVisitToday = $p->visits()
                ->whereDate('visit_date', $today)
                ->exists();

            // build display age like you do in blade
            $ageDisplay = '—';
            if ($p->age_months || $p->age_days) {
                $ageDisplay =
                    trim(($p->age_months ? $p->age_months . ' months ' : '') .
                         ($p->age_days ? $p->age_days . ' days' : ''));
            } elseif (!is_null($p->age)) {
                $ageDisplay = $p->age . ' yrs';
            }

            return [
                'id' => $p->id,
                'name' => $p->name,
                'patient_id' => $p->patient_id,
                'gender' => $p->gender,
                'phone' => $p->phone,
                'address' => $p->address,
                'age_display' => $ageDisplay,
                'expiry_date' => optional($p->expiry_date)->format('d M Y'),
                'is_active' => (bool) $p->is_active,
                'expired' => $expired,
                'has_visit_today' => $hasVisitToday,
            ];
        });

        return response()->json([
            'patients' => $data,
            'pagination' => (string) $patients->links('pagination::bootstrap-5'),
        ]);
    }

    return view('patients.index', compact('patients'));
}



    

public function store(Request $request)
{
    $request->validate([
        'name'        => 'required|string|max:255',
        'age'         => 'nullable|integer|min:0|max:120',
        'age_months'  => 'nullable|integer|min:0|max:11',
        'age_days'    => 'nullable|integer|min:0|max:31',
        'gender'      => 'required|in:Male,Female,Other',
        'phone' => 'nullable|string|max:15',
        'address'     => 'nullable|string|max:500',
    ]);

    

    try {
        DB::transaction(function () use ($request) {
            Patient::create([
                'patient_id'        => Patient::generatePatientId(),
                'name'              => $request->name,
                'age'               => $request->age ?? null,                    // can be null or any value
                'age_months'        => $request->age_months ?? null,
                'age_days'          => $request->age_days ?? null,
                'gender'            => $request->gender,
                'phone'             => $request->phone,
                'address'           => $request->address ?? null,
                'registration_date' => now(),
                'expiry_date'       => now()->addMonths(
                    (int) Setting::get('card_validity_months', 12)
                ),
                'is_active'         => true,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Patient registered successfully!'
        ]);

    } catch (\Exception $e) {
        Log::error('Patient create error: ' . $e->getMessage(), [
            'request' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please try again.'
        ], 500);
    }
}


public function history(Patient $patient)
{
    $patient->load([
        'visits' => function ($q) {
            $q->latest('visit_date')
              ->latest('visit_time')
              ->with([
                  'doctor:id,name',
                  'vitals',
                  'labOrders.results',      // if you have results relation
                  'medicineOrders.medicine' // medicine master
              ]);
        }
    ]);

    return response()->json([
        'success' => true,
        'patient' => $patient
    ]);
}

public function update(Request $request, Patient $patient)
{
    $request->validate([
        'name'        => 'required|string|max:255',
        'age'         => 'nullable|integer|min:0|max:120',
        'age_months'  => 'nullable|integer|min:0|max:11',
        'age_days'    => 'nullable|integer|min:0|max:31',
        'gender'      => 'required|in:Male,Female,Other',
        'phone' => 'nullable|string|max:15',
        'address'     => 'nullable|string|max:500',
    ]);

    // === ALL AGE RESTRICTIONS REMOVED ===

    try {
        $patient->update([
            'name'        => $request->name,
            'age'         => $request->age ?? null,
            'age_months'  => $request->age_months ?? null,
            'age_days'    => $request->age_days ?? null,
            'gender'      => $request->gender,
            'phone'       => $request->phone,
            'address'     => $request->address,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Patient details updated successfully!'
        ]);

    } catch (\Exception $e) {
        Log::error('Patient update error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to update patient.'
        ], 500);
    }
}

    public function reactivate(Patient $patient)
    {
        $months = (int) Setting::get('card_validity_months', 12);
        $fee    = (float) Setting::get('reactivation_fee', 150.00);

        $patient->update([
            'expiry_date'           => now()->addMonths($months),
            'is_active'             => true,
            'reactivation_fee_paid' => number_format($fee, 2, '.', ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Card reactivated for {$months} months!"
        ]);
    }
}