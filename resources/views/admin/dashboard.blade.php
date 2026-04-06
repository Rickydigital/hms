{{-- resources/views/admin/dashboard.blade.php --}}
@extends('components.main-layout')
@section('title', 'Admin Dashboard • Mana Dispensary')

@section('content')
<div class="container-fluid py-4 admin-dashboard">

    {{-- HEADER --}}
    <div class="dashboard-hero mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="hero-content">
                    <span class="hero-badge">Management Console</span>
                    <h1 class="hero-title mb-2">Mana Dispensary Admin Dashboard</h1>
                    <p class="hero-text mb-0">
                        Monitor patients, RCH activity, revenue, stock performance, and quick administrative actions from one place.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="hero-user-card">
                    <div class="small text-muted">Welcome back</div>
                    <div class="fw-bold fs-5">{{ auth()->user()->name }}</div>
                    <div class="small text-muted">{{ now()->format('d M Y • h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- PRIMARY METRICS --}}
    <div class="section-label mb-3">Patient & Service Overview</div>
    <div class="row g-4 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="metric-card metric-primary h-100">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-label">General Patients</div>
                <div class="metric-value">
                    <span class="counter" data-target="{{ $total_patients }}">0</span>
                </div>
                <div class="metric-note">Non-RCH registered patients</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="metric-card metric-info h-100">
                <div class="metric-icon">
                    <i class="fas fa-baby"></i>
                </div>
                <div class="metric-label">RCH Patients</div>
                <div class="metric-value">
                    <span class="counter" data-target="{{ $total_rch_patients }}">0</span>
                </div>
                <div class="metric-note">Registered RCH patients</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="metric-card metric-success h-100">
                <div class="metric-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="metric-label">Today's OPD</div>
                <div class="metric-value">
                    <span class="counter" data-target="{{ $today_opd }}">0</span>
                </div>
                <div class="metric-note">Patients attended today</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="metric-card metric-teal h-100">
                <div class="metric-icon">
                    <i class="fas fa-vials"></i>
                </div>
                <div class="metric-label">Today's Lab Tests</div>
                <div class="metric-value">
                    <span class="counter" data-target="{{ $today_lab_tests }}">0</span>
                </div>
                <div class="metric-note">Completed tests today</div>
            </div>
        </div>
    </div>

    {{-- REVENUE & STOCK --}}
    <div class="section-label mb-3">Revenue & Inventory Overview</div>
    <div class="row g-4 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="metric-card metric-warning h-100">
                <div class="metric-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="metric-label">Today's Revenue</div>
                <div class="metric-value">
                    Tsh<span class="counter" data-target="{{ $today_revenue }}">0</span>
                </div>
                <div class="metric-note">Clinical payments received today</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="metric-card metric-danger h-100">
                <div class="metric-icon">
                    <i class="fas fa-pills"></i>
                </div>
                <div class="metric-label">Today's Pharmacy Sales</div>
                <div class="metric-value">
                    Tsh<span class="counter" data-target="{{ $today_pharmacy_sales }}">0</span>
                </div>
                <div class="metric-note">OTC and pharmacy sales today</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="metric-card metric-dark h-100">
                <div class="metric-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="metric-label">All Time Revenue</div>
                <div class="metric-value">
                    Tsh<span class="counter" data-target="{{ $total_revenue_all_time }}">0</span>
                </div>
                <div class="metric-note">Combined total revenue</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="metric-card metric-stock h-100">
                <div class="metric-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="metric-label">Stock Value</div>
                <div class="metric-value">
                    Tsh<span class="counter" data-target="{{ $total_stock_value }}">0</span>
                </div>
                <div class="metric-note">Current inventory valuation</div>
            </div>
        </div>
    </div>

    {{-- SECONDARY HEALTH / INVENTORY --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="mini-stat-card h-100">
                <div class="mini-stat-title">Low Stock Items</div>
                <div class="mini-stat-value text-warning">
                    <span class="counter" data-target="{{ $low_stock_items }}">0</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="mini-stat-card h-100">
                <div class="mini-stat-title">Expired Medicines</div>
                <div class="mini-stat-value text-danger">
                    <span class="counter" data-target="{{ $expired_medicines }}">0</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="mini-stat-card h-100">
                <div class="mini-stat-title">Today's Medicines Issued</div>
                <div class="mini-stat-value text-success">
                    <span class="counter" data-target="{{ $today_medicines_issued }}">0</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('admin.profit-report') }}" class="mini-stat-card h-100 text-decoration-none d-block hover-card-link">
                <div class="mini-stat-title">Profit & Loss Report</div>
                <div class="mini-stat-value text-primary">Open</div>
                <div class="small text-muted mt-2">View detailed financial performance</div>
            </a>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="dashboard-panel mb-4">
        <div class="panel-header">
            <div>
                <h5 class="panel-title mb-1">Quick Admin Actions</h5>
                <p class="panel-subtitle mb-0">Fast shortcuts for daily management tasks</p>
            </div>
        </div>

        <div class="panel-body">
            <div class="row g-3">

                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <a href="{{ route('users.index') }}" class="action-card action-primary text-decoration-none">
                        <i class="bi bi-person-plus-fill"></i>
                        <span>Add New User</span>
                    </a>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <a href="{{ route('admin.roles') }}" class="action-card action-info text-decoration-none">
                        <i class="bi bi-shield-check"></i>
                        <span>Manage Roles</span>
                    </a>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <a href="{{ route('lab-tests.index') }}" class="action-card action-success text-decoration-none">
                        <i class="bi bi-eyedropper"></i>
                        <span>Add Lab Test</span>
                    </a>
                </div>

                @can('manage procedures')
                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <a href="{{ route('admin.procedures.index') }}" class="action-card action-green text-decoration-none">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Procedure Master</span>
                    </a>
                </div>
                @endcan

                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <a href="{{ route('medicines.index') }}" class="action-card action-warning text-decoration-none">
                        <i class="bi bi-capsule-pill"></i>
                        <span>Add Medicine</span>
                    </a>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <a href="{{ route('store-items.index') }}" class="action-card action-secondary text-decoration-none">
                        <i class="bi bi-box-seam"></i>
                        <span>Store Items</span>
                    </a>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <a href="{{ route('wards.index') }}" class="action-card action-danger text-decoration-none">
                        <i class="bi bi-building"></i>
                        <span>Ward / Bed</span>
                    </a>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <a href="{{ route('suppliers.index') }}" class="action-card action-dark text-decoration-none">
                        <i class="bi bi-truck"></i>
                        <span>Suppliers</span>
                    </a>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <a href="{{ route('admin.medicine.logs') }}" class="action-card action-purple text-decoration-none">
                        <i class="bi bi-journal-medical"></i>
                        <span>Medicine Logs</span>
                    </a>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <a href="{{ route('admin.revenue') }}" class="action-card action-teal text-decoration-none">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Revenue History</span>
                    </a>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <a href="{{ route('patients.history.index') }}" class="action-card action-primary-light text-decoration-none">
                        <i class="bi bi-clock-history"></i>
                        <span>Patient History</span>
                    </a>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <a href="{{ route('patients.rch.history.index') }}" class="action-card action-info-light text-decoration-none">
                        <i class="bi bi-clock-history"></i>
                        <span>RCH History</span>
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="dashboard-panel h-100">
                <div class="panel-header">
                    <div>
                        <h5 class="panel-title mb-1">Revenue Trend</h5>
                        <p class="panel-subtitle mb-0">Last 7 days revenue performance</p>
                    </div>
                </div>
                <div class="panel-body">
                    <canvas id="revenueChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-panel h-100">
                <div class="panel-header">
                    <div>
                        <h5 class="panel-title mb-1">OPD Patients Trend</h5>
                        <p class="panel-subtitle mb-0">Last 7 days patient flow</p>
                    </div>
                </div>
                <div class="panel-body">
                    <canvas id="opdChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- TOP SELLING MEDICINES --}}
    <div class="dashboard-panel">
        <div class="panel-header">
            <div>
                <h5 class="panel-title mb-1">Top Selling Medicines</h5>
                <p class="panel-subtitle mb-0">Best performing medicines this month</p>
            </div>
            <a href="{{ route('medicines.index') }}" class="btn btn-sm btn-primary-soft">View All Medicines</a>
        </div>

        <div class="panel-body">
            @if($top_medicines->count() > 0)
                <div class="table-responsive">
                    <table class="table align-middle custom-table mb-0">
                        <thead>
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
                                    <td>
                                        <div class="fw-semibold">{{ $item->medicine->medicine_name }}</div>
                                    </td>
                                    <td>
                                        <span class="table-badge">
                                            {{ $item->medicine->category ?? 'General' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="qty-pill">{{ $item->total }}</span>
                                    </td>
                                    <td class="fw-semibold text-success">
                                        Tsh{{ number_format($item->total * ($item->medicine->selling_price ?? 0), 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-bar-chart-line"></i>
                    <p class="mb-0">No sales recorded this month yet.</p>
                </div>
            @endif
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx1 = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: @json($revenue_chart['labels']),
            datasets: [{
                label: 'Daily Revenue (Tsh)',
                data: @json($revenue_chart['data']),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.10)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#2563eb'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.15)'
                    }
                }
            }
        }
    });

    const ctx2 = document.getElementById('opdChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: @json($opd_chart['labels']),
            datasets: [{
                label: 'OPD Patients',
                data: @json($opd_chart['data']),
                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                borderRadius: 10,
                maxBarThickness: 34
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.15)'
                    }
                }
            }
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const counters = document.querySelectorAll('.counter');
        const speed = 200;

        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');

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

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        updateCount();
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            const card = counter.closest('.metric-card, .mini-stat-card');
            if (card) observer.observe(card);
        });
    });
