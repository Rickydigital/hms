<?php

namespace App\Http\Controllers;

use App\Models\MedicineBatch;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Visit;
use App\Models\VisitLabOrder;
use App\Models\VisitMedicineOrder;
use App\Models\VisitProcedure; // ← PROCEDURE
use App\Models\Receipt;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    public function index()
    {
        // PENDING BILLS: Visits with ANY unpaid services (lab, medicine, procedures)
        $pendingVisits = Visit::with(['patient', 'doctor'])
            ->where(function ($q) {
                $q->whereHas('labOrders', fn($sq) => $sq->where('is_paid', false))
                  ->orWhereHas('medicineOrders', fn($sq) => $sq->where('is_issued', false)->orWhere('is_paid', false))
                  ->orWhereHas('injectionOrders')
                  ->orWhereHas('bedAdmission')
                  ->orWhereHas('procedures', fn($sq) => $sq->where('is_paid', false)); // PROCEDURE
            })
            ->latest('visit_date')
            ->paginate(20);

        // IN PROGRESS: Visits with services but not fully completed/paid
        $inProgressVisits = Visit::with(['patient', 'doctor'])
            ->where(function ($q) {
                $q->whereHas('labOrders', fn($sq) => $sq->where('is_completed', false)->where('is_paid', true))
                  ->orWhereHas('medicineOrders', fn($sq) => $sq->where('is_issued', false))
                  ->orWhereHas('injectionOrders', fn($sq) => $sq->where('is_given', false))
                  ->orWhereHas('bedAdmission', fn($sq) => $sq->where('is_discharged', false))
                  ->orWhereHas('procedures', fn($sq) => $sq->where('is_issued', false)); // PROCEDURE
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

    public function removeItem(Request $request)
    {
        $validated = $request->validate([
            'visit_id'  => 'required|exists:visits,id',
            'item_id'   => 'required|integer',
            'item_type' => 'required|in:medicine,lab,injection,procedure', // PROCEDURE
        ]);

        $response = ['success' => false, 'message' => 'Unknown error'];

        try {
            $visit = Visit::findOrFail($request->visit_id);

            DB::transaction(function () use ($request, $visit, &$response) {
                $message = '';

                if ($request->item_type === 'medicine') {
                    $order = $visit->medicineOrders()->findOrFail($request->item_id);

                    if ($order->is_issued) {
                        throw new \Exception('Cannot remove: medicine already issued.');
                    }

                    $issues = $order->pharmacyIssues;
                    foreach ($issues as $issue) {
                        MedicineBatch::where('medicine_id', $issue->medicine_id)
                            ->where('batch_no', $issue->batch_no)
                            ->increment('current_stock', $issue->quantity_issued);
                        $issue->delete();
                    }

                    $medicineName = $order->medicine->medicine_name;
                    $order->delete();

                    $message = "Medicine '{$medicineName}' removed. Stock restored.";
                } elseif ($request->item_type === 'lab') {
                    $order = $visit->labOrders()->findOrFail($request->item_id);

                    if ($order->is_paid) throw new \Exception('Lab test already paid.');
                    if ($order->result) throw new \Exception('Result already entered.');

                    $order->delete();
                    $message = "Lab test removed.";
                } elseif ($request->item_type === 'injection') {
                    $order = $visit->injectionOrders()->findOrFail($request->item_id);

                    if ($order->is_paid || $order->is_given) {
                        throw new \Exception('Injection already processed.');
                    }

                    $order->delete();
                    $message = "Injection removed.";
                } elseif ($request->item_type === 'procedure') { // PROCEDURE
                    $order = $visit->procedures()->findOrFail($request->item_id);

                    if ($order->is_paid || $order->is_issued) {
                        throw new \Exception('Procedure already processed.');
                    }

                    $order->delete();
                    $message = "Procedure removed.";
                }

                $response = [
                    'success' => true,
                    'message' => $message
                ];
            });
        } catch (\Exception $e) {
            Log::error("Remove bill item failed", [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            $response = [
                'success' => false,
                'message' => $e->getMessage() ?: 'Failed to remove item.'
            ];
        }

        return response()->json($response)
            ->header('Content-Type', 'application/json');
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
                'procedures.procedure', // PROCEDURE
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
        // Medicines: Only issued AND unpaid
        $medicines = $visit->medicineOrders()
            ->where('is_issued', false)
            ->where('is_paid', false)
            ->with(['medicine', 'pharmacyIssues'])
            ->get();

        // Lab Tests: Only unpaid
        $labTests = $visit->labOrders()
            ->where('is_paid', false)
            ->with('test')
            ->get();

        // Injections: Only given
        $injections = $visit->injectionOrders()
            ->where('is_given', true)
            ->with('medicine')
            ->get();

        // PROCEDURES: Only issued AND unpaid
        $procedures = $visit->procedures()
            ->where('is_issued', false)
            ->where('is_paid', false)
            ->with('procedure')
            ->get();

        // Bed/Ward charges
        $bedAdmission = $visit->bedAdmission()->where('is_discharged', true)->first();
        $bedCharges = $bedAdmission?->bed_charges ?? 0;
        $bedDays    = $bedAdmission?->total_days ?? 0;

        $showRegConsult = false; // No registration/consultation fees

        // Grand total
        $grandTotal = 0;
        $grandTotal += $medicines->sum(fn($order) => ($order->pharmacyIssues->sum('quantity_issued') ?? 1) * $order->medicine->price);
        $grandTotal += $labTests->sum(fn($t) => $t->test->price);
        $grandTotal += $injections->sum(fn($i) => $i->medicine->price);
        $grandTotal += $procedures->sum(fn($p) => $p->procedure->price); // PROCEDURE
        $grandTotal += $bedCharges;

        $hasUnpaidItems = $medicines->isNotEmpty()
            || $labTests->isNotEmpty()
            || $injections->isNotEmpty()
            || $bedCharges > 0
            || $procedures->isNotEmpty(); // PROCEDURE

        $showGenerateButton = $hasUnpaidItems && $grandTotal > 0;

        // Sidebar data
        $pendingVisits = Visit::with(['patient', 'doctor'])
            ->where(function ($q) {
                $q->whereHas('labOrders', fn($sq) => $sq->where('is_paid', false))
                  ->orWhereHas('medicineOrders', fn($sq) => $sq->where('is_issued', false)->orWhere('is_paid', false))
                  ->orWhereHas('injectionOrders')
                  ->orWhereHas('bedAdmission')
                  ->orWhereHas('procedures', fn($sq) => $sq->where('is_paid', false)); // PROCEDURE
            })
            ->latest('visit_date')
            ->paginate(20);

        $pendingCount = $pendingVisits->total();

        $inProgressVisits = Visit::with(['patient', 'doctor'])
            ->where(function ($q) {
                $q->whereHas('labOrders', fn($sq) => $sq->where('is_completed', false)->where('is_paid', true))
                  ->orWhereHas('medicineOrders', fn($sq) => $sq->where('is_issued', false))
                  ->orWhereHas('injectionOrders', fn($sq) => $sq->where('is_given', false))
                  ->orWhereHas('bedAdmission', fn($sq) => $sq->where('is_discharged', false))
                  ->orWhereHas('procedures', fn($sq) => $sq->where('is_issued', false)); // PROCEDURE
            })
            ->latest('visit_date')
            ->get();

        $receipts = Receipt::with(['visit.patient', 'visit.payments'])
            ->latest('generated_at')
            ->get();

        return view('billing.index', compact(
            'visit',
            'medicines',
            'labTests',
            'injections',
            'procedures', // PROCEDURE
            'bedCharges',
            'bedDays',
            'grandTotal',
            'showGenerateButton',
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

        $recentPayment = $visit->payments()
            ->where('amount', $data['grandTotal'])
            ->where('paid_at', '>', now()->subSeconds(10))
            ->exists();

        if ($recentPayment) {
            return redirect()->route('billing.index')
                ->with('warning', 'Receipt already generated recently. Avoiding duplicate.');
        }

        if ($data['grandTotal'] <= 0) {
            return back()->with('error', 'No pending amount to generate receipt.');
        }

        try {
            $year = now()->format('Y');
            $count = Receipt::whereYear('generated_at', $year)->count() + 1;
            $receiptNo = "RCPT{$year}-" . str_pad($count, 6, '0', STR_PAD_LEFT);

            $receipt = Receipt::create([
                'visit_id'     => $visit->id,
                'receipt_no'   => $receiptNo,
                'grand_total'  => $data['grandTotal'],
                'generated_by' => Auth::id(),
                'generated_at' => now(),
            ]);

            $payment = Payment::create([
                'visit_id'       => $visit->id,
                'amount'         => $data['grandTotal'],
                'type'           => 'bill_payment',
                'payment_method' => 'cash',
                'received_by'    => Auth::id(),
                'paid_at'        => now(),
            ]);

            $details = [];

            // Medicines
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

            // PROCEDURES
            foreach ($data['procedures'] as $proc) {
                $details[] = [
                    'item_type'   => 'procedure',
                    'item_name'   => $proc->procedure->name . ' (Procedure)',
                    'quantity'    => 1,
                    'unit_price'  => $proc->procedure->price,
                    'total_price' => $proc->procedure->price,
                ];

                $proc->update([
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

            foreach ($details as $detail) {
                PaymentDetail::create(array_merge($detail, ['payment_id' => $payment->id]));
            }

            return redirect()->route('billing.index')
                ->with('success', "Receipt {$receiptNo} generated and payment recorded successfully!");

        } catch (\Exception $e) {
            Log::error("BillingController@generateReceipt - ERROR: " . $e->getMessage());
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

            // Medicines
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

            // PROCEDURES
            foreach ($data['procedures'] as $proc) {
                $details[] = [
                    'item_type'   => 'procedure',
                    'item_name'   => $proc->procedure->name . ' (Procedure)',
                    'quantity'    => 1,
                    'unit_price'  => $proc->procedure->price,
                    'total_price' => $proc->procedure->price,
                ];

                $proc->update([
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

            foreach ($details as $detail) {
                PaymentDetail::create(array_merge($detail, ['payment_id' => $payment->id]));
            }

            return back()->with('success', "Payment of Tsh " . number_format($request->amount, 0) . " recorded successfully!");

        } catch (\Exception $e) {
            Log::error("recordPayment ERROR: " . $e->getMessage());
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