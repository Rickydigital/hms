<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Patient;
use App\Models\PatientVital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class VisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create visit');
    }

    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'chief_complaint' => 'required|string|max:500',
            'height'          => 'nullable|numeric',
            'weight'          => 'nullable|numeric',
            'bp'              => 'nullable|string|max:20',
            'temperature'     => 'nullable|numeric',
            'pulse'           => 'nullable|integer',
            'respiration'     => 'nullable|integer',
            'history'         => 'nullable|string',
        ]);

        if (!$patient->is_active || $patient->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Patient card is expired or inactive!'
            ], 403);
        }

        try {
            $result = DB::transaction(function () use ($request, $patient) {

                // Check if patient already has an active visit within the last 72 hours
                $existingVisit = Visit::where('patient_id', $patient->id)
                    ->where('created_at', '>=', now()->subHours(72))
                    ->whereIn('status', [
                        'in_opd',
                        'consulting',
                        'sent_to_lab',
                        'sent_to_pharmacy',
                        'sent_to_procedure',
                        'follow_up',
                    ])
                    ->latest()
                    ->first();

                if ($existingVisit) {
                    // Update or create vitals on the reused visit
                    PatientVital::updateOrCreate(
                        ['visit_id' => $existingVisit->id],
                        [
                            'chief_complaint' => $request->chief_complaint,
                            'history'         => $request->history,
                            'height'          => $request->filled('height') ? $request->height : null,
                            'weight'          => $request->filled('weight') ? $request->weight : null,
                            'bp'              => $request->bp,
                            'temperature'     => $request->filled('temperature') ? $request->temperature : null,
                            'pulse'           => $request->pulse,
                            'respiration'     => $request->respiration,
                        ]
                    );

                    return [
                        'reused' => true,
                        'visit'  => $existingVisit,
                    ];
                }

                // Create a fresh visit if none exists within 72 hours
                $visit = Visit::create([
                    'patient_id'             => $patient->id,
                    'doctor_id'              => null,
                    'visit_date'             => now()->toDateString(),
                    'visit_time'             => now(),
                    'status'                 => 'in_opd',
                    'registration_amount'    => 0.00,
                    'registration_paid'      => true,
                    'all_services_completed' => false,
                ]);

                PatientVital::create([
                    'visit_id'        => $visit->id,
                    'chief_complaint' => $request->chief_complaint,
                    'history'         => $request->history,
                    'height'          => $request->filled('height') ? $request->height : null,
                    'weight'          => $request->filled('weight') ? $request->weight : null,
                    'bp'              => $request->bp,
                    'temperature'     => $request->filled('temperature') ? $request->temperature : null,
                    'pulse'           => $request->pulse,
                    'respiration'     => $request->respiration,
                ]);

                return [
                    'reused' => false,
                    'visit'  => $visit,
                ];
            });

            return response()->json([
                'success'  => true,
                'message'  => $result['reused']
                    ? 'Existing visit reused successfully (within 72 hours).'
                    : 'Patient sent to OPD queue successfully!',
                'visit_id' => $result['visit']->id,
                'reused'   => $result['reused'],
            ]);

        } catch (\Throwable $e) {
            Log::error('Visit creation failed: ' . $e->getMessage(), [
                'patient_id' => $patient->id,
                'request'    => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error. Please try again.'
            ], 500);
        }
    }
}