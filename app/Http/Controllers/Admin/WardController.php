<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Ward;
use Illuminate\Http\Request;

class WardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage wards');
    }

    public function index()
    {
        $wards = Ward::orderBy('ward_name')->get();
        return view('admin.wards.index', compact('wards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ward_name' => 'required|unique:wards',
            'price_per_day' => 'required|numeric|min:0',
            'total_beds' => 'required|integer|min:1',
        ]);

        Ward::create([
            'ward_code' => 'WD' . str_pad(Ward::max('id') + 1, 3, '0', STR_PAD_LEFT),
            'ward_name' => $request->ward_name,
            'price_per_day' => $request->price_per_day,
            'total_beds' => $request->total_beds,
            'available_beds' => $request->total_beds,
            'facilities' => $request->facilities,
            'is_active' => true,
        ]);

        return back()->with('success', 'Ward added');
    }

    public function update(Request $request, Ward $ward)
    {
        $request->validate([
            'ward_name' => 'required|unique:wards,ward_name,'.$ward->id,
            'price_per_day' => 'required|numeric',
        ]);
        $ward->update($request->all());
        return back()->with('success', 'Ward updated');
    }
}