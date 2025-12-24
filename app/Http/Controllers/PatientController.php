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
            $search = $request->search;
            $query->where(fn($q) => $q
                ->where('patient_id', 'LIKE', "%$search%")
                ->orWhere('name', 'LIKE', "%$search%")
                ->orWhere('phone', 'LIKE', "%$search%")
            );
        }

        $patients = $query->latest()->paginate(20)->withQueryString();

        return view('patients.index', compact('patients'));
    }


    // PatientController.php

public function update(Request $request, Patient $patient)
{
    $request->validate([
        'name'        => 'required|string|max:255',
        'age'         => 'nullable|integer|min:0|max:120',
        'age_months'  => 'nullable|integer|min:0|max:11',
        'age_days'    => 'nullable|integer|min:0|max:31',
        'gender'      => 'required|in:Male,Female,Other',
        'phone'       => 'required|string|max:15|unique:patients,phone,' . $patient->id,
        'address'     => 'nullable|string|max:500',
    ]);

    // At least one age field must be provided
    if (!$request->filled('age') && !$request->filled('age_months') && !$request->filled('age_days')) {
        return response()->json([
            'success' => false,
            'message' => 'Please enter age in years OR months/days.'
        ], 422);
    }

    // Prevent mixing years with months/days
    if ($request->filled('age') && ($request->filled('age_months') || $request->filled('age_days'))) {
        return response()->json([
            'success' => false,
            'message' => 'Enter either years OR months/days — not both.'
        ], 422);
    }

    try {
        $patient->update([
            'name'        => $request->name,
            'age'         => $request->filled('age') ? $request->age : null,
            'age_months'  => $request->age_months,
            'age_days'    => $request->age_days,
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

    // PatientController@store

public function store(Request $request)
{
    $request->validate([
        'name'        => 'required|string|max:255',
        'age'         => 'nullable|integer|min:0|max:120', // now nullable
        'age_months'  => 'nullable|integer|min:0|max:11',
        'age_days'    => 'nullable|integer|min:0|max:31',
        'gender'      => 'required|in:Male,Female,Other',
        'phone'       => 'required|string|max:15|unique:patients,phone',
        'address'     => 'nullable|string|max:500',
    ]);

    // At least one age field must be provided
    if (!$request->filled('age') && !$request->filled('age_months') && !$request->filled('age_days')) {
        return response()->json([
            'success' => false,
            'message' => 'Please enter age in years OR months/days.'
        ], 422);
    }

    try {
        DB::transaction(function () use ($request) {
            Patient::create([
                'patient_id'        => Patient::generatePatientId(),
                'name'              => $request->name,
                'age'               => $request->filled('age') ? $request->age : null,
                'age_months'        => $request->age_months,
                'age_days'          => $request->age_days,
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
        Log::error('Patient create error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please try again.'
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