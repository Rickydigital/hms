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
        // Pending = Not yet issued
        $pending = VisitMedicineOrder::with(['visit.patient', 'medicine'])
            ->where('is_issued', false)
            ->latest()
            ->get();

        // Issued but not paid = Ready for collection after payment
        $readyForCollection = VisitMedicineOrder::with(['visit.patient', 'medicine', 'issuedBy'])
            ->where('is_issued', true)
            ->where('is_paid', false)
            ->latest()
            ->get();

        // Issued & Paid = Given to patient
        $givenToday = VisitMedicineOrder::with(['visit.patient', 'medicine', 'issuedBy'])
            ->where('is_issued', true)
            ->where('is_paid', true)
            ->whereDate('paid_at', today())
            ->latest()
            ->take(20)
            ->get();

        return view('pharmacy.index', compact('pending', 'readyForCollection', 'givenToday'));
    }

    public function history(Request $request)
    {
        $query = PharmacyIssue::with([
            'order.visit.patient',
            'medicine',
            'issuedBy'
        ])->latest('issued_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order.visit.patient', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%");
            })->orWhereHas('medicine', function($q) use ($search) {
                $q->where('medicine_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('issued_at', $request->date);
        }

        $issues = $query->paginate(25)->withQueryString();

        return view('pharmacy.history', compact('issues'));
    }

    // 1. Pharmacist ISSUES medicine (before payment)
    public function issue(Request $request, VisitMedicineOrder $order)
    {
        $request->validate([
            'quantity_issued' => 'required|integer|min:1',
        ]);

        $medicine = $order->medicine;
        $qtyNeeded = $request->quantity_issued;

        $batches = MedicineBatch::available()
            ->where('medicine_id', $medicine->id)
            ->orderBy('expiry_date')
            ->get();

        if ($batches->sum('current_stock') < $qtyNeeded) {
            return back()->withErrors([
                'quantity_issued' => "Only {$batches->sum('current_stock')} in stock for {$medicine->medicine_name}!"
            ]);
        }

        $remaining = $qtyNeeded;

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;
            $take = min($remaining, $batch->current_stock);

            $batch->logStockChange(
                quantity: -$take,
                type: 'sale',
                reference: $order,
                remarks: 'Issued (awaiting payment) - Patient: ' . $order->visit->patient->name
            );

            PharmacyIssue::create([
                'visit_medicine_order_id' => $order->id,
                'medicine_id'             => $medicine->id,
                'batch_no'                 => $batch->batch_no,
                'expiry_date'              => $batch->expiry_date,
                'quantity_issued'          => $take,
                'unit_price'               => $medicine->price,
                'total_amount'             => $take * $medicine->price,
                'issued_by'                => Auth::id(),
                'issued_at'                => now(),
            ]);

            $remaining -= $take;
        }

        // Mark as issued (but NOT paid yet)
        $order->update([
            'is_issued' => true,
            'issued_by' => Auth::id(),
            'issued_at' => now(),
            // is_paid remains false
        ]);

        return back()->with('success', "Medicine issued. Patient must pay at billing before collecting.");
    }

    // 2. FINAL HANDOVER: After payment, pharmacist gives medicine
    public function handover(VisitMedicineOrder $order)
    {
        if (!$order->is_issued) {
            return back()->withErrors('Medicine not issued yet!');
        }

        if (!$order->is_paid) {
            return back()->withErrors('Payment not received yet! Patient must pay first.');
        }

        // Optional: Add a "handed over" flag or log
        // For now, just mark as fully completed
        $order->update([
            'handed_over_at' => now(),
            'handed_over_by' => Auth::id(),
        ]);

        return back()->with('success', "Medicine handed over to patient successfully!");
    }
}