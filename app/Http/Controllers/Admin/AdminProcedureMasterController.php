<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\ProcedureMaster;
use Illuminate\Http\Request;

class AdminProcedureMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage procedures');
    }

    // List all procedures (admin view)
    public function index()
    {
        $procedures = ProcedureMaster::orderBy('sort_order')->paginate(20);
        return view('admin.procedures.index', compact('procedures'));
    }

    // Store new procedure
    public function store(Request $request)
    {
        $request->validate([
            'procedure_name' => 'required|string|max:255|unique:procedures_master',
            'price' => 'required|numeric|min:0',
        ]);

        ProcedureMaster::create([
            'procedure_code' => 'PR' . str_pad(ProcedureMaster::max('id') + 1, 4, '0', STR_PAD_LEFT),
            'procedure_name' => $request->procedure_name,
            'description' => $request->description,
            'price' => $request->price,
            'is_active' => true,
            'sort_order' => ProcedureMaster::max('sort_order') + 1,
        ]);

        return back()->with('success', 'Procedure added');
    }

    // Update existing procedure
    public function update(Request $request, ProcedureMaster $procedure)
    {
        $request->validate([
            'procedure_name' => 'required|unique:procedures_master,procedure_name,' . $procedure->id,
            'price' => 'required|numeric',
        ]);

        $procedure->update($request->only(['procedure_name', 'description', 'price', 'is_active']));
        return back()->with('success', 'Updated');
    }
}