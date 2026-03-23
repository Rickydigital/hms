<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\VisitProcedure;
use App\Models\ProcedureMaster;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class ProcedureController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->can('manage procedures')) {
                abort(403, 'You do not have permission to access Procedures.');
            }
            return $next($request);
        });
    }

    // List procedures for a visit / dashboard
    public function index(Request $request)
    {
        $search = $request->get('search');
        $history = collect();

        // Search patient procedures
        if ($search) {
            $patients = Patient::where('patient_id', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%")
                ->limit(10)
                ->get();

            foreach ($patients as $patient) {
                $orders = VisitProcedure::with(['procedure', 'visit'])
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

        // Pending (paid but not issued)
        $pending = VisitProcedure::with(['visit.patient', 'procedure'])
            ->where('is_issued', false)
            ->where('is_paid', true)
            ->latest()
            ->get();

        // Completed today
        $completedToday = VisitProcedure::with(['visit.patient', 'procedure'])
            ->where('is_issued', true)
            ->whereDate('issued_at', today())
            ->latest()
            ->take(15)
            ->get();

        return view('procedures.index', compact('pending', 'completedToday', 'history', 'search'));
    }

    // Show a single procedure order
    public function show(VisitProcedure $procedure)
    {
        $procedure->load(['visit.patient', 'procedure']);

        // Block if not paid
        if (!$procedure->is_paid) {
            return redirect()->route('procedures.index')
                ->with('error', "This procedure is NOT PAID yet. Payment required first.");
        }

        return view('procedures.show', compact('procedure'));
    }

    // Mark procedure as issued
    public function issue(VisitProcedure $procedure)
    {
        if (!$procedure->is_paid) {
            return back()->with('error', "Cannot issue an unpaid procedure.");
        }

        $procedure->markAsIssued();

        return redirect()->route('procedures.index')
            ->with('success', "{$procedure->procedure->procedure_name} marked as issued.");
    }

    // Mark procedure as paid
    public function markPaid(Request $request, VisitProcedure $procedure)
    {
        $procedure->markAsPaid(Auth::id());

        return redirect()->route('procedures.index')
            ->with('success', "{$procedure->procedure->procedure_name} marked as paid.");
    }

    // Store a new procedure for a visit
    public function store(Request $request)
    {
        $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'procedure_id' => 'required|exists:procedures_master,id',
        ]);

        VisitProcedure::create([
            'visit_id' => $request->visit_id,
            'procedure_id' => $request->procedure_id,
            'is_paid' => false,
            'is_issued' => false,
        ]);

        return back()->with('success', 'Procedure added to visit successfully.');
    }
}