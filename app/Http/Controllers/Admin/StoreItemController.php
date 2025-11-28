<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\StoreItemMaster;
use Illuminate\Http\Request;

class StoreItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage store items');
    }

    public function index()
    {
        $items = StoreItemMaster::orderBy('item_name')->paginate(25);
        return view('admin.store-items.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|unique:store_item_masters',
            'price' => 'required|numeric',
        ]);

        StoreItemMaster::create([
            'item_name' => $request->item_name,
            'unit' => $request->unit ?? 'Piece',
            'price' => $request->price,
            'minimum_stock' => $request->minimum_stock ?? 50,
            'is_active' => true,
        ]);

        return back()->with('success', 'Store item added');
    }

    public function update(Request $request, StoreItemMaster $storeItem)
    {
        $request->validate([
            'item_name' => 'required|unique:store_item_masters,item_name,'.$storeItem->id,
        ]);
        $storeItem->update($request->all());
        return back()->with('success', 'Updated');
    }
}