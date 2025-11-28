<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\MedicineMaster;
use App\Models\MedicineBatch;
use App\Models\PharmacyIssue;
use App\Models\VisitLabOrder;
use App\Models\Payment;
use App\Models\PharmacySale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // KEY METRICS
        $data = [
                'total_patients'          => Patient::count(),
                'today_opd'               => Visit::whereDate('visit_date', $today)->count(),
                'total_medicines'         => MedicineMaster::active()->count(),
                'total_stock_value'       => MedicineBatch::sum(DB::raw('current_stock * purchase_price')),
                'low_stock_items'         => MedicineMaster::active()
                                            ->whereHas('batches', fn($q) => $q->where('current_stock', '<=', 10))
                                            ->count(),
                'expired_medicines'       => MedicineBatch::where('expiry_date', '<', $today)
                                            ->where('current_stock', '>', 0)->count(),

                // OPD Payments
                'today_revenue'           => Payment::whereDate('paid_at', $today)->sum('amount'),
                'month_revenue'           => Payment::whereMonth('paid_at', $today->month)
                                            ->whereYear('paid_at', $today->year)->sum('amount'),

                // Pharmacy (OTC) Sales
                'today_pharmacy_sales'    => PharmacySale::whereDate('sold_at', $today)->sum('total_amount'),
                'total_pharmacy_sales'    => PharmacySale::sum('total_amount'),

                // Combined Revenue
                'today_total_revenue'     => Payment::whereDate('paid_at', $today)->sum('amount') +
                                            PharmacySale::whereDate('sold_at', $today)->sum('total_amount'),
                'total_revenue_all_time'  => Payment::sum('amount') + PharmacySale::sum('total_amount'),

                // Services Today
                'today_medicines_issued'  => PharmacyIssue::whereDate('issued_at', $today)->sum('quantity_issued'),
                'today_lab_tests'         => VisitLabOrder::where('is_completed', true)
                                            ->whereDate('completed_at', $today)->count(),
            ];

        // CHARTS DATA
        $data['revenue_chart'] = $this->getRevenueChart();
        $data['opd_chart']     = $this->getOpdChart();
        $data['top_medicines'] = $this->getTopSellingMedicines();

        return view('admin.dashboard', $data);
    }

    private function getRevenueChart()
    {
        $days = 7;
        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('D');
            $values[] = Payment::whereDate('paid_at', $date)->sum('amount');
        }

        return [
            'labels' => $labels,
            'data'   => $values
        ];
    }

    private function getOpdChart()
    {
        $days = 7;
        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d M');
            $values[] = Visit::whereDate('visit_date', $date)->count();
        }

        return [
            'labels' => $labels,
            'data'   => $values
        ];
    }

    private function getTopSellingMedicines()
    {
        return PharmacyIssue::with('medicine')
            ->select('medicine_id', DB::raw('SUM(quantity_issued) as total'))
            ->groupBy('medicine_id')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
    }
}