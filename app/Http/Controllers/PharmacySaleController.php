<?php

namespace App\Http\Controllers;

use App\Models\MedicineMaster;
use App\Models\MedicineBatch;
use App\Models\PharmacySale;
use App\Models\PharmacySaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class PharmacySaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:issue medicine');
    }

    public function create()
    {
        $medicines = MedicineMaster::active()->with('batches')->orderBy('medicine_name')->get();
        return view('pharmacy.sales.create', compact('medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines_master,id',
            'items.*.quantity' => 'required|integer|min:1',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        $sale = PharmacySale::create([
            'invoice_no' => PharmacySale::generateInvoiceNo(),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'total_amount' => 0, // temporary
            'amount_paid' => $request->amount_paid,
            'change_due' => 0,
            'remarks' => $request->remarks,
            'sold_by' => Auth::id(),
        ]);

        $total = 0;

        foreach ($request->items as $item) {
            $medicine = MedicineMaster::find($item['medicine_id']);
            $qty = (int) $item['quantity'];

            $batches = MedicineBatch::available()
                ->where('medicine_id', $medicine->id)
                ->orderBy('expiry_date')
                ->get();

            if ($batches->sum('current_stock') < $qty) {
                return back()->withErrors("Not enough stock for {$medicine->medicine_name}");
            }

            $remaining = $qty;
            foreach ($batches as $batch) {
                if ($remaining <= 0) break;
                $take = min($remaining, $batch->current_stock);

                // Reduce stock + log
                $batch->logStockChange(
                    quantity: -$take,
                    type: 'sale',
                    reference: $sale,
                    remarks: "OTC Sale - {$sale->invoice_no} | Customer: {$request->customer_name}"
                );

                PharmacySaleItem::create([
                    'pharmacy_sale_id' => $sale->id,
                    'medicine_id' => $medicine->id,
                    'batch_id' => $batch->id,
                    'batch_no' => $batch->batch_no,
                    'expiry_date' => $batch->expiry_date,
                    'quantity' => $take,
                    'unit_price' => $medicine->price,
                    'total_price' => $take * $medicine->price,
                ]);

                $total += $take * $medicine->price;
                $remaining -= $take;
            }
        }

        $change = $request->amount_paid - $total;
        $sale->update([
            'total_amount' => $total,
            'change_due' => $change > 0 ? $change : 0,
        ]);

        return redirect()->route('pharmacy.sales.receipt', $sale)
            ->with('success', 'Sale completed successfully!');
    }

    public function receipt(PharmacySale $sale)
    {
        $sale->load('items.medicine', 'soldBy');
        return view('pharmacy.sales.receipt', compact('sale'));
    }

    public function search(Request $request)
{
    if (!$request->filled('q')) {
        return response()->json([]);
    }

    $term = trim($request->q);

    $results = MedicineMaster::active()
        ->whereAny([
            'medicine_name',
            'generic_name',
            'strength',
            'dosage_form',
            'manufacturer'
        ], 'LIKE', "%{$term}%")
        ->withSum('batches as total_stock', 'current_stock')
        ->select('id', 'medicine_name', 'generic_name', 'strength', 'price')
        ->having('total_stock', '>', 0) // Only show medicines with stock
        ->orderBy('medicine_name')
        ->limit(30)
        ->get()
        ->map(function ($med) {
            $stock = (int) $med->total_stock;
            return [
                'id' => $med->id,
                'text' => $med->medicine_name
                    . ($med->generic_name ? " • {$med->generic_name}" : "")
                    . ($med->strength ? " {$med->strength}" : "")
                    . " — Stock: {$stock}",
                'price' => $med->price,
                'stock' => $stock,
            ];
        });

    return response()->json($results);
}

    public function history(Request $request)
    {
        $sales = PharmacySale::with(['soldBy', 'items.medicine'])
            ->latest()
            ->when($request->date, fn($q) => $q->whereDate('sold_at', $request->date))
            ->paginate(20);

        return view('pharmacy.sales.history', compact('sales'));
    }
}