@extends('components.main-layout')

@section('title', 'Medicine Stock Logs • Mana Dispensary')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">
            <i class="fas fa-prescription-bottle-alt"></i> Medicine Stock Movement Logs
        </h1>
        <span class="badge bg-primary fs-5 px-4 py-2">{{ $logs->total() }} Total Records</span>
    </div>

    <!-- FILTER CARD -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Filter Logs</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.medicine.logs.filter') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Medicine</label>
                        <select name="medicine_id" class="form-select select2">
                            <option value="">All Medicines</option>
                            @foreach($medicines as $m)
                                <option value="{{ $m->id }}" {{ request('medicine_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->medicine_name }} @if($m->generic_name) • {{ $m->generic_name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Purchase (IN)</option>
                            <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Sale (OUT)</option>
                            <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
                            <option value="damage" {{ request('type') == 'damage' ? 'selected' : '' }}>Damage</option>
                            <option value="expiry" {{ request('type') == 'expiry' ? 'selected' : '' }}>Expiry</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-success me-2">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                        <a href="{{ route('admin.medicine.logs') }}" class="btn btn-secondary">
                            <i class="fas fa-sync"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- LOGS TABLE -->
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-gradient-primary text-green">
                        <tr>
                            <th>Date & Time</th>
                            <th>Medicine</th>
                            <th>Batch</th>
                            <th>Type</th>
                            <th class="text-center">Qty</th>
                            <th>Balance After</th>
                            <th>User</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr class="{{ $log->quantity < 0 ? 'table-danger' : 'table-success' }} opacity-75">
                            <td>
                                <small class="text-muted">{{ $log->created_at->format('d M Y') }}</small><br>
                                <strong>{{ $log->created_at->format('h:i A') }}</strong>
                            </td>
                            <td>
                                <strong>{{ $log->medicine->medicine_name }}</strong><br>
                                <small class="text-muted">{{ $log->medicine->generic_name }}</small>
                            </td>
                            <td>
                                @if($log->batch)
                                    <span class="badge bg-info">{{ $log->batch->batch_no }}</span><br>
                                    <small>Exp: {{ $log->batch->expiry_date->format('M Y') }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @switch($log->type)
                                    @case('purchase')  <span class="badge bg-success fs-6">PURCHASE</span> @break
                                    @case('sale')      <span class="badge bg-danger fs-6">SALE</span> @break
                                    @case('return')    <span class="badge bg-warning text-dark fs-6">RETURN</span> @break
                                    @case('damage')    <span class="badge bg-dark fs-6">DAMAGE</span> @break
                                    @case('expiry')    <span class="badge bg-secondary fs-6">EXPIRED</span> @break
                                    @default           <span class="badge bg-primary">{{ strtoupper($log->type) }}</span>
                                @endswitch
                            </td>
                            <td class="text-center fw-bold fs-5">
                                @if($log->quantity > 0)
                                    <span class="text-success">+{{ $log->quantity }}</span>
                                @else
                                    <span class="text-danger">{{ $log->quantity }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $balance = \App\Models\MedicineBatch::where('medicine_id', $log->medicine_id)
                                        ->sum('current_stock');
                                @endphp
                                <strong>{{ $balance }}</strong>
                            </td>
                            <td>
                                <small>{{ $log->user->name }}</small><br>
                                <span class="text-muted">{{ $log->user->role ?? 'Staff' }}</span>
                            </td>
                            <td>
                                @if($log->reference_type && $log->reference_id)
                                    <a href="#" class="text-decoration-none">
                                        {{ class_basename($log->reference_type) }} #{{ $log->reference_id }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 opacity-20"></i>
                                <h4>No stock movement found</h4>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="card-footer bg-light">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Select2 + Icons -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Search medicine...",
            width: '100%'
        });
    });
</script>
@endsection