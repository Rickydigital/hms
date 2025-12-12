<?php

namespace App\Http\Controllers;

use App\Models\VisitLabOrder;
use App\Models\LabResult;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class LabController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->can('enter lab results')) {
                abort(403, 'You do not have permission to access the Lab module.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $history = collect();

        // Search Patient Lab History (shows both paid & unpaid)
        if ($search) {
            $patients = Patient::where('patient_id', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%")
                ->limit(10)
                ->get();

            foreach ($patients as $patient) {
                $orders = VisitLabOrder::with(['test', 'result', 'visit'])
                    ->whereHas('visit', fn($q) => $q->where('patient_id', $patient->id))
                    ->latest()
                    ->get();

                if ($orders->count()) {
                    $history->push([
                        'patient' => $patient,
                        'orders' => $orders
                    ]);
                }
            }
        }

        // PENDING TESTS: ONLY PAID + NOT COMPLETED
        $pending = VisitLabOrder::with(['visit.patient', 'test'])
            ->where('is_completed', false)
            ->where('is_paid', true)  // ONLY PAID TESTS
            ->latest()
            ->get();

        // TODAY'S COMPLETED (paid or not — just for reference)
        $completedToday = VisitLabOrder::with(['visit.patient', 'test', 'result'])
            ->where('is_completed', true)
            ->whereDate('completed_at', today())
            ->latest()
            ->take(15)
            ->get();

        return view('lab.index', compact('pending', 'completedToday', 'history', 'search'));
    }

    public function show(VisitLabOrder $order)
    {
        $order->load(['visit.patient', 'test', 'result']);

        // BLOCK ACCESS IF NOT PAID
        if (!$order->is_paid) {
            return redirect()->route('lab.index')
                ->with('error', "This lab test is NOT PAID yet. Patient must pay at billing first before results can be entered.");
        }

        return view('lab.show', compact('order'));
    }

    public function storeResult(Request $request, VisitLabOrder $order)
    {
        // Double security check
        if (!Auth::user()->can('enter lab results')) {
            abort(403);
        }

        // BLOCK IF NOT PAID
        if (!$order->is_paid) {
            return back()->withErrors('This lab test is not paid. Payment required before entering results.');
        }

        $request->validate([
            'result_value' => 'nullable|numeric',
            'result_text'  => 'nullable|string',
            'remarks'      => 'nullable|string|max:500',
            'normal_range' => 'nullable|string',
        ]);

        $isAbnormal = false;
        if ($request->filled('result_value') && $order->test->normal_range) {
            $range = explode('-', str_replace(' ', '', $order->test->normal_range));
            if (count($range) == 2) {
                $min = (float)$range[0];
                $max = (float)$range[1];
                $value = (float)$request->result_value;
                $isAbnormal = $value < $min || $value > $max;
            }
        }

        LabResult::updateOrCreate(
            ['visit_lab_order_id' => $order->id],
            [
                'result_value'    => $request->result_value,
                'result_text'     => $request->result_text,
                'remarks'         => $request->remarks,
                'normal_range'    => $request->normal_range ?? $order->test->normal_range,
                'is_abnormal'     => $isAbnormal,
                'technician_id'   => Auth::id(),
                'reported_at'     => now(),
            ]
        );

        // Mark as completed
        $order->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        return redirect()->route('lab.index')
            ->with('success', "Result for {$order->test->test_name} saved successfully!");
    }
}