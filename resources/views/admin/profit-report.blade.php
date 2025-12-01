{{-- resources/views/admin/profit-report.blade.php --}}
@extends('components.main-layout')

@section('title', 'Profit & Loss Report • Mana Dispensary')

@section('content')
<div class="d-flex flex-column min-vh-100">
    <div class="container-fluid py-4 py-md-5 flex-grow-1 d-flex flex-column">

        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-teal-800 mb-1">
                    Profit & Loss Report
                </h1>
                <p class="text-muted mb-0">Real-time profit analysis • OTC + Prescription sales</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                Back to Dashboard
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-5">
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
                        <small class="opacity-90 d-block mt-2">
                            {{ $summary['total_cost'] > 0 ? round(($summary['total_profit'] / $summary['total_cost']) * 100, 1) : 0 }}% profit margin
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profit Table Card -->
        <div class="card shadow-sm border-0 rounded-4 flex-grow-1 d-flex flex-column" style="min-height: 0;">
            <div class="card-header bg-teal text-white py-3 py-md-4">
                <h5 class="mb-0 fw-bold d-flex align-items-center justify-content-between">
                    Profit by Medicine
                    <small class="d-block d-md-none text-white-50">Swipe →</small>
                </h5>
            </div>

            <div class="card-body p-0 d-flex flex-column flex-grow-1" style="min-height: 0;">
                <div class="table-responsive flex-grow-1">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-nowrap sticky-top">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Medicine</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Cost</th>
                                <th class="text-end">Sales</th>
                                <th class="text-end">Profit</th>
                                <th class="text-center pe-4">Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report as $item)
                                <tr class="{{ $item['profit'] < 0 ? 'table-danger' : '' }}">
                                    <td class="ps-4 fw-medium">
                                        {{ $report->firstItem() + $loop->index }}
                                    </td>
                                    <td class="py-3">
                                        <div>
                                            <div class="fw-semibold">{{ $item['medicine_name'] }}</div>
                                            @if($item['generic_name'])
                                                <small class="text-muted">{{ $item['generic_name'] }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill px-3">{{ $item['total_qty'] }}</span>
                                    </td>
                                    <td class="text-end text-muted">Tsh{{ number_format($item['total_cost']) }}</td>
                                    <td class="text-end fw-600">Tsh{{ number_format($item['total_sales']) }}</td>
                                    <td class="text-end fw-bold {{ $item['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        Tsh{{ number_format($item['profit']) }}
                                    </td>
                                    <td class="text-center pe-4">
                                        <span class="badge rounded-pill {{ 
                                            $item['profit_margin'] >= 50 ? 'bg-success' : 
                                            ($item['profit_margin'] >= 20 ? 'bg-warning text-dark' : 'bg-danger')
                                        }}">
                                            {{ $item['profit_margin'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i><br>
                                        No sales recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION – HAINA card-footer -->
                <div class="px-4 py-3 bg-white border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted small">
                        Showing {{ $report->firstItem() }} to {{ $report->lastItem() }} 
                        of {{ $report->total() }} medicines
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <!-- Per page selector -->
                        <select class="form-select form-select-sm w-auto" 
                                onchange="window.location = this.value">
                            @foreach([10, 25, 50, 100] as $size)
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => null]) }}"
                                    {{ request('per_page', 25) == $size ? 'selected' : '' }}>
                                    {{ $size }} per page
                                </option>
                            @endforeach
                        </select>

                        <!-- Laravel Pagination Links (Bootstrap 5 style) -->
                        {{ $report->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->

    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-teal {
        background: linear-gradient(135deg, #0d9488, #0f766e) !important;
    }
    .text-teal-800 {
        color: #0f766e !important;
    }
    .table-responsive { -webkit-overflow-scrolling: touch; }
</style>
@endpush