<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage suppliers');
    }

    public function index()
    {
        $suppliers = Supplier::orderBy('name')->paginate(20);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:suppliers',
        ]);

        Supplier::create($request->all());
        return back()->with('success', 'Supplier added');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:suppliers,phone,'.$supplier->id,
        ]);
        $supplier->update($request->all());
        return back()->with('success', 'Updated');
    }
}