<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\LabTestMaster;
use Illuminate\Http\Request;

class LabTestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage lab tests');
    }

    public function index()
    {
        $tests = LabTestMaster::orderBy('sort_order')->paginate(20);
        return view('admin.lab-tests.index', compact('tests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'test_name' => 'required|string|max:255|unique:lab_tests_master',
            'price' => 'required|numeric|min:0',
        ]);

        LabTestMaster::create([
            'test_code' => 'LT' . str_pad(LabTestMaster::max('id') + 1, 4, '0', STR_PAD_LEFT),
            'test_name' => $request->test_name,
            'description' => $request->description,
            'price' => $request->price,
            'is_active' => true,
            'sort_order' => LabTestMaster::max('sort_order') + 1,
        ]);

        return back()->with('success', 'Lab test added');
    }

    public function update(Request $request, LabTestMaster $labTest)
    {
        $request->validate([
            'test_name' => 'required|unique:lab_tests_master,test_name,'.$labTest->id,
            'price' => 'required|numeric',
        ]);

        $labTest->update($request->only(['test_name', 'description', 'price', 'is_active']));
        return back()->with('success', 'Updated');
    }
}