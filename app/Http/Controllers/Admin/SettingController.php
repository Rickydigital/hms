<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage settings');
    }

    public function index()
    {
        $settings = Setting::orderBy('sort_order')->get();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return back()->with('success', 'Settings saved successfully!');
  }

  public function storeCustom(Request $request)
{
    $request->validate([
        'key' => 'required|unique:settings,key',
        'value' => 'required',
    ]);

    Setting::create($request->only(['key', 'value', 'type']));
    return back()->with('success', 'Custom setting added!');
}

public function delete($key)
{
    Setting::where('key', $key)->delete();
    return response()->json(['success' => true]);
}
}