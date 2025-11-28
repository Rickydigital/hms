{{-- resources/views/admin/profit-report.blade.php --}}
@extends('components.main-layout')
@section('title', 'Profit & Loss Report • Mana Dispensary')

@section('content')
<div class="container-fluid py-4 py-md-5">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 text-teal-800 fw-bold mb-1">
                <i class="fas fa-chart-pie me-2"></i> Profit & Loss Report
            </h1>
            <p class="text-muted mb-0">Real-time profit per medicine • OTC + Prescription</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 g-md-4 mb-4 mb-md-5">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white">
                <div class="card-body text-center py-4">
                    <h6 class="mb-2 opacity-90">Total Revenue</h6>
                    <h3 class="mb-0 fw-bold">Tsh{{ number_format($summary['total_sales']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-gradient-danger text-white">
                <div class="card-body text-center py-4">
                    <h6 class="mb-2 opacity-90">Total Cost</h6>
                    <h3 class="mb-0 fw-bold">Tsh{{ number_format($summary['total_cost']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
                <div class="card-body text-center py-4">
                    <h6 class="mb-2 opacity-90">Net Profit</h6>
                    <h3 class="mb-0 fw-bold">Tsh{{ number_format($summary['total_profit']) }}</h3>
                    <small class="opacity-90">
                        {{ $summary['total_cost'] > 0 ? round(($summary['total_profit'] / $summary['total_cost']) * 100, 1) : 0 }}% margin
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit Table - Super Responsive -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-teal text-white py-3 py-md-4">
            <h5 class="mb-0 fw-bold d-flex align-items-center justify-content-between">
                Profit by Medicine
                <small class="d-block d-md-none mt-1">← Swipe to see more →</small>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Medicine</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Cost</th>
                            <th class="text-end">Sales</th>
                            <th class="text-end">Profit</th>
                            <th class="text-center pe-3">Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report as $item)
                        <tr class="{{ $item['profit'] < 0 ? 'table-danger' : '' }}">
                            <td class="ps-3 fw-medium">{{ $loop->iteration }}</td>
                            <td class="py-3">
                                <div>
                                    <strong class="d-block">{{ $item['medicine_name'] }}</strong>
                                    @if($item['generic_name'])
                                        <small class="text-muted">{{ $item['generic_name'] }}</small>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary fs-6">{{ $item['total_qty'] }}</span>
                            </td>
                            <td class="text-end text-muted">Tsh{{ number_format($item['total_cost']) }}</td>
                            <td class="text-end">Tsh{{ number_format($item['total_sales']) }}</td>
                            <td class="text-end fw-bold {{ $item['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                Tsh{{ number_format($item['profit']) }}
                            </td>
                            <td class="text-center pe-3">
                                <span class="badge rounded-pill {{ $item['profit_margin'] >= 50 ? 'bg-success' : ($item['profit_margin'] >= 20 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $item['profit_margin'] }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-2x mb-3 opacity-50"></i><br>
                                No sales recorded yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
.bg-teal {
    background: linear-gradient(135deg, #0d9488, #0f766e) !important;
}
.text-teal-800 {
    color: #0d9488 !important;
}

/* Super smooth horizontal scroll on mobile */
.table-responsive {
    -webkit-overflow-scrolling: touch;
}

/* Optional: Add subtle shadow when scrolling */
.card-body {
    position: relative;
}
.card-body::after {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0;
    width: 20px;
    background: linear-gradient(to left, rgba(0,0,0,0.1), transparent);
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s;
}
.table-responsive:not(.overflow-x-hidden)::after {
    opacity: 1;
}

/* Make cards stack nicely on small screens */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem !important;
    }
    .table th, .table td {
        font-size: 0.9rem;
        padding: 0.75rem 0.5rem !important;
    }
}
</style>