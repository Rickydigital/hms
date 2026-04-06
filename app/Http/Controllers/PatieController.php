<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Setting;
use App\Models\Visit;
use App\Models\VisitLabOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class PatieController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view patients')->only([
        'index', 'historyIndex', 'historySearch', 'historyData'
        ]);
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

    $patients = $query->latest()->paginate(20)->withQueryString();

    if ($request->ajax()) {
        return response()->json([
            'html'       => view('patients.partials.patients-grid', compact('patients'))->render(),
            'pagination' => $patients->links('pagination::bootstrap-5')->toHtml(),
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
        'is_rch' => 'nullable|boolean',
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
                'is_rch' => (bool) $request->is_rch,
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




/**
 * History page: list patients A-Z
 */
public function historyIndex(Request $request)
{
    $query = Patient::query();

    // ✅ SEARCH FILTER
    if ($request->filled('search')) {
        $search = trim($request->search);
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('patient_id', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    // ✅ DATE FILTER (DEFAULT = THIS MONTH)
    $startDate = $request->start_date;
    $endDate   = $request->end_date;

    if (!$startDate || !$endDate) {
        $startDate = now()->startOfMonth()->toDateString();
        $endDate   = now()->endOfMonth()->toDateString();
    }

    // ✅ CLONE for summary
    $summaryQuery = clone $query;

    // =========================
    // 📊 SUMMARY CALCULATIONS
    // =========================

    // Total patients (filtered by search only)
    $totalPatients = $summaryQuery->count();

    // Total visits (FILTERED BY DATE)
    $totalVisits = DB::table('visits')
        ->whereBetween('visit_date', [$startDate, $endDate])
        ->whereIn('patient_id', $summaryQuery->pluck('id'))
        ->count();

    // Returning patients (visited in selected period)
    $returningPatients = (clone $summaryQuery)
        ->whereHas('visits', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('visit_date', [$startDate, $endDate]);
        })
        ->count();

    // New patients (never visited EVER)
   $newPatients = (clone $summaryQuery)
    ->whereDoesntHave('visits', function ($q) use ($startDate) {
        $q->where('visit_date', '<', $startDate);
    })
    ->count();

    // =========================
    // 📄 PAGINATION
    // =========================
    $patients = $query->orderBy('name')
        ->paginate(20)
        ->withQueryString();

    return view('patients.history', compact(
        'patients',
        'totalPatients',
        'totalVisits',
        'newPatients',
        'returningPatients',
        'startDate',
        'endDate'
    ));
}

public function historySearch(Request $request)
{
    $term = trim($request->get('term') ?? '');

    $patients = Patient::query()
        ->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('patient_id', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%");
        })
        ->limit(20)
        ->get();

    return response()->json([
        'results' => $patients->map(function ($p) {
            return [
                'id' => $p->id,
                'text' => "{$p->name} • {$p->patient_id}" . ($p->phone ? " • {$p->phone}" : '')
            ];
        })
    ]);
}

public function sendRchToLab(Request $request, Patient $patient)
{
    $request->validate([
        'lab_tests' => 'required|array|min:1',
        'lab_tests.*' => 'exists:lab_tests_master,id',
        'lab_instruction' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    if (!$patient->is_rch) {
        return response()->json([
            'success' => false,
            'message' => 'Only RCH patients can be sent directly to lab.'
        ], 422);
    }

    if (!$patient->is_active || $patient->isExpired()) {
        return response()->json([
            'success' => false,
            'message' => 'Patient card is expired or inactive.'
        ], 403);
    }

    DB::beginTransaction();

    try {
        $visit = Visit::where('patient_id', $patient->id)
            ->where('created_at', '>=', now()->subHours(72))
            ->where('source', 'rch_direct')
            ->whereIn('status', ['sent_to_lab', 'consulting', 'follow_up'])
            ->latest()
            ->first();

        if (!$visit) {
            $visit = Visit::create([
                'patient_id'             => $patient->id,
                'doctor_id'              => null,
                'visit_date'             => now()->toDateString(),
                'visit_time'             => now(),
                'visit_type'             => 'lab_only',
                'source'                 => 'rch_direct',
                'status'                 => 'sent_to_lab',
                'registration_amount'    => 0,
                'registration_paid'      => true,
                'all_services_completed' => false,
            ]);
        }

        $visit->vitals()->updateOrCreate(
            ['visit_id' => $visit->id],
            [
                'chief_complaint' => 'RCH direct lab request',
                'history'         => $request->notes,
                'diagnosis'       => $request->notes,
            ]
        );

        foreach ($request->lab_tests as $testId) {
            VisitLabOrder::firstOrCreate(
                [
                    'visit_id' => $visit->id,
                    'lab_test_id' => $testId,
                ],
                [
                    'extra_instruction' => $request->lab_instruction ?? '',
                ]
            );
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'RCH patient assigned to lab successfully.',
            'visit_id' => $visit->id,
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();

        Log::error('RCH direct lab failed: ' . $e->getMessage(), [
            'patient_id' => $patient->id,
            'request' => $request->all(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to send patient to lab.'
        ], 500);
    }
}

public function historyData(Patient $patient)
{
    $patient->load([
        'visits' => function ($q) {
            $q->latest('visit_date')
              ->latest('visit_time')
              ->with([
                  'doctor:id,name',
                  'vitals',
                  'labOrders.test',
                  'labOrders.result',
                  'labOrders.paidBy:id,name',
                  'medicineOrders.medicine',
                  'medicineOrders.issuedBy:id,name',
                  'medicineOrders.paidBy:id,name',
                  'medicineOrders.handedOverBy:id,name',
                  'medicineOrders.pharmacyIssues.issuedBy:id,name',
                  'procedures.procedure',
                  'payments.receivedBy:id,name',
                  'receipt',
              ]);
        },
    ]);

    return response()->json([
        'success' => true,
        'patient' => $patient,
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
