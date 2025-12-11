{{-- resources/views/admin/dashboard.blade.php --}}
@extends('components.main-layout')
@section('title', 'Admin Dashboard • Mana Dispensary')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800 fw-bold">Mana Dispensary • Admin Dashboard</h1>
        <div class="text-muted">Welcome back, {{ auth()->user()->name }}</div>
    </div>

    <!-- STATS CARDS -->
    <div class="row">

        <!-- Total Patients -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Patients</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="counter" data-target="{{ $total_patients }}">0</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's OPD -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Today's OPD</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="counter" data-target="{{ $today_opd }}">0</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Value -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Stock Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Tsh<span class="counter" data-target="{{ $total_stock_value }}">0</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Today's Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Tsh<span class="counter" data-target="{{ $today_revenue }}">0</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue History Link Card -->
<div class="col-xl-3 col-md-6 mb-4">
    <a href="{{ route('admin.revenue') }}" class="text-decoration-none">
        <div class="card border-left-teal shadow h-100 py-2 hover-lift" style="border-left-color: #20c997 !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-teal text-uppercase mb-1">
                            Revenue History
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            View Daily Logs & Chart
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

    </div>

    <div class="row">

        <!-- Today's Pharmacy Sales -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Today's Pharmacy Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Tsh<span class="counter" data-target="{{ $today_pharmacy_sales }}">0</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pills fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pharmacy Sales (All Time) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-purple shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">Total Pharmacy Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Tsh<span class="counter" data-target="{{ $total_pharmacy_sales }}">0</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cash-register fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Time Total Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">All Time Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Tsh<span class="counter" data-target="{{ $total_revenue_all_time }}">0</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profit & Loss Report Link Card (Same Size as Others) -->
<div class="col-xl-3 col-md-6 mb-4">
    <a href="{{ route('admin.profit-report') }}" class="text-decoration-none">
        <div class="card border-left-success shadow h-100 py-2 hover-lift" style="border-left-color: #1cc88a !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Profit & Loss Report
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            View Profit Details
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-trend-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

    </div>

    <!-- QUICK ADMIN ACTIONS -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Quick Admin Actions</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-primary w-100 py-3 shadow-sm">
                        <i class="bi bi-person-plus-fill fs-4"></i><br>
                        <span class="small fw-bold">Add New User</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('admin.roles') }}" class="btn btn-outline-info w-100 py-3 shadow-sm">
                        <i class="bi bi-shield-check fs-4"></i><br>
                        <span class="small fw-bold">Manage Roles</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('lab-tests.index') }}" class="btn btn-outline-success w-100 py-3 shadow-sm">
                        <i class="bi bi-eyedropper fs-4"></i><br>
                        <span class="small fw-bold">Add Lab Test</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('medicines.index') }}" class="btn btn-outline-warning w-100 py-3 shadow-sm">
                        <i class="bi bi-capsule-pill fs-4"></i><br>
                        <span class="small fw-bold">Add Medicine</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('store-items.index') }}" class="btn btn-outline-secondary w-100 py-3 shadow-sm">
                        <i class="bi bi-box-seam fs-4"></i><br>
                        <span class="small fw-bold">Add Store Item</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('wards.index') }}" class="btn btn-outline-danger w-100 py-3 shadow-sm">
                        <i class="bi bi-building fs-4"></i><br>
                        <span class="small fw-bold">Add Ward / Bed</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-dark w-100 py-3 shadow-sm">
                        <i class="bi bi-truck fs-4"></i><br>
                        <span class="small fw-bold">Add Supplier</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('admin.medicine.logs') }}" class="btn btn-outline-purple w-100 py-3 shadow-sm">
                        <i class="bi bi-journal-medical fs-4"></i><br>
                        <span class="small fw-bold">Medicine Logs</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Trend (Last 7 Days)</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">OPD Patients Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="opdChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- TOP SELLING MEDICINES -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Top Selling Medicines (This Month)</h6>
            <a href="{{ route('medicines.index') }}" class="btn btn-sm btn-outline-primary">View All Medicines</a>
        </div>
        <div class="card-body">
            @if($top_medicines->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Medicine Name</th>
                            <th>Category</th>
                            <th>Qty Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($top_medicines as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $item->medicine->medicine_name }}</strong></td>
                            <td>{{ $item->medicine->category ?? 'General' }}</td>
                            <td><span class="badge bg-success fs-6">{{ $item->total }}</span></td>
                            <td>Tsh{{ number_format($item->total * ($item->medicine->selling_price ?? 0), 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted text-center py-4">No sales recorded this month yet.</p>
            @endif
        </div>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Charts Script -->
<script>
    const ctx1 = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: @json($revenue_chart['labels']),
            datasets: [{
                label: 'Daily Revenue (Tsh)',
                data: @json($revenue_chart['data']),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { plugins: { legend: { display: false } } }
    });

    const ctx2 = document.getElementById('opdChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: @json($opd_chart['labels']),
            datasets: [{
                label: 'OPD Patients',
                data: @json($opd_chart['data']),
                backgroundColor: '#1cc88a'
            }]
        },
        options: { plugins: { legend: { display: false } } }
    });
</script>

<!-- Animated Counters Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const counters = document.querySelectorAll('.counter');
        const speed = 200; // Lower = faster animation

        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const isMoney = counter.closest('.card-body').innerHTML.includes('Tsh');

            const updateCount = () => {
                const count = +counter.innerText.replace(/,/g, '');
                const increment = target / speed;

                if (count < target) {
                    const newCount = Math.ceil(count + increment);
                    counter.innerText = newCount.toLocaleString('en-US');
                    requestAnimationFrame(updateCount);
                } else {
                    counter.innerText = target.toLocaleString('en-US');
                }
            };

            // Trigger animation only when card is in viewport
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        updateCount();
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.7 });

            // Observe the parent card
            const card = counter.closest('.card');
            if (card) observer.observe(card);
        });
    });
</script>

<style>
    .btn-outline-purple {
        border-color: #6f42c1;
        color: #6f42c1;
    }
    .btn-outline-purple:hover {
        background-color: #6f42c1;
        color: white;
    }
</style>
@endsection