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
            ->where('created_at', '>=', now()->subHours(72))
            ->where('status', 'in_opd')
            ->orderByDesc('created_at')
            ->get();

        $completedToday = Visit::where('created_at', '>=', now()->subHours(72))
            ->where('status', 'completed')
            ->count();

        return view('doctor.opd', compact('todayQueue', 'completedToday'));
    }

    // All other methods (saveVitals, prescription, etc.) will come in next message
}