</script>

<style>
    :root {
        --dash-bg: #f4f7fb;
        --dash-card: #ffffff;
        --dash-text: #1f2937;
        --dash-muted: #6b7280;
        --dash-border: #e5e7eb;
        --dash-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        --dash-shadow-hover: 0 18px 40px rgba(15, 23, 42, 0.12);
        --dash-radius: 1.25rem;

        --primary-soft: linear-gradient(135deg, #4f46e5, #6366f1);
        --success-soft: linear-gradient(135deg, #059669, #10b981);
        --info-soft: linear-gradient(135deg, #0284c7, #38bdf8);
        --warning-soft: linear-gradient(135deg, #d97706, #f59e0b);
        --danger-soft: linear-gradient(135deg, #dc2626, #ef4444);
        --purple-soft: linear-gradient(135deg, #7c3aed, #a855f7);
        --dark-soft: linear-gradient(135deg, #111827, #374151);
    }

    body {
        background: var(--dash-bg);
    }

    .container-fluid.py-4 {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
    }

    .h3.text-gray-800.fw-bold {
        font-size: 1.9rem;
        color: var(--dash-text) !important;
        letter-spacing: -0.02em;
    }

    .text-muted {
        color: var(--dash-muted) !important;
    }

    .card {
        border: 1px solid rgba(229, 231, 235, 0.8) !important;
        border-radius: var(--dash-radius) !important;
        background: var(--dash-card);
        box-shadow: var(--dash-shadow) !important;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: var(--dash-shadow-hover) !important;
    }

    .shadow,
    .shadow-sm {
        box-shadow: var(--dash-shadow) !important;
    }

    .border-left-primary,
    .border-left-success,
    .border-left-info,
    .border-left-warning,
    .border-left-danger,
    .border-left-purple,
    .border-left-dark {
        border-left-width: 0 !important;
        position: relative;
    }

    .border-left-primary::before,
    .border-left-success::before,
    .border-left-info::before,
    .border-left-warning::before,
    .border-left-danger::before,
    .border-left-purple::before,
    .border-left-dark::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        border-radius: 0 12px 12px 0;
    }

    .border-left-primary::before { background: #4f46e5; }
    .border-left-success::before { background: #10b981; }
    .border-left-info::before { background: #0ea5e9; }
    .border-left-warning::before { background: #f59e0b; }
    .border-left-danger::before { background: #ef4444; }
    .border-left-purple::before { background: #8b5cf6; }
    .border-left-dark::before { background: #374151; }

    .card.h-100.py-2 {
        min-height: 150px;
    }

    .card .card-body {
        padding: 1.25rem 1.25rem;
    }

    .text-xs {
        font-size: 0.74rem !important;
        letter-spacing: 0.08em;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    .h5.mb-0.font-weight-bold.text-gray-800 {
        font-size: 1.6rem;
        color: var(--dash-text) !important;
        margin-top: 0.4rem;
    }

    .fa-2x.text-gray-300 {
        color: #cbd5e1 !important;
        opacity: 0.95;
    }

    .card-header {
        border-bottom: 1px solid var(--dash-border) !important;
        background: #fff !important;
        padding: 1rem 1.25rem !important;
    }

    .card-header.bg-primary,
    .card-header.bg-success {
        color: #fff !important;
        border-bottom: none !important;
    }

    .card-header.bg-primary {
        background: var(--primary-soft) !important;
    }

    .card-header.bg-success {
        background: var(--success-soft) !important;
    }

    .quick-actions-card,
    .chart-card,
    .table-card {
        border-radius: 1.5rem !important;
    }

    .btn {
        border-radius: 0.95rem !important;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .btn-outline-primary,
    .btn-outline-info,
    .btn-outline-success,
    .btn-outline-warning,
    .btn-outline-secondary,
    .btn-outline-danger,
    .btn-outline-dark,
    .btn-outline-purple,
    .btn-outline-teal {
        border-width: 1.5px;
        background: #fff;
    }

    .btn-outline-primary:hover,
    .btn-outline-info:hover,
    .btn-outline-success:hover,
    .btn-outline-warning:hover,
    .btn-outline-secondary:hover,
    .btn-outline-danger:hover,
    .btn-outline-dark:hover,
    .btn-outline-purple:hover,
    .btn-outline-teal:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    .btn-outline-primary {
        border-color: #4f46e5;
        color: #4f46e5;
    }
    .btn-outline-primary:hover {
        background: #4f46e5;
        color: #fff;
    }

    .btn-outline-info {
        border-color: #0ea5e9;
        color: #0ea5e9;
    }
    .btn-outline-info:hover {
        background: #0ea5e9;
        color: #fff;
    }

    .btn-outline-success {
        border-color: #10b981;
        color: #10b981;
    }
    .btn-outline-success:hover {
        background: #10b981;
        color: #fff;
    }

    .btn-outline-warning {
        border-color: #f59e0b;
        color: #f59e0b;
    }
    .btn-outline-warning:hover {
        background: #f59e0b;
        color: #fff;
    }

    .btn-outline-secondary {
        border-color: #64748b;
        color: #64748b;
    }
    .btn-outline-secondary:hover {
        background: #64748b;
        color: #fff;
    }

    .btn-outline-danger {
        border-color: #ef4444;
        color: #ef4444;
    }
    .btn-outline-danger:hover {
        background: #ef4444;
        color: #fff;
    }

    .btn-outline-dark {
        border-color: #111827;
        color: #111827;
    }
    .btn-outline-dark:hover {
        background: #111827;
        color: #fff;
    }

    .btn-outline-purple {
        border-color: #8b5cf6;
        color: #8b5cf6;
    }
    .btn-outline-purple:hover {
        background: #8b5cf6;
        color: #fff;
    }

    .btn-outline-teal {
        border-color: #0f766e;
        color: #0f766e;
    }
    .btn-outline-teal:hover {
        background: #0f766e;
        color: #fff;
    }

    .quick-admin-actions .btn,
    .card-body .row.g-3 .btn {
        min-height: 104px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.35rem;
        border-radius: 1rem !important;
    }

    .quick-admin-actions .btn i,
    .card-body .row.g-3 .btn i {
        font-size: 1.5rem !important;
        margin-bottom: 0.25rem;
    }

    canvas {
        max-height: 320px !important;
    }

    .table {
        margin-bottom: 0;
        vertical-align: middle;
    }

    .table thead th {
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #475569;
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding-top: 0.9rem;
        padding-bottom: 0.9rem;
    }

    .table td {
        padding-top: 0.95rem;
        padding-bottom: 0.95rem;
        border-color: #eef2f7 !important;
    }

    .table-hover tbody tr:hover {
        background: #f8fbff;
    }

    .badge {
        border-radius: 999px;
        padding: 0.45rem 0.8rem;
        font-weight: 700;
    }

    .hover-lift {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: var(--dash-shadow-hover) !important;
    }

    .m-0.font-weight-bold.text-primary,
    .m-0.font-weight-bold.text-success {
        font-size: 1rem;
        letter-spacing: -0.01em;
    }

    .card-body p.text-muted.text-center.py-4 {
        margin-bottom: 0;
        font-size: 0.95rem;
    }

    @media (max-width: 991.98px) {
        .h3.text-gray-800.fw-bold {
            font-size: 1.5rem;
        }

        .card.h-100.py-2 {
            min-height: 135px;
        }
    }

    @media (max-width: 767.98px) {
        .container-fluid.py-4 {
            padding-left: 0.9rem;
            padding-right: 0.9rem;
        }

        .d-flex.justify-content-between.align-items-center.mb-4 {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 0.5rem;
        }

        .h5.mb-0.font-weight-bold.text-gray-800 {
            font-size: 1.3rem;
        }

        .card-body .row.g-3 .btn {
            min-height: 96px;
        }
    }
</style>