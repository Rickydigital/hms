<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MedicinePurchase;
use App\Models\MedicineBatch;
use App\Models\MedicineMaster;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MedicinePurchaseController extends Controller
{
    public function index()
{
    $purchases = MedicinePurchase::with(['supplier', 'batches.medicine', 'receivedBy'])
        ->latest()
        ->paginate(15);

    return view('store.purchase.index', compact('purchases'));
}

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $medicines = MedicineMaster::active()->orderBy('medicine_name')->get();
        return view('store.purchase.create', compact('suppliers', 'medicines'));
    }


public function store(Request $request)
{
    $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'items.*.medicine_id' => 'required|exists:medicines_master,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.purchase_price' => 'required|numeric|min:0',
        'items.*.selling_price' => 'required|numeric', // comes from master
    ]);

    $nextNumber = MedicinePurchase::count() + 1;
    $invoiceNo = 'INV-' . date('Y') . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

    $purchase = MedicinePurchase::create([
        'invoice_no' => $invoiceNo,
        'invoice_date' => now(),
        'supplier_id' => $request->supplier_id,
        'total_amount' => collect($request->items)->sum(fn($i) => $i['purchase_price'] * $i['quantity']),
        'discount' => $request->discount ?? 0,
        'remarks' => $request->remarks,
        'received_by' => Auth::id(),
        'received_at' => now(),
    ]);

    foreach ($request->items as $item) {
        $medicine = MedicineMaster::find($item['medicine_id']);

        $batchNo = !empty($item['batch_no'] ?? '') 
            ? strtoupper($item['batch_no'])
            : strtoupper(substr($medicine->medicine_name, 0, 4)) . date('ym') . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $expiry = $item['expiry_date'] ?? now()->addYears(3)->format('Y-m-d');

        MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'purchase_id' => $purchase->id,
            'batch_no' => $batchNo,
            'expiry_date' => $expiry,
            'initial_quantity' => $item['quantity'],
            'current_stock' => $item['quantity'],
            'purchase_price' => $item['purchase_price'],
            // selling_price REMOVED from batch
            'received_date' => now(),
            'is_expired' => false,
        ]);
    }

    return redirect()->route('store.purchase.index')
        ->with('success', "Stock added successfully! Invoice: {$invoiceNo}");
}

}