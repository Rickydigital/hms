<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PharmacySaleItem;
use App\Models\PharmacyIssue;
use App\Models\MedicineMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ProfitReportController extends Controller
{
    public function index()
    {
        $profitData = $this->getProfitReport();
        
        // PAGINATE COLLECTION - HII NDIO MISTARI 2 TU
        $perPage = request('per_page', 25);
        $report = $this->paginateCollection($profitData['report'], $perPage);

        return view('admin.profit-report', ['report' => $report, 'summary' => $profitData['summary']]);
    }

    /**
     * Paginate a collection
     */
    private function paginateCollection($items, $perPage, $page = null)
    {
        $page = $page ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof \Illuminate\Support\Collection ? $items : collect($items);

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }

    private function getProfitReport()
    {
        // 1. OTC Sales
        $otc = PharmacySaleItem::select(
                'pharmacy_sale_items.medicine_id',
                DB::raw('SUM(pharmacy_sale_items.quantity) as total_qty'),
                DB::raw('SUM(pharmacy_sale_items.quantity * pharmacy_sale_items.unit_price) as total_sales'),
                DB::raw('SUM(pharmacy_sale_items.quantity * COALESCE(batch.purchase_price, medicine.purchase_price, 0)) as total_cost')
            )
            ->join('pharmacy_sales', 'pharmacy_sale_items.pharmacy_sale_id', '=', 'pharmacy_sales.id')
            ->leftJoin('medicine_batches as batch', 'pharmacy_sale_items.batch_id', '=', 'batch.id')
            ->join('medicines_master as medicine', 'pharmacy_sale_items.medicine_id', '=', 'medicine.id')
            ->groupBy('pharmacy_sale_items.medicine_id')
            ->get();

        // 2. Prescription Sales
        $prescription = PharmacyIssue::select(
                'pharmacy_issues.medicine_id',
                DB::raw('SUM(pharmacy_issues.quantity_issued) as total_qty'),
                DB::raw('SUM(pharmacy_issues.quantity_issued * pharmacy_issues.unit_price) as total_sales'),
                DB::raw('SUM(pharmacy_issues.quantity_issued * medicine.purchase_price) as total_cost')
            )
            ->join('medicines_master as medicine', 'pharmacy_issues.medicine_id', '=', 'medicine.id')
            ->groupBy('pharmacy_issues.medicine_id')
            ->get();

        // Merge both collections
        $merged = collect();

        foreach ([$otc, $prescription] as $collection) {
            foreach ($collection as $row) {
                $key = $row->medicine_id;

                if ($merged->has($key)) {
                    $merged[$key]->total_qty    += $row->total_qty;
                    $merged[$key]->total_sales  += $row->total_sales;
                    $merged[$key]->total_cost   += $row->total_cost;
                } else {
                    $merged[$key] = $row;
                }
            }
        }

        // Build final report
        $report = $merged->map(function ($item) {
            $medicine = MedicineMaster::find($item->medicine_id);
            $profit   = $item->total_sales - $item->total_cost;

            return [
                'medicine_name'  => $medicine?->medicine_name ?? 'Unknown',
                'generic_name'   => $medicine?->generic_name,
                'total_qty'      => (int) $item->total_qty,
                'total_cost'     => round($item->total_cost, 2),
                'total_sales'    => round($item->total_sales, 2),
                'profit'         => round($profit, 2),
                'profit_margin'  => $item->total_cost > 0 ? round(($profit / $item->total_cost) * 100, 1) : 0,
            ];
        })->sortByDesc('profit')->values();

        $summary = [
            'total_sales'  => $report->sum('total_sales'),
            'total_cost'   => $report->sum('total_cost'),
            'total_profit' => $report->sum('profit'),
        ];

        return compact('report', 'summary');
    }
}