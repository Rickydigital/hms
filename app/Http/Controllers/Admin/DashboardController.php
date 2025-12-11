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
use Illuminate\Support\Collection;

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

    public function revenue()
    {
        $from = Carbon::parse(request('from', Carbon::today()->subMonth()))->startOfDay();
        $to = Carbon::parse(request('to', Carbon::today()))->endOfDay();

        $opdRevenues = Payment::select(
            DB::raw('DATE(paid_at) as date'),
            DB::raw('SUM(amount) as opd_revenue')
        )
            ->whereBetween('paid_at', [$from, $to])
            ->groupBy('date')
            ->pluck('opd_revenue', 'date');

        $pharmacyRevenues = PharmacySale::select(
            DB::raw('DATE(sold_at) as date'),
            DB::raw('SUM(total_amount) as pharmacy_revenue')
        )
            ->whereBetween('sold_at', [$from, $to])
            ->groupBy('date')
            ->pluck('pharmacy_revenue', 'date');

        $dates = collect(array_unique(array_merge($opdRevenues->keys()->toArray(), $pharmacyRevenues->keys()->toArray())))
            ->sort()
            ->values();

        $revenues = $dates->map(function ($date) use ($opdRevenues, $pharmacyRevenues) {
            $opd = $opdRevenues->get($date, 0);
            $pharmacy = $pharmacyRevenues->get($date, 0);
            return [
                'date' => $date,
                'opd_revenue' => $opd,
                'pharmacy_revenue' => $pharmacy,
                'total_revenue' => $opd + $pharmacy,
            ];
        })->sortByDesc('date');

        // For pagination (if range is large)
        $perPage = 30;
        $page = request('page', 1);
        $paginatedRevenues = new \Illuminate\Pagination\LengthAwarePaginator(
            $revenues->forPage($page, $perPage),
            $revenues->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Chart data (sorted ascending for time series)
        $chartLabels = $dates->map(fn($d) => Carbon::parse($d)->format('d M'));
        $chartOpd = $dates->map(fn($d) => $opdRevenues->get($d, 0));
        $chartPharmacy = $dates->map(fn($d) => $pharmacyRevenues->get($d, 0));
        $chartTotal = $dates->map(fn($d) => $opdRevenues->get($d, 0) + $pharmacyRevenues->get($d, 0));

        $revenueChart = [
            'labels' => $chartLabels,
            'datasets' => [
                [
                    'label' => 'OPD Revenue',
                    'data' => $chartOpd,
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                ],
                [
                    'label' => 'Pharmacy Revenue',
                    'data' => $chartPharmacy,
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                    'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                ],
                [
                    'label' => 'Total Revenue',
                    'data' => $chartTotal,
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                    'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                ],
            ],
        ];

        return view('admin.revenue-history', [
            'revenues' => $paginatedRevenues,
            'revenue_chart' => $revenueChart,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
        ]);
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