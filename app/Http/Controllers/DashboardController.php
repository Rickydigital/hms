<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Common data for all users
        $data = [
            'todayPatients' => Visit::whereDate('visit_date', $today)->count(),
            'todayRevenue'  => Payment::whereDate('paid_at', $today)->sum('amount'),
        ];

        // Role-specific extra data (optional – you can expand later)
        if ($user->hasRole('Doctor')) {
            $data['myTodayPatients'] = Visit::whereHas('prescription', function($q) use ($user) {
                $q->where('doctor_id', $user->id);
            })->whereDate('visit_date', $today)->count();
        }

        if ($user->hasRole('Reception')) {
            $data['pendingRegistrations'] = Visit::whereDate('visit_date', $today)
                ->whereNull('registration_completed_at')
                ->count();
        }

        return view('dashboard', $data);
    }
}