<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Visit;
use App\Models\Receipt;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    // Main page: shows pending list + search
    public function index()
{
    // 1. Pending Bills (ready for receipt)
    $pendingVisits = Visit::with(['patient', 'doctor'])
        ->where('all_services_completed', true)
        ->whereDoesntHave('receipt')
        ->latest('visit_date')
        ->paginate(20);

    // 2. In Progress (not completed)
    $inProgressVisits = Visit::with(['patient', 'doctor'])
    ->where(function ($query) {
        $query->where('all_services_completed', false)
              ->orWhereNull('all_services_completed'); // in case it's null
    })
    ->whereDoesntHave('receipt') // don't show finalized ones
    ->latest('visit_date')
    ->get();

    // 3. All receipts with payment status
    $receipts = Receipt::with(['visit.patient', 'visit.payments'])
        ->latest('generated_at')
        ->get();

    $pendingCount = $pendingVisits->total();

    return view('billing.index', compact(
        'pendingVisits', 'pendingCount',
        'inProgressVisits',
        'receipts'
    ));
}

    // Search by patient ID or name
    public function search(Request $request)
    {
        $q = trim($request->q);

        $visit = Visit::with([
                'patient',
                'doctor',
                'medicineIssues.medicine',
                'labOrders.test',
                'injectionOrders.medicine',
                'bedAdmission.ward'
            ])
            ->where(function ($query) use ($q) {
                $query->whereHas('patient', function ($sq) use ($q) {
                    $sq->where('patient_id', 'like', "%$q%")
                       ->orWhere('name', 'like', "%$q%");
                })
                ->orWhere('id', $q);
            })
            ->firstOrFail();

        return $this->calculateBill($visit);
    }

    // Direct access from pending list: /billing/pending/5
    public function showBill(Visit $visit)
    {
        return $this->calculateBill($visit);
    }

  private function calculateBill($visit)
{
    if (!$visit->all_services_completed) {
        abort(403, 'Services not completed yet. Cannot generate bill.');
    }

    // === CALCULATE BILL ITEMS ===
    $regFee     = Setting::get('registration_fee', 0);
    $consultFee = $visit->doctor->consultation_fee ?? Setting::get('consultation_fee', 0);

    $medicines  = $visit->medicineIssues()
                        ->whereNotNull('pharmacy_issues.issued_at')
                        ->get();

    $labTests   = $visit->labOrders()->where('is_completed', true)->get();
    $injections = $visit->injectionOrders()->where('is_given', true)->get();

    $bedAdmission = $visit->bedAdmission()->where('is_discharged', true)->first();
    $bedCharges = 0; 
    $bedDays    = 0;
    if ($bedAdmission) {
        $bedDays    = $bedAdmission->total_days;
        $bedCharges = $bedAdmission->bed_charges;
    }

    $grandTotal = $regFee + $consultFee
                + $medicines->sum('total_amount')
                + $labTests->sum(fn($t) => $t->test->price)
                + $injections->sum(fn($i) => $i->medicine->price)
                + $bedCharges;

    $receiptGenerated = $visit->receipt()->exists();

    // === FIX: ADD ALL MISSING VARIABLES THAT BLADE EXPECTS ===
    $pendingVisits = Visit::with(['patient', 'doctor'])
        ->where('all_services_completed', true)
        ->whereDoesntHave('receipt')
        ->latest('visit_date')
        ->paginate(20);

    $pendingCount = $pendingVisits->total();

    $inProgressVisits = Visit::with(['patient', 'doctor', 'medicineIssues', 'labOrders', 'injectionOrders', 'bedAdmission'])
        ->where('all_services_completed', false)
        ->orWhere(function ($q) {
            $q->where('all_services_completed', true)
              ->whereHas('receipt');
        })
        ->latest('visit_date')
        ->get();

    $receipts = Receipt::with(['visit.patient', 'visit.payments'])
        ->latest('generated_at')
        ->get();

    // === RETURN VIEW WITH EVERYTHING ===
    return view('billing.index', compact(
        'visit',
        'regFee',
        'consultFee',
        'medicines',
        'labTests',
        'injections',
        'bedCharges',
        'bedDays',
        'grandTotal',
        'receiptGenerated',
        'pendingVisits',      // ← ADDED
        'pendingCount',       // ← ADDED
        'inProgressVisits',   // ← ALREADY HAD
        'receipts'            // ← ALREADY HAD
    ));
}

   public function generateReceipt(Request $request, Visit $visit)
{
    // Temporarily capture the view data without rendering it
    $view = $this->calculateBill($visit);
    $data = $view->getData(); // This is an array

    Receipt::create([
        'visit_id'           => $visit->id,
        'total_registration' => $data['regFee'],
        'total_final'        => $data['grandTotal'],
        'grand_total'        => $data['grandTotal'],
        'generated_by'       => Auth::id(),
        'generated_at'       => now(),
    ]);

    return back()->with('success', "Receipt generated successfully!");
}

