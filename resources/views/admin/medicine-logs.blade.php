{{-- resources/views/admin/medicine-logs.blade.php --}}
@extends('components.main-layout')

@section('title', 'Medicine Stock Logs • Mana Dispensary')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">
            <i class="fas fa-history text-primary"></i> Medicine Stock Movement Logs
        </h1>
        <span class="badge bg-primary fs-5 px-4 py-2">{{ $logs->total() }} Records</span>
    </div>

    <!-- FILTER CARD -->
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-filter"></i> Filter Logs
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.medicine.logs.filter') }}" method="GET">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
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
                            <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
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

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-success me-2 flex-fill">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('admin.medicine.logs') }}" class="btn btn-outline-secondary flex-fill">
                            <i class="fas fa-sync"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- LOGS TABLE -->
    <div class="card shadow border-left-success">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-gradient-primary text-green">
                        <tr>
                            <th width="140">Date & Time</th>
                            <th>Medicine</th>
                            <th width="100">Batch</th>
                            <th width="100">Type</th>
                            <th width="90" class="text-center">Qty</th>
                            <th width="140" class="text-center">Stock Before → After</th>
                            <th width="100">User</th>
                            <th width="140">Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="{{ $log->quantity < 0 ? 'table-danger' : 'table-success' }} opacity-90">
                                <td>
                                    <div class="small text-muted">{{ $log->created_at->format('d M Y') }}</div>
                                    <strong>{{ $log->created_at->format('h:i A') }}</strong>
                                </td>

                                <td>
                                    <strong>{{ $log->medicine->medicine_name }}</strong><br>
                                    <small class="text-muted">{{ $log->medicine->generic_name ?? '—' }}</small>
                                </td>

                                <td class="text-center">
                                    @if($log->batch)
                                        <div class="badge bg-info text-dark fs-6 mb-1">{{ $log->batch->batch_no }}</div>
                                        <small class="d-block text-muted">Exp: {{ $log->batch->expiry_date->format('M/y') }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $type = $log->type;
                                        $badge = match($type) {
                                            'purchase'   => ['bg-success', 'IN'],
                                            'sale'       => ['bg-danger', 'OUT'],
                                            'return'     => ['bg-warning text-dark', 'RETURN'],
                                            'damage'     => ['bg-dark', 'DAMAGE'],
                                            'expiry'     => ['bg-secondary', 'EXPIRED'],
                                            'adjustment' => ['bg-primary', 'ADJ'],
                                            default      => ['bg-info', strtoupper($type)]
                                        };
                                    @endphp
                                    <span class="badge {{ $badge[0] }} fs-6">
                                        <i class="fas fa-arrow-{{ $log->quantity > 0 ? 'down' : 'up' }} me-1"></i>
                                        {{ $badge[1] }}
                                    </span>
                                </td>

                                <td class="text-center fw-bold fs-5 {{ $log->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}
                                </td>

                                <td class="text-center font-monospace fw-bold">
                                    <span class="text-muted">{{ $log->stock_before }}</span>
                                    <i class="fas fa-arrow-right mx-2 text-primary"></i>
                                    <span class="{{ $log->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $log->stock_after }}
                                    </span>
                                </td>

                                <td>
                                    <div class="small fw-bold">{{ $log->user->name }}</div>
                                    <span class="text-muted small">{{ $log->user->getRoleNames()->first() ?? 'Staff' }}</span>
                                </td>

                                <td>
                                    @if($log->reference_type && $log->reference_id)
                                        @php
                                            $ref = class_basename($log->reference_type);
                                            $id  = $log->reference_id;
                                        @endphp
                                        @if($ref === 'MedicinePurchase')
                                            <a href="{{ route('store.purchase.index', $id) }}" class="text-decoration-none">
                                                <i class="fas fa-file-invoice text-success"></i> Purchase #{{ $id }}
                                            </a>
                                        @elseif($ref === 'VisitMedicineOrder')
                                            <a href="#" class="text-decoration-none">
                                                <i class="fas fa-prescription text-primary"></i> Prescription #{{ $id }}
                                            </a>
                                        @else
                                            <small>{{ $ref }} #{{ $id }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-box-open fa-4x text-muted mb-3 opacity-25"></i>
                                    <h5 class="text-muted">No stock movement logs found</h5>
                                    <p class="text-muted">Try adjusting your filters</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="card-footer bg-light border-top">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Select2 + FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('.select2').select2({
            placeholder: "Search medicine by name or generic...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection