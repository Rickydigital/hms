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
use Illuminate\Support\Facades\Log;

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
        // Lab still needs completion (ignore already paid completed ones)
        $q->whereHas('labOrders', function ($sq) {
            $sq->where('is_completed', false);
            // Note: We do NOT care about payment here — payment belongs to Pending
        })

        // Medicine still needs to be issued
        ->orWhereHas('medicineOrders', function ($sq) {
            $sq->where('is_issued', false);
        })

        // Injection still needs to be given
        ->orWhereHas('injectionOrders', function ($sq) {
            $sq->where('is_given', false);
        })

        // Bed still occupied
        ->orWhereHas('bedAdmission', function ($sq) {
            $sq->where('is_discharged', false);
        });
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
    // === NO REGISTRATION OR CONSULTATION FEES ANYMORE ===
    // These are completely removed from billing

    // 1. Medicines: Only issued ones, with correct issued quantity
    // 1. Medicines: Only issued AND unpaid ones
$medicines = $visit->medicineOrders()
    ->where('is_issued', true)
    ->where('is_paid', false)  // ← CRITICAL: Only unpaid
    ->with(['medicine', 'pharmacyIssues'])
    ->get();

    // 2. Lab Tests: Only unpaid ones
    $labTests = $visit->labOrders()
        ->where('is_paid', false)
        ->with('test')
        ->get();

    // 3. Injections: Only given ones
    $injections = $visit->injectionOrders()
        ->where('is_given', true)
        ->with('medicine')
        ->get();

    // 4. Bed/Ward charges
    $bedAdmission = $visit->bedAdmission()->where('is_discharged', true)->first();
    $bedCharges = $bedAdmission?->bed_charges ?? 0;
    $bedDays    = $bedAdmission?->total_days ?? 0;

    // We no longer need to check for receipt to show reg/consult
    // $hasReceipt = $visit->receipt()->exists();
    // $showRegConsult = !$hasReceipt;

    // Force false - no reg/consult fees will ever be shown or added
    $showRegConsult = false;

    // === CALCULATE GRAND TOTAL - ONLY ACTUAL SERVICES ===
    $grandTotal = 0;

    // Medicines (using actual issued quantity)
    $grandTotal += $medicines->sum(function ($order) {
        $issuedQty = $order->pharmacyIssues->sum('quantity_issued') ?? 1;
        return $issuedQty * $order->medicine->price;
    });

    // Unpaid lab tests
    $grandTotal += $labTests->sum(fn($t) => $t->test->price);

    // Injections
    $grandTotal += $injections->sum(fn($i) => $i->medicine->price);

    // Bed charges
    $grandTotal += $bedCharges;

    // === DETERMINE IF GENERATE BUTTON SHOULD BE SHOWN ===
    $hasUnpaidItems = $medicines->isNotEmpty()
        || $labTests->isNotEmpty()
        || $injections->isNotEmpty()
        || $bedCharges > 0;

    $showGenerateButton = $hasUnpaidItems && $grandTotal > 0;

    // === SIDEBAR DATA (Pending, In Progress, Receipts) ===
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

    // === RETURN VIEW WITH CLEANED VARIABLES ===
    return view('billing.index', compact(
        'visit',
        'medicines',
        'labTests',
        'injections',
        'bedCharges',
        'bedDays',
        'grandTotal',
        'showGenerateButton',
        'pendingVisits',
        'pendingCount',
        'inProgressVisits',
        'receipts'
        // 'regFee', 'consultFee', 'showRegConsult' → removed entirely
    ));
}

