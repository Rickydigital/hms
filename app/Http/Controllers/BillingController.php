<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Visit;
use App\Models\VisitLabOrder;
use App\Models\VisitMedicineOrder;
use App\Models\Receipt;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function index()
{
    // PENDING BILLS: Visits with ANY unpaid services (lab or medicine)
    $pendingVisits = Visit::with(['patient', 'doctor'])
        ->where(function ($q) {
            // Has lab orders that are not paid
            $q->whereHas('labOrders', function ($sq) {
                $sq->where('is_paid', false);
            })
            // OR has medicine orders that are not issued OR not paid
            ->orWhereHas('medicineOrders', function ($sq) {
                $sq->where('is_issued', false)
                   ->orWhere('is_paid', false);
            })
            // OR has unpaid injections (if you add payment flag later)
            ->orWhereHas('injectionOrders')
            ->orWhereHas('bedAdmission');
        })
        ->latest('visit_date')
        ->paginate(20);

    // IN PROGRESS: Visits with services but not fully completed/paid
    $inProgressVisits = Visit::with(['patient', 'doctor'])
        ->where(function ($q) {
            $q->whereHas('labOrders', fn($sq) => $sq->where('is_completed', false)->where('is_paid', true))
              ->orWhereHas('medicineOrders', fn($sq) => $sq->where('is_issued', false))
              ->orWhereHas('injectionOrders', fn($sq) => $sq->where('is_given', false))
              ->orWhereHas('bedAdmission', fn($sq) => $sq->where('is_discharged', false));
        })
        ->latest('visit_date')
        ->get();

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

    public function showBill(Visit $visit)
    {
        return $this->calculateBill($visit);
    }

    private function calculateBill($visit)
{
    $regFee     = Setting::get('registration_fee', 0);
    $consultFee = $visit->doctor->consultation_fee ?? Setting::get('consultation_fee', 0);

    // 1. Medicines: Only issued ones
    $medicines = $visit->medicineOrders()
        ->where('is_issued', true)
        ->with('medicine')
        ->get();

    // 2. Lab Tests: Only UNPAID ones
    $labTests = $visit->labOrders()
        ->where('is_paid', false)
        ->with('test')
        ->get();

    // 3. Injections: Only if not paid (add is_paid flag later if needed)
    $injections = $visit->injectionOrders()->where('is_given', true)->get();

    $bedAdmission = $visit->bedAdmission()->where('is_discharged', true)->first();
    $bedCharges = 0;
    $bedDays    = 0;
    if ($bedAdmission) {
        $bedDays    = $bedAdmission->total_days;
        $bedCharges = $bedAdmission->bed_charges;
    }

    // GRAND TOTAL: Only unpaid items + one-time fees (only if no receipt yet)
    $hasReceipt = $visit->receipt()->exists();

    $grandTotal = 0;

    // Only add registration + consultation if NO receipt yet
    if (!$hasReceipt) {
        $grandTotal += $regFee + $consultFee;
    }

    // Add unpaid medicines
    $grandTotal += $medicines->sum(function ($order) {
        return ($order->quantity_issued ?? 1) * $order->medicine->price;
    });

    // Add unpaid lab tests
    $grandTotal += $labTests->sum(fn($t) => $t->test->price);

    // Add injections
    $grandTotal += $injections->sum(fn($i) => $i->medicine->price);

    // Add bed charges
    $grandTotal += $bedCharges;

    $receiptGenerated = $hasReceipt;

    // Sidebar data (keep your existing logic)
    $pendingVisits = Visit::with(['patient', 'doctor'])
        ->where(function ($q) {
            $q->whereHas('labOrders', fn($sq) => $sq->where('is_paid', false))
              ->orWhereHas('medicineOrders', fn($sq) => $sq->where('is_issued', false)->orWhere('is_paid', false))
              ->orWhereHas('injectionOrders')
              ->orWhereHas('bedAdmission');
        })
        ->latest('visit_date')
        ->paginate(20);

    $pendingCount = $pendingVisits->total();

    $inProgressVisits = Visit::with(['patient', 'doctor'])
        ->where(function ($q) {
            $q->whereHas('labOrders', fn($sq) => $sq->where('is_completed', false)->where('is_paid', true))
              ->orWhereHas('medicineOrders', fn($sq) => $sq->where('is_issued', false))
              ->orWhereHas('injectionOrders', fn($sq) => $sq->where('is_given', false))
              ->orWhereHas('bedAdmission', fn($sq) => $sq->where('is_discharged', false));
        })
        ->latest('visit_date')
        ->get();

    $receipts = Receipt::with(['visit.patient', 'visit.payments'])
        ->latest('generated_at')
        ->get();

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
        'pendingVisits',
        'pendingCount',
        'inProgressVisits',
        'receipts'
    ));
}

    public function generateReceipt(Request $request, Visit $visit)
{
    // Only allow generating receipt ONCE (final bill)
    if ($visit->receipt()->exists()) {
        return back()->with('error', 'Official final receipt already generated!');
    }

    $view = $this->calculateBill($visit);
    $data = $view->getData();

    if ($data['grandTotal'] <= 0) {
        return back()->with('error', 'No pending amount to generate receipt for.');
    }

    // Create FINAL receipt
    Receipt::create([
        'visit_id'     => $visit->id,
        'grand_total'  => $data['grandTotal'],
        'generated_by' => Auth::id(),
        'generated_at' => now(),
    ]);

    // Record FULL payment for remaining amount
    $payment = Payment::create([
        'visit_id'       => $visit->id,
        'amount'         => $data['grandTotal'],
        'type'           => 'final_payment',
        'payment_method' => 'cash',
        'received_by'    => Auth::id(),
        'paid_at'        => now(),
    ]);

    // Save details (only current unpaid items)
    $details = [];

    foreach ($data['medicines'] as $order) {
        $details[] = [
            'item_type'   => 'medicine',
            'item_name'   => $order->medicine->medicine_name,
            'quantity'    => $order->quantity_issued ?? 1,
            'unit_price'  => $order->medicine->price,
            'total_price' => ($order->quantity_issued ?? 1) * $order->medicine->price,
        ];
        $order->update(['is_paid' => true, 'paid_at' => now(), 'paid_by' => Auth::id()]);
    }

    foreach ($data['labTests'] as $test) {
        $details[] = [
            'item_type'   => 'lab_test',
            'item_name'   => $test->test->test_name . ' (Lab Test)',
            'quantity'    => 1,
            'unit_price'  => $test->test->price,
            'total_price' => $test->test->price,
        ];
        $test->update(['is_paid' => true, 'paid_at' => now(), 'paid_by' => Auth::id()]);
    }

    foreach ($details as $detail) {
        PaymentDetail::create(array_merge($detail, ['payment_id' => $payment->id]));
    }

    return back()->with('success', 'Final receipt generated and payment recorded!');
}

    public function recordPayment(Request $request, Visit $visit)
    {
        $request->validate([
            'amount'          => 'required|numeric|min:0.01',
            'payment_method'  => 'required|in:cash,mpesa,card,bank_transfer,insurance',
            'transaction_id'  => 'nullable|string|max:100',
        ]);

        $view = $this->calculateBill($visit);
        $data = $view->getData();

        $payment = Payment::create([
            'visit_id'        => $visit->id,
            'amount'          => $request->amount,
            'type'            => 'bill_payment',
            'payment_method'  => $request->payment_method,
            'transaction_id'  => $request->transaction_id,
            'received_by'     => Auth::id(),
            'paid_at'         => now(),
        ]);

        $details = [];

        if ($data['regFee'] > 0) {
            $details[] = ['item_type' => 'registration', 'item_name' => 'Registration Fee', 'quantity' => 1, 'unit_price' => $data['regFee'], 'total_price' => $data['regFee']];
        }

        if ($data['consultFee'] > 0) {
            $details[] = ['item_type' => 'consultation', 'item_name' => 'Doctor Consultation - Dr. ' . ($visit->doctor->name ?? 'Unknown'), 'quantity' => 1, 'unit_price' => $data['consultFee'], 'total_price' => $data['consultFee']];
        }

        foreach ($data['medicines'] as $item) {
            $details[] = ['item_type' => 'medicine', 'item_name' => $item->medicine->medicine_name, 'quantity' => $item->quantity_issued, 'unit_price' => $item->unit_price, 'total_price' => $item->total_amount];

            VisitMedicineOrder::where('visit_id', $visit->id)
                ->where('medicine_id', $item->medicine->id)
                ->update(['is_paid' => true, 'paid_at' => now(), 'paid_by' => Auth::id()]);
        }

        foreach ($data['labTests'] as $test) {
            $details[] = ['item_type' => 'lab_test', 'item_name' => $test->test->test_name . ' (Lab Test)', 'quantity' => 1, 'unit_price' => $test->test->price, 'total_price' => $test->test->price];

            $test->update(['is_paid' => true, 'paid_at' => now(), 'paid_by' => Auth::id()]);
        }

        foreach ($data['injections'] as $inj) {
            $details[] = ['item_type' => 'injection', 'item_name' => $inj->medicine->medicine_name . ' (Injection)', 'quantity' => 1, 'unit_price' => $inj->medicine->price, 'total_price' => $inj->medicine->price];
        }

        if ($data['bedCharges'] > 0) {
            $details[] = ['item_type' => 'bed_charges', 'item_name' => 'Bed/Ward Charges (' . $data['bedDays'] . ' days)', 'quantity' => 1, 'unit_price' => $data['bedCharges'], 'total_price' => $data['bedCharges']];
        }

        foreach ($details as $detail) {
            PaymentDetail::create(array_merge($detail, ['payment_id' => $payment->id]));
        }

        return back()->with('success', "Payment of Tsh " . number_format($request->amount, 0) . " recorded successfully!");
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
                'amount'      => (int) $payment->amount,
                'method'      => ucfirst($payment->payment_method),
                'received_by' => $payment->receivedBy?->name ?? 'Unknown',
                'details'     => $payment->details->map(fn($d) => [
                    'item_name'   => $d->item_name,
                    'quantity'    => (int) $d->quantity,
                    'unit_price'  => number_format((float) $d->unit_price, 0),
                    'total_price' => number_format((float) $d->total_price, 0),
                    'line_total'  => number_format((int) $d->quantity * (float) $d->unit_price, 0),
                ])->toArray()
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