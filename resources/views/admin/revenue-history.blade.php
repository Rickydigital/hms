@extends('components.main-layout')
@section('title', 'Revenue History • Mana Dispensary')

@section('content')
<div class="container-fluid py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-teal-800 fw-bold">
                <i class="bi bi-graph-up me-3"></i> Revenue History
            </h1>
            <p class="text-muted">Daily breakdown of OPD, pharmacy, and total revenues</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-teal">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Filter -->
    <div class="card shadow mb-4 border-teal">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from" class="form-control form-control-lg" 
                           value="{{ request('from', $from) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to" class="form-control form-control-lg" 
                           value="{{ request('to', $to) }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-teal btn-lg px-5">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.revenue') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header bg-teal text-white">
            <h5 class="mb-0">Revenue Growth Overview</h5>
        </div>
        <div class="card-body">
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>

    <!-- Totals Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow border-0 text-center p-3">
                <h6>Total OPD Revenue</h6>
                <h4 class="text-teal-700 fw-bold">Tsh {{ number_format($totalOpd, 0) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow border-0 text-center p-3">
                <h6>Total Pharmacy Revenue</h6>
                <h4 class="text-teal-700 fw-bold">Tsh {{ number_format($totalPharmacy, 0) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow border-0 text-center p-3">
                <h6>Grand Total Revenue</h6>
                <h4 class="text-teal-700 fw-bold">Tsh {{ number_format($grandTotal, 0) }}</h4>
            </div>
        </div>
    </div>

    <!-- Revenue Table -->
    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-teal text-white">
                        <tr>
                            <th>Date</th>
                            <th class="text-end">OPD Revenue</th>
                            <th class="text-end">Pharmacy Revenue</th>
                            <th class="text-end">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenues as $revenue)
                        <tr>
                            <td class="fw-bold">{{ \Carbon\Carbon::parse($revenue['date'])->format('d M Y') }}</td>
                            <td class="text-end fw-bold text-teal-700">
                                Tsh {{ number_format($revenue['opd_revenue'], 0) }}
                            </td>
                            <td class="text-end fw-bold text-teal-700">
                                Tsh {{ number_format($revenue['pharmacy_revenue'], 0) }}
                            </td>
                            <td class="text-end fw-bold text-teal-700">
                                Tsh {{ number_format($revenue['total_revenue'], 0) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fa-3x mb-3 opacity-20"></i>
                                <h5>No revenue history found</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($revenues->count())
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td>Total</td>
                            <td class="text-end">Tsh {{ number_format($totalOpd, 0) }}</td>
                            <td class="text-end">Tsh {{ number_format($totalPharmacy, 0) }}</td>
                            <td class="text-end">Tsh {{ number_format($grandTotal, 0) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            <div class="card-footer bg-light">
                {{ $revenues->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>

<script>
console.log("Revenue Chart Data:", {!! json_encode($revenue_chart) !!});
</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($revenue_chart['labels']) !!},
        datasets: {!! json_encode($revenue_chart['datasets']) !!}
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false
        },
        stacked: false,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': Tsh ' + Number(context.raw).toLocaleString();
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Tsh ' + Number(value).toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
@endpush
@endsection