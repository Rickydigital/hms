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
        $pendingVisits = Visit::with(['patient', 'doctor'])
            ->where('all_services_completed', true)
            ->whereDoesntHave('receipt')
            ->latest('visit_date')
            ->paginate(20);

        $inProgressVisits = Visit::with(['patient', 'doctor'])
            ->where(function ($query) {
                $query->where('all_services_completed', false)
                      ->orWhereNull('all_services_completed');
            })
            ->whereDoesntHave('receipt')
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
        if (!$visit->all_services_completed) {
            abort(403, 'Services not completed yet. Cannot generate bill.');
        }

        $regFee     = Setting::get('registration_fee', 0);
        $consultFee = $visit->doctor->consultation_fee ?? Setting::get('consultation_fee', 0);

        // Only issued medicines (pharmacy has already given)
        $medicines = $visit->medicineIssues()
            ->whereNotNull('pharmacy_issues.issued_at')
            ->get();

        // ALL lab tests ordered by doctor (appears immediately!)
        $labTests = $visit->labOrders()->with('test')->get();

        // Only given injections
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
                    + $labTests->sum(fn($t) => $t->test->price)   // All ordered lab tests
                    + $injections->sum(fn($i) => $i->medicine->price)
                    + $bedCharges;

        $receiptGenerated = $visit->receipt()->exists();

        // Re-fetch for sidebar
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
        $view = $this->calculateBill($visit);
        $data = $view->getData();

        Receipt::create([
            'visit_id'           => $visit->id,
            'total_registration' => $data['regFee'],
            'total_final'        => $data['grandTotal'],
            'grand_total'        => $data['grandTotal'],
            'generated_by'       => Auth::id(),
            'generated_at'       => now(),
        ]);

        return back()->with('success', "Official receipt generated successfully!");
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

        // 3. Medicines – Only issued ones
        foreach ($data['medicines'] as $item) {
            $details[] = [
                'item_type'   => 'medicine',
                'item_name'   => $item->medicine->medicine_name,
                'quantity'    => $item->quantity_issued,
                'unit_price'  => $item->unit_price,
                'total_price' => $item->total_amount,
            ];

            VisitMedicineOrder::where('visit_id', $visit->id)
                ->where('medicine_id', $item->medicine->id)
                ->update([
                    'is_paid' => true,
                    'paid_at' => now(),
                    'paid_by' => Auth::id(),
                ]);
        }

        // 4. Lab Tests – ALL ordered tests (not just completed!)
        foreach ($data['labTests'] as $test) {
            $details[] = [
                'item_type'   => 'lab_test',
                'item_name'   => $test->test->test_name . ' (Lab Test)',
                'quantity'    => 1,
                'unit_price'  => $test->test->price,
                'total_price' => $test->test->price,
            ];

            // Mark as paid when payment is recorded
            $test->update([
                'is_paid' => true,
                'paid_at' => now(),
                'paid_by' => Auth::id(),
            ]);
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

        // 6. Bed Charges
        if ($data['bedCharges'] > 0) {
            $details[] = [
                'item_type'   => 'bed_charges',
                'item_name'   => 'Bed/Ward Charges (' . $data['bedDays'] . ' days)',
                'quantity'    => 1,
                'unit_price'  => $data['bedCharges'],
                'total_price' => $data['bedCharges'],
            ];
        }

        // Save payment details
        foreach ($details as $detail) {
            PaymentDetail::create(array_merge($detail, [
                'payment_id' => $payment->id
            ]));
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
                'details'     => $payment->details->map(function ($detail) {
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