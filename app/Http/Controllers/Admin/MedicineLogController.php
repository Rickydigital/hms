<?php
// app/Http/Controllers/Admin/MedicineLogController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicineStockLog;
use App\Models\MedicineMaster;
use Illuminate\Http\Request;

class MedicineLogController extends Controller
{
    public function index()
    {
        $logs = MedicineStockLog::with(['medicine', 'batch', 'user'])
            ->latest()
            ->paginate(50);

        $medicines = MedicineMaster::active()->orderBy('medicine_name')->get();

        return view('admin.medicine-logs', compact('logs', 'medicines'));
    }

    public function filter(Request $request)
    {
        $query = MedicineStockLog::with(['medicine', 'batch', 'user']);

        if ($request->filled('medicine_id')) {
            $query->where('medicine_id', $request->medicine_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(50);
        $medicines = MedicineMaster::active()->orderBy('medicine_name')->get();

        return view('admin.medicine-logs', compact('logs', 'medicines'));
    }
}