public function generateReceipt(Request $request, Visit $visit)
    {

        
        Log::info("BillingController@generateReceipt - Attempting to generate receipt for visit ID: {$visit->id}");

        $view = $this->calculateBill($visit);
        $data = $view->getData();

        Log::info("BillingController@generateReceipt - Grand Total: {$data['grandTotal']}");

        $recentPayment = $visit->payments()
    ->where('amount', $data['grandTotal'])
    ->where('paid_at', '>', now()->subSeconds(10))
    ->exists();

if ($recentPayment) {
    return redirect()->route('billing.index')
        ->with('warning', 'Receipt already generated recently. Avoiding duplicate.');
}
        if ($data['grandTotal'] <= 0) {
            Log::warning("BillingController@generateReceipt - No amount to bill for visit {$visit->id}");
            return back()->with('error', 'No pending amount to generate receipt.');
        }

        try {
            // Generate receipt number
            $year = now()->format('Y');
            $count = Receipt::whereYear('generated_at', $year)->count() + 1;
            $receiptNo = "RCPT{$year}-" . str_pad($count, 6, '0', STR_PAD_LEFT);

            Log::info("BillingController@generateReceipt - Creating receipt #{$receiptNo}");

            $receipt = Receipt::create([
                'visit_id'     => $visit->id,
                'receipt_no'   => $receiptNo,
                'grand_total'  => $data['grandTotal'],
                'generated_by' => Auth::id(),
                'generated_at' => now(),
            ]);

            Log:: info("Receipt created ID: {$receipt->id}");

            // Create payment record
            $payment = Payment::create([
                'visit_id'       => $visit->id,
                'amount'         => $data['grandTotal'],
                'type'           => 'bill_payment',
                'payment_method' => 'cash',
                'received_by'    => Auth::id(),
                'paid_at'        => now(),
            ]);

            Log::info("BillingController@generateReceipt - Payment created ID: {$payment->id}");

            // Save payment details
            $details = [];

            // Save payment details - Medicines (correct quantity)
foreach ($data['medicines'] as $order) {
    $issuedQty = $order->pharmacyIssues->sum('quantity_issued') ?? 1;
    $price = $order->medicine->price;
    $total = $issuedQty * $price;

    $details[] = [
        'item_type'   => 'medicine',
        'item_name'   => $order->medicine->medicine_name,
        'quantity'    => $issuedQty,
        'unit_price'  => $price,
        'total_price' => $total,
    ];

    // Mark order as paid
    $order->update([
        'is_paid' => true,
        'paid_at' => now(),
        'paid_by' => Auth::id(),
    ]);
}

            foreach ($data['labTests'] as $test) {
                $details[] = [
                    'item_type'   => 'lab_test',
                    'item_name'   => $test->test->test_name . ' (Lab Test)',
                    'quantity'    => 1,
                    'unit_price'  => $test->test->price,
                    'total_price' => $test->test->price,
                ];

                $test->update([
                    'is_paid' => true,
                    'paid_at' => now(),
                    'paid_by' => Auth::id(),
                ]);
            }

            foreach ($details as $detail) {
                PaymentDetail::create(array_merge($detail, ['payment_id' => $payment->id]));
            }

            Log::info("BillingController@generateReceipt - Receipt #{$receiptNo} and payment recorded successfully for visit {$visit->id}");

            return redirect()->route('billing.index')->with('success', "Receipt {$receiptNo} generated and payment recorded successfully!");

        } catch (\Exception $e) {
            Log::error("BillingController@generateReceipt - ERROR for visit {$visit->id}: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->with('error', 'Failed to generate receipt. Check logs.');
        }
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

    if ($data['grandTotal'] <= 0) {
        return back()->with('error', 'No pending amount to record payment.');
    }

    try {
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

        // Registration & Consultation (only if shown)
        if ($data['showRegConsult'] ?? false) {
            if ($data['regFee'] > 0) {
                $details[] = [
                    'item_type'   => 'registration',
                    'item_name'   => 'Registration Fee',
                    'quantity'    => 1,
                    'unit_price'  => $data['regFee'],
                    'total_price' => $data['regFee'],
                ];
            }
            if ($data['consultFee'] > 0) {
                $details[] = [
                    'item_type'   => 'consultation',
                    'item_name'   => 'Doctor Consultation - Dr. ' . ($visit->doctor->name ?? 'Unknown'),
                    'quantity'    => 1,
                    'unit_price'  => $data['consultFee'],
                    'total_price' => $data['consultFee'],
                ];
            }
        }

        // Medicines - Use pharmacy_issues for correct quantity
        // Medicines - Use pharmacy_issues for correct quantity
foreach ($data['medicines'] as $order) {
    $issuedQty = $order->pharmacyIssues->sum('quantity_issued') ?? 1;
    $price = $order->medicine->price;
    $total = $issuedQty * $price;

    $details[] = [
        'item_type'   => 'medicine',
        'item_name'   => $order->medicine->medicine_name,
        'quantity'    => $issuedQty,
        'unit_price'  => $price,
        'total_price' => $total,
    ];

    // Mark as paid
    $order->update([
        'is_paid' => true,
        'paid_at' => now(),
        'paid_by' => Auth::id(),
    ]);
}

        // Lab Tests
        foreach ($data['labTests'] as $test) {
            $details[] = [
                'item_type'   => 'lab_test',
                'item_name'   => $test->test->test_name . ' (Lab Test)',
                'quantity'    => 1,
                'unit_price'  => $test->test->price,
                'total_price' => $test->test->price,
            ];

            $test->update([
                'is_paid' => true,
                'paid_at' => now(),
                'paid_by' => Auth::id(),
            ]);
        }

        // Injections
        foreach ($data['injections'] as $inj) {
            $details[] = [
                'item_type'   => 'injection',
                'item_name'   => $inj->medicine->medicine_name . ' (Injection)',
                'quantity'    => 1,
                'unit_price'  => $inj->medicine->price,
                'total_price' => $inj->medicine->price,
            ];
        }

        // Bed charges
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
            PaymentDetail::create(array_merge($detail, ['payment_id' => $payment->id]));
        }

        return back()->with('success', "Payment of Tsh " . number_format($request->amount, 0) . " recorded successfully!");

    } catch (\Exception $e) {
        Log::error("recordPayment ERROR: " . $e->getMessage());
        Log::error($e->getTraceAsString());
        return back()->with('error', 'Payment failed. Please try again.');
    }
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