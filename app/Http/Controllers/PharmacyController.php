<?php

namespace App\Http\Controllers;

use App\Models\VisitMedicineOrder;
use App\Models\PharmacyIssue;
use App\Models\MedicineBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class PharmacyController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->can('issue medicine')) {
                abort(403, 'Access denied. Only Pharmacy can issue medicines.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $pending = VisitMedicineOrder::with(['visit.patient', 'medicine'])
            ->where('is_issued', false)
            ->latest()
            ->get();

        $issuedToday = VisitMedicineOrder::with(['visit.patient', 'medicine', 'issuedBy'])
            ->where('is_issued', true)
            ->whereDate('issued_at', today())
            ->latest()
            ->take(20)
            ->get();

        return view('pharmacy.index', compact('pending', 'issuedToday'));
    }

    // PharmacyController.php → issue() method
public function issue(Request $request, VisitMedicineOrder $order)
{
    $request->validate([
        'quantity_issued' => 'required|integer|min:1',
    ]);

    $medicine = $order->medicine;
    $qtyNeeded = $request->quantity_issued;

    // Get available batches sorted by expiry (FEFO)
    $batches = MedicineBatch::available()
        ->where('medicine_id', $medicine->id)
        ->orderBy('expiry_date')
        ->get();

    if ($batches->sum('current_stock') < $qtyNeeded) {
        return back()->withErrors(['quantity_issued' => "Only {$batches->sum('current_stock')} in stock!"]);
    }

    $remaining = $qtyNeeded;

    foreach ($batches as $batch) {
        if ($remaining <= 0) break;

        $take = min($remaining, $batch->current_stock);

        // Reduce stock
        $batch->decrement('current_stock', $take);

        // Create stock log
        \App\Models\MedicineStockLog::create([
            'medicine_id' => $medicine->id,
            'batch_id' => $batch->id,
            'quantity' => -$take,
            'type' => 'sale',
            'reference_type' => 'App\Models\VisitMedicineOrder',
            'reference_id' => $order->id,
            'created_by' => Auth::id(),
        ]);

        // Create issue record
        PharmacyIssue::create([
            'visit_medicine_order_id' => $order->id,
            'medicine_id' => $medicine->id,
            'batch_no' => $batch->batch_no,
            'expiry_date' => $batch->expiry_date,
            'quantity_issued' => $take,
            'unit_price' => $medicine->price, // From master
            'total_amount' => $take * $medicine->price,
            'issued_by' => Auth::id(),
            'issued_at' => now(),
        ]);

        $remaining -= $take;
    }

    // Mark prescription as issued
    $order->update([
        'is_issued' => true,
        'issued_by' => Auth::id(),
        'issued_at' => now(),
    ]);

    return back()->with('success', "Issued {$qtyNeeded} × {$medicine->medicine_name}");
}
}