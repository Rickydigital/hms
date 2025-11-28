<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\PatientVital;
use App\Models\VisitLabOrder;
use App\Models\VisitMedicineOrder;
use App\Models\VisitInjectionOrder;
use App\Models\VisitBedAdmission;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function __construct()
    {
        // Allow ONLY Doctor role OR Admin role
        $this->middleware(function ($request, $next) {
            $user = Auth::user();

            if (!$user->hasRole('Doctor') && !$user->hasRole('Admin')) {
                abort(403, 'Access denied. Only Doctors and Admin can access OPD.');
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // Show all today's visits (any doctor can see all patients)
        $todayVisits = Visit::with('patient')
            ->whereDate('visit_date', today())
            ->orderBy('visit_time')
            ->get();

        $visit = null;

        if ($request->filled('patient_id')) {
            $patient = Patient::where('patient_id', $request->patient_id)->firstOrFail();

            $visit = Visit::firstOrCreate(
                [
                    'patient_id' => $patient->id,
                    'visit_date' => today(),
                ],
                [
                    'visit_time' => now(),
                    'registration_amount' => setting('registration_fee', 200),
                    'status' => 'consulting',
                    // doctor_id is NO LONGER SET → Any doctor can treat
                ]
            );

            return redirect()->route('doctor.opd.show', $visit);
        }

        return view('doctor.opd.index', compact('todayVisits', 'visit'));
    }

    public function show(Visit $visit)
{
    $visit->load([
        'patient', 
        'vitals', 
        'labOrders.test',           // ← already had
        'labOrders.result',         // ← ADD THIS LINE
        'medicineOrders.medicine', 
        'injectionOrders.medicine', 
        'bedAdmission.ward'
    ]);

    $todayVisits = Visit::whereDate('visit_date', today())
        ->orderBy('visit_time')
        ->get();

    return view('doctor.opd.index', compact('visit', 'todayVisits'));
}

    public function storeVitals(Request $request, Visit $visit)
    {
        $this->authorizeAccess();

        $request->validate([
            'bp' => 'nullable|string',
            'pulse' => 'nullable|integer',
            'temperature' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
        ]);

        $visit->vitals()->updateOrCreate(
            ['visit_id' => $visit->id],
            $request->only(['bp', 'pulse', 'temperature', 'weight', 'height'])
        );

        return back()->with('success', 'Vitals saved successfully!');
    }

      public function storePrescription(Request $request, Visit $visit)
{
    $this->authorizeAccess();

    $request->validate([
        'chief_complaint' => 'nullable|string',
        'examination'     => 'nullable|string',
        'diagnosis'       => 'nullable|string',
        'lab_tests.*'     => 'exists:lab_tests_master,id',

        // MEDICINES – Only validate if medicine_id is present
        'medicines' => 'nullable|array',
        'medicines.*.medicine_id'   => 'nullable|exists:medicines_master,id',
        'medicines.*.dosage'        => 'required_if:medicines.*.medicine_id,!=,|string',
        'medicines.*.duration_days' => 'required_if:medicines.*.medicine_id,!=,|integer|min:1',
        'medicines.*.instruction'   => 'nullable|string',

            // INJECTION
        'injection_medicine_id' => 'nullable|exists:medicines_master,id',
        'injection_route'       => 'nullable|string',

        // ADMISSION
        'ward_id'               => 'nullable|exists:wards,id',
        'admission_reason'      => 'nullable|string',
    ]);

    // Save clinical notes
    $visit->vitals()->updateOrCreate(['visit_id' => $visit->id], $request->only([
        'chief_complaint', 'examination', 'diagnosis'
    ]));

    $hasLab       = $request->filled('lab_tests');
    $hasMedicine  = collect($request->medicines ?? [])->filter(fn($m) => !empty($m['medicine_id']))->count() > 0;
    $hasInjection = $request->filled('injection_medicine_id');
    $hasAdmission = $request->filled('ward_id');

    // Lab
    if ($hasLab) {
        foreach ($request->lab_tests as $testId) {
            VisitLabOrder::updateOrCreate([
                'visit_id' => $visit->id,
                'lab_test_id' => $testId
            ], ['extra_instruction' => $request->lab_instruction ?? '']);
        }
    }

    // Medicines – Only save if medicine selected
    if ($hasMedicine) {
        foreach ($request->medicines as $med) {
            if (!empty($med['medicine_id'])) {
                VisitMedicineOrder::create([
                    'visit_id'       => $visit->id,
                    'medicine_id'    => $med['medicine_id'],
                    'dosage'         => $med['dosage'] ?? '',
                    'duration_days'  => $med['duration_days'] ?? 1,
                    'instruction'    => $med['instruction'] ?? '',
                ]);
            }
        }
    }

    // Injection & Admission (same as before)
    if ($hasInjection) {
        VisitInjectionOrder::create([
            'visit_id' => $visit->id,
            'medicine_id' => $request->injection_medicine_id,
            'route' => $request->injection_route,
        ]);
    }

    if ($hasAdmission) {
        $ward = Ward::findOrFail($request->ward_id);
        if ($ward->available_beds <= 0) {
            return back()->withErrors(['ward_id' => 'No beds available']);
        }
        VisitBedAdmission::create([
            'visit_id' => $visit->id,
            'ward_id' => $ward->id,
            'admission_date' => today(),
            'admission_reason' => $request->admission_reason,
        ]);
    }

    // Smart Status
    if ($request->has('follow_up_only')) {
        $visit->update(['status' => 'follow_up', 'all_services_completed' => true]);
        return back()->with('success', 'Follow-up advised');
    }

    $status = $hasLab ? 'sent_to_lab' : ($hasMedicine || $hasInjection ? 'sent_to_pharmacy' : ($hasAdmission ? 'admitted' : 'follow_up'));
    $completed = !in_array($status, ['sent_to_lab', 'sent_to_pharmacy']);

    $visit->update(['status' => $status, 'all_services_completed' => $completed]);

    return back()->with('success', 'Prescription sent successfully!');
}

    // Simple access control (Doctor OR Admin)
    private function authorizeAccess()
    {
        $user = Auth::user();
        if (!$user->hasRole('Doctor') && !$user->hasRole('Admin')) {
            abort(403, 'Unauthorized');
        }
    }
}