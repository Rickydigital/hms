<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\MedicineMaster;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage medicines');
    }

    public function index()
    {
        $medicines = MedicineMaster::orderBy('medicine_name')->paginate(25);
        return view('admin.medicines.index', compact('medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_name' => 'required|unique:medicines_master',
            'price' => 'required|numeric',
            'purchase_price' => 'required|numeric',
        ]);

        MedicineMaster::create([
            'medicine_code' => 'MED' . str_pad(MedicineMaster::max('id') + 1, 5, '0', STR_PAD_LEFT),
            'medicine_name' => $request->medicine_name,
            'generic_name' => $request->generic_name,
            'packing' => $request->packing,
            'type' => $request->type ?? 'Tablet',
            'price' => $request->price,
            'purchase_price' => $request->purchase_price,
            'minimum_stock' => $request->minimum_stock ?? 10,
            'is_active' => true,
        ]);

        return back()->with('success', 'Medicine added');
    }

    public function update(Request $request, MedicineMaster $medicine)
    {
        $request->validate([
            'medicine_name' => 'required|unique:medicines_master,medicine_name,'.$medicine->id,
        ]);
        $medicine->update($request->all());
        return back()->with('success', 'Updated');
    }
}