public function recordPayment(Request $request, Visit $visit)
{
    $request->validate([
        'amount'          => 'required|numeric|min:0.01',
        'payment_method'  => 'required|in:cash,mpesa,card,bank_transfer,insurance',
        'transaction_id'  => 'nullable|string|max:100',
    ]);

    // Reuse the exact same calculation logic as receipt
    $view = $this->calculateBill($visit);
    $data = $view->getData(); // This gives us all calculated values

    // Create the main payment record
    $payment = Payment::create([
    'visit_id'        => $visit->id,
    'amount'          => $request->amount,
    'type'            => 'bill_payment',   // ← Clean & safe
    'payment_method'  => $request->payment_method,
    'transaction_id'  => $request->transaction_id,
    'received_by'     => Auth::id(),
    'paid_at'         => now(),
]);

    // === RECORD EVERY SINGLE ITEM IN PAYMENT DETAILS ===
    $details = [];

    // 1. Registration Fee
    if ($data['regFee'] > 0) {
        $details[] = [
            'item_type'   => 'registration',
            'item_name'   => 'Registration Fee',
            'quantity'    => 1,
            'unit_price'  => $data['regFee'],
            'total_price' => $data['regFee'],
        ];
    }

    // 2. Consultation Fee
    if ($data['consultFee'] > 0) {
        $details[] = [
            'item_type'   => 'consultation',
            'item_name'   => 'Doctor Consultation - Dr. ' . ($visit->doctor->name ?? 'Unknown'),
            'quantity'    => 1,
            'unit_price'  => $data['consultFee'],
            'total_price' => $data['consultFee'],
        ];
    }

    // 3. Medicines (Pharmacy Issues)
    foreach ($data['medicines'] as $item) {
        $details[] = [
            'item_type'   => 'medicine',
            'item_name'   => $item->medicine->medicine_name,
            'quantity'    => $item->quantity_issued,
            'unit_price'  => $item->unit_price,
            'total_price' => $item->total_amount,
        ];
    }

    // 4. Lab Tests
    foreach ($data['labTests'] as $test) {
        $details[] = [
            'item_type'   => 'lab_test',
            'item_name'   => $test->test->test_name,
            'quantity'    => 1,
            'unit_price'  => $test->test->price,
            'total_price' => $test->test->price,
        ];
    }

    // 5. Injections
    foreach ($data['injections'] as $inj) {
        $details[] = [
            'item_type'   => 'injection',
            'item_name'   => $inj->medicine->medicine_name . ' (Injection)',
            'quantity'    => 1,
            'unit_price'  => $inj->medicine->price,
            'total_price' => $inj->medicine->price,
        ];
    }

    // 6. Bed/Ward Charges
    if ($data['bedCharges'] > 0) {
        $details[] = [
            'item_type'   => 'bed_charges',
            'item_name'   => 'Bed/Ward Charges (' . $data['bedDays'] . ' days)',
            'quantity'    => 1,
            'unit_price'  => $data['bedCharges'],
            'total_price' => $data['bedCharges'],
        ];
    }

    // Save all details
    foreach ($details as $detail) {
        PaymentDetail::create(array_merge($detail, [
            'payment_id' => $payment->id
        ]));
    }

    return back()->with('success', "Payment of Tsh " . number_format($request->amount, 0) . " recorded successfully with full details!");
}

public function paymentDetails(Visit $visit)
{
    $receipt = $visit->receipt;
    $payments = $visit->payments()->with(['details', 'receivedBy'])->latest()->get();

    $totalPaid = $payments->sum('amount');
    $grandTotal = $receipt?->grand_total ?? 0;
    $balance = $grandTotal - $totalPaid;

    $paymentData = $payments->map(function ($payment) {
        return [
            'date'        => $payment->paid_at->format('d M Y H:i'),
            'amount'      => (int) $payment->amount, // Force integer
            'method'      => ucfirst($payment->payment_method),
            'received_by' => $payment->receivedBy?->name ?? 'Unknown',
            'details'     => $payment->details->map(function ($detail) {
                // Force numbers — this fixes NaN forever
                $unitPrice  = (float) $detail->unit_price;
                $totalPrice = (float) $detail->total_price;
                $quantity   = (int) $detail->quantity;

                return [
                    'item_name'   => $detail->item_name,
                    'quantity'    => $quantity,
                    'unit_price'  => number_format($unitPrice, 0),
                    'total_price' => number_format($totalPrice, 0),
                    'line_total'  => number_format($quantity * $unitPrice, 0),
                ];
            })->toArray()
        ];
    })->toArray();

    return response()->json([
        'patient'  => $visit->patient->name,
        'total'    => (int) $grandTotal,
        'paid'     => (int) $totalPaid,
        'balance'  => (int) $balance,
        'payments' => $paymentData
    ]);
}
}