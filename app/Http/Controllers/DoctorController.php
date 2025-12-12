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
        $todayVisits = Visit::with('patient')
            ->whereDate('visit_date', today())
            ->orderBy('visit_time')
            ->get();

        $visit = null;

        if ($request->filled('patient_id')) {
            $search = trim($request->patient_id);

            $patient = Patient::where('patient_id', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->first();

            if (!$patient) {
                return back()->withErrors(['patient_id' => "No patient found for: {$search}"]);
            }

            $visit = Visit::updateOrCreate(
                ['patient_id' => $patient->id, 'visit_date' => today()],
                ['visit_time' => now(), 'registration_amount' => setting('registration_fee', 200), 'status' => 'consulting']
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
            'labOrders.test',
            'labOrders.result',
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

            'medicines' => 'nullable|array',
            'medicines.*.medicine_id'   => 'nullable|exists:medicines_master,id',
            'medicines.*.dosage'        => 'required_if:medicines.*.medicine_id,!=,|string',
            'medicines.*.duration_days' => 'required_if:medicines.*.medicine_id,!=,|integer|min:1',
            'medicines.*.instruction'   => 'nullable|string',

            'injection_medicine_id' => 'nullable|exists:medicines_master,id',
            'injection_route'       => 'nullable|string',

            'ward_id'               => 'nullable|exists:wards,id',
            'admission_reason'      => 'nullable|string',
        ]);

        // Save clinical notes
        $visit->vitals()->updateOrCreate(
            ['visit_id' => $visit->id],
            $request->only(['chief_complaint', 'examination', 'diagnosis'])
        );

        $hasLab       = $request->filled('lab_tests');
        $hasMedicine  = collect($request->medicines ?? [])->filter(fn($m) => !empty($m['medicine_id']))->count() > 0;
        $hasInjection = $request->filled('injection_medicine_id');
        $hasAdmission = $request->filled('ward_id');

        $hasAnyService = $hasLab || $hasMedicine || $hasInjection || $hasAdmission;

        // Lab Orders
        if ($hasLab) {
            foreach ($request->lab_tests as $testId) {
                VisitLabOrder::updateOrCreate([
                    'visit_id' => $visit->id,
                    'lab_test_id' => $testId
                ], ['extra_instruction' => $request->lab_instruction ?? '']);
            }
        }

        // Medicine Orders
        if ($hasMedicine) {
            $alreadyPrescribed = [];
            $newlyAdded = [];

            foreach ($request->medicines as $med) {
                if (empty($med['medicine_id'])) {
                    continue;
                }

                $medicineId = $med['medicine_id'];
                $medicineName = \App\Models\MedicineMaster::find($medicineId)->medicine_name ?? 'Unknown Medicine';

                $exists = VisitMedicineOrder::where('visit_id', $visit->id)
                    ->where('medicine_id', $medicineId)
                    ->exists();

                if ($exists) {
                    $alreadyPrescribed[] = $medicineName;
                } else {
                    VisitMedicineOrder::create([
                        'visit_id'         => $visit->id,
                        'medicine_id'      => $medicineId,
                        'dosage'           => $med['dosage'] ?? '',
                        'duration_days'    => $med['duration_days'] ?? 1,
                        'instruction'      => $med['instruction'] ?? '',
                        'extra_instruction'=> $med['extra_instruction'] ?? '',
                    ]);
                    $newlyAdded[] = $medicineName;
                }
            }

            $message = 'Prescription sent successfully!';
            if (!empty($alreadyPrescribed)) {
                $list = implode(', ', $alreadyPrescribed);
                $message .= " | Already prescribed (not added again): $list";
            }
            if (!empty($newlyAdded) && empty($alreadyPrescribed)) {
                $message = 'Prescription sent successfully! Medicines added: ' . implode(', ', $newlyAdded);
            }

            return back()->with('success', $message);
        }

        // Injection
        if ($hasInjection) {
            VisitInjectionOrder::create([
                'visit_id' => $visit->id,
                'medicine_id' => $request->injection_medicine_id,
                'route' => $request->injection_route,
            ]);
        }

        // Admission
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

        // FINAL STATUS LOGIC – FIXED!
        if ($request->has('follow_up_only')) {
            $visit->update([
                'status' => 'follow_up',
                'all_services_completed' => true
            ]);
            return back()->with('success', 'Follow-up advised');
        }

        if ($hasAnyService) {
            // Doctor ordered services → send to billing immediately
            $visit->update([
                'status' => $hasLab ? 'sent_to_lab' :
                           ($hasMedicine || $hasInjection ? 'sent_to_pharmacy' :
                           ($hasAdmission ? 'admitted' : 'consulting')),
                'all_services_completed' => false  // Services pending payment/completion
            ]);
        } else {
            // No services ordered → consultation complete
            $visit->update([
                'status' => 'consulting',
                'all_services_completed' => true
            ]);
        }

        return back()->with('success', 'Prescription sent successfully!');
    }

    private function authorizeAccess()
    {
        $user = Auth::user();
        if (!$user->hasRole('Doctor') && !$user->hasRole('Admin')) {
            abort(403, 'Unauthorized');
        }
    }
}