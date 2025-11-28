<?php
namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\PatientVital;
use App\Models\VisitPrescription;
use App\Models\VisitLabOrder;
use Illuminate\Http\Request;

class OpdDoctorController extends Controller
{
    public function index()
    {
        $todayQueue = Visit::with(['patient', 'vitals'])
            ->whereDate('visit_date', today())
            ->where('status', 'in_opd')
            ->orderBy('visit_time')
            ->get();

        $completedToday = Visit::whereDate('visit_date', today())
            ->where('status', 'completed')
            ->count();

        return view('doctor.opd', compact('todayQueue', 'completedToday'));
    }

    // All other methods (saveVitals, prescription, etc.) will come in next message
}