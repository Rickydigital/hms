<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\PatientVital;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create visit');
    }

       // app/Http/Controllers/VisitController.php
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
        DB::transaction(function () use ($request, $patient) {
            $visit = Visit::create([
                'patient_id'          => $patient->id,
                'doctor_id'           => auth()->id(),
                'visit_date'          => today(),
                'visit_time'          => now(),
                'status'              => 'in_opd',
                'registration_amount' => 0.00,
                'registration_paid'   => true,
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
        });

        return response()->json([
            'success' => true,
            'message' => 'Patient sent to OPD queue successfully!'
        ]);

    } catch (\Exception $e) {
        Log::error('Visit creation failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Server error. Please try again.'
        ], 500);
    }
}
}