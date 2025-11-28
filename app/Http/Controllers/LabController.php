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
        // Search Patient History
        $search = $request->get('search');
        $history = collect();

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

        // Pending + Today's Completed
        $pending = VisitLabOrder::with(['visit.patient', 'test'])
            ->where('is_completed', false)
            ->latest()
            ->get();

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
        return view('lab.show', compact('order'));
    }

    public function storeResult(Request $request, VisitLabOrder $order)
    {
        if (!Auth::user()->can('enter lab results')) {
            abort(403);
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

        return redirect()->route('lab.index')->with('success', 'Lab result saved successfully!');
    }
}