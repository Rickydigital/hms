@extends('components.main-layout')
@section('title', 'Admin Dashboard • Mana Dispensary')

@section('content')
<div class="dashboard-page container-fluid py-4">

    <!-- HERO HEADER -->
    <div class="dashboard-hero mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="hero-badge mb-2">
                    <i class="fas fa-hospital-alt me-2"></i>Mana Dispensary Administration
                </div>
                <h1 class="dashboard-title mb-1">Admin Dashboard</h1>
                <p class="dashboard-subtitle mb-0">
                    Welcome back, <strong>{{ auth()->user()->name }}</strong>. Here is today’s operational overview.
                </p>
            </div>
            <div class="hero-date-box">
                <div class="small text-muted">Today</div>
                <div class="fw-bold">{{ now()->format('l, d M Y') }}</div>
            </div>
        </div>
    </div>

    <!-- STATS -->
    <div class="row g-4 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-primary">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-label">General Patients</div>
                <div class="stat-value">
                    <span class="counter" data-target="{{ $total_patients }}">0</span>
                </div>
                <div class="stat-foot">Total registered patients</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-info">
                <div class="stat-icon"><i class="fas fa-baby"></i></div>
                <div class="stat-label">RCH Patients</div>
                <div class="stat-value">
                    <span class="counter" data-target="{{ $total_rch_patients }}">0</span>
                </div>
                <div class="stat-foot">Maternal and child care records</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-success">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-label">Today's OPD</div>
                <div class="stat-value">
                    <span class="counter" data-target="{{ $today_opd }}">0</span>
                </div>
                <div class="stat-foot">Outpatient visits today</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-warning">
                <div class="stat-icon"><i class="fas fa-boxes"></i></div>
                <div class="stat-label">Stock Value</div>
                <div class="stat-value">
                    Tsh <span class="counter" data-target="{{ $total_stock_value }}">0</span>
                </div>
                <div class="stat-foot">Estimated inventory worth</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-orange">
                <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-label">Today's Revenue</div>
                <div class="stat-value">
                    Tsh <span class="counter" data-target="{{ $today_revenue }}">0</span>
                </div>
                <div class="stat-foot">Collected across services</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-danger">
                <div class="stat-icon"><i class="fas fa-pills"></i></div>
                <div class="stat-label">Today's Pharmacy Sales</div>
                <div class="stat-value">
                    Tsh <span class="counter" data-target="{{ $today_pharmacy_sales }}">0</span>
                </div>
                <div class="stat-foot">Medicine sales today</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-purple">
                <div class="stat-icon"><i class="fas fa-cash-register"></i></div>
                <div class="stat-label">Total Pharmacy Sales</div>
                <div class="stat-value">
                    Tsh <span class="counter" data-target="{{ $total_pharmacy_sales }}">0</span>
                </div>
                <div class="stat-foot">All-time pharmacy income</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-dark">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-label">All Time Revenue</div>
                <div class="stat-value">
                    Tsh <span class="counter" data-target="{{ $total_revenue_all_time }}">0</span>
                </div>
                <div class="stat-foot">Cumulative facility revenue</div>
            </div>
        </div>

    </div>

    <!-- FEATURE SHORTCUTS -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="dashboard-card h-100">
                <div class="card-head">
                    <div>
                        <h5 class="mb-1">Quick Admin Actions</h5>
                        <p class="text-muted mb-0 small">Jump quickly to important management sections</p>
                    </div>
                </div>

                <div class="card-body pt-0">
                    <div class="row g-3">

                        <div class="col-md-3 col-6">
                            <a href="{{ route('users.index') }}" class="quick-action qa-primary">
                                <i class="bi bi-person-plus-fill"></i>
                                <span>Add New User</span>
                            </a>
                        </div>

                        <div class="col-md-3 col-6">
                            <a href="{{ route('admin.roles') }}" class="quick-action qa-info">
                                <i class="bi bi-shield-check"></i>
                                <span>Manage Roles</span>
                            </a>
                        </div>

                        <div class="col-md-3 col-6">
                            <a href="{{ route('lab-tests.index') }}" class="quick-action qa-success">
                                <i class="bi bi-eyedropper"></i>
                                <span>Add Lab Test</span>
                            </a>
                        </div>

                        @can('manage procedures')
                        <div class="col-md-3 col-6">
                            <a href="{{ route('admin.procedures.index') }}" class="quick-action qa-green">
                                <i class="bi bi-clipboard-check"></i>
                                <span>Procedure Master</span>
                            </a>
                        </div>
                        @endcan

                        <div class="col-md-3 col-6">
                            <a href="{{ route('medicines.index') }}" class="quick-action qa-warning">
                                <i class="bi bi-capsule-pill"></i>
                                <span>Add Medicine</span>
                            </a>
                        </div>

                        <div class="col-md-3 col-6">
                            <a href="{{ route('store-items.index') }}" class="quick-action qa-secondary">
                                <i class="bi bi-box-seam"></i>
                                <span>Add Store Item</span>
                            </a>
                        </div>

                        <div class="col-md-3 col-6">
                            <a href="{{ route('wards.index') }}" class="quick-action qa-danger">
                                <i class="bi bi-building"></i>
                                <span>Add Ward / Bed</span>
                            </a>
                        </div>

                        <div class="col-md-3 col-6">
                            <a href="{{ route('suppliers.index') }}" class="quick-action qa-dark">
                                <i class="bi bi-truck"></i>
                                <span>Add Supplier</span>
                            </a>
                        </div>

                        <div class="col-md-3 col-6">
                            <a href="{{ route('admin.medicine.logs') }}" class="quick-action qa-purple">
                                <i class="bi bi-journal-medical"></i>
                                <span>Medicine Logs</span>
                            </a>
                        </div>

                        <div class="col-md-3 col-6">
                            <a href="{{ route('admin.revenue') }}" class="quick-action qa-teal">
                                <i class="bi bi-graph-up-arrow"></i>
                                <span>Revenue History</span>
                            </a>
                        </div>

                        <div class="col-md-3 col-6">
                            <a href="{{ route('patients.history.index') }}" class="quick-action qa-indigo">
                                <i class="bi bi-clock-history"></i>
                                <span>Patient History</span>
                            </a>
                        </div>

                        <div class="col-md-3 col-6">
                            <a href="{{ route('patients.rch.history.index') }}" class="quick-action qa-sky">
                                <i class="bi bi-clock-history"></i>
                                <span>RCH History</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <a href="{{ route('admin.profit-report') }}" class="text-decoration-none">
                <div class="dashboard-card h-100 report-card">
                    <div class="report-icon">
                        <i class="fas fa-money-bill-trend-up"></i>
                    </div>
                    <h5 class="mb-2 text-dark">Profit & Loss Report</h5>
                    <p class="text-muted mb-4">
                        Open a summarized financial performance report for income, cost, and profit analysis.
                    </p>
                    <div class="report-link">
                        View Profit Details <i class="fas fa-arrow-right ms-2"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="dashboard-card h-100">
                <div class="card-head">
                    <div>
                        <h5 class="mb-1">Revenue Trend</h5>
                        <p class="text-muted mb-0 small">Performance overview for the last 7 days</p>
                    </div>
                </div>
                <div class="chart-wrap">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-card h-100">
                <div class="card-head">
                    <div>
                        <h5 class="mb-1">OPD Patients Trend</h5>
                        <p class="text-muted mb-0 small">Daily outpatient attendance snapshot</p>
                    </div>
                </div>
                <div class="chart-wrap">
                    <canvas id="opdChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- TOP SELLING MEDICINES -->
    <div class="dashboard-card">
        <div class="card-head">
            <div>
                <h5 class="mb-1">Top Selling Medicines</h5>
                <p class="text-muted mb-0 small">This month’s highest-moving medicines</p>
            </div>
            <a href="{{ route('medicines.index') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                View All Medicines
            </a>
        </div>

        <div class="card-body pt-0">
            @if($top_medicines->count() > 0)
                <div class="table-responsive">
                    <table class="table medicine-table align-middle">
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
                                    <td>
                                        <span class="rank-pill">{{ $loop->iteration }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->medicine->medicine_name }}</div>
                                    </td>
                                    <td>
                                        <span class="soft-badge">
                                            {{ $item->medicine->category ?? 'General' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success rounded-pill px-3 py-2">
                                            {{ $item->total }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark">
                                        Tsh{{ number_format($item->total * ($item->medicine->selling_price ?? 0), 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-capsules"></i>
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
                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
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
                backgroundColor: '#10b981',
                borderRadius: 10,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
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
                const increment = Math.max(1, Math.ceil(target / speed));

                if (count < target) {
                    const newCount = Math.min(count + increment, target);
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
            }, { threshold: 0.4 });

            const card = counter.closest('.stat-card');
            if (card) observer.observe(card);
        });
    });
</script>

<style>
    .dashboard-page {
        background: linear-gradient(180deg, #f8fbff 0%, #f4f7fb 100%);
        min-height: 100vh;
    }

    .dashboard-hero {
        background: linear-gradient(135deg, #0f172a, #1e3a8a 55%, #0ea5e9);
        border-radius: 24px;
        padding: 28px;
        color: #fff;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        background: rgba(255,255,255,0.12);
        color: #e2e8f0;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
    }

    .dashboard-title {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .dashboard-subtitle {
        color: rgba(255,255,255,0.85);
        font-size: 0.98rem;
    }

    .hero-date-box {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.18);
        padding: 14px 18px;
        border-radius: 18px;
        min-width: 180px;
        text-align: center;
        backdrop-filter: blur(8px);
    }

    .stat-card {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        padding: 24px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(226, 232, 240, 0.9);
        height: 100%;
        transition: all 0.25s ease;
    }

    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.12);
    }

    .stat-card::after {
        content: "";
        position: absolute;
        top: -30px;
        right: -30px;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: rgba(255,255,255,0.16);
    }

    .stat-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        margin-bottom: 18px;
        color: #fff;
        box-shadow: 0 10px 24px rgba(0,0,0,0.12);
    }

    .stat-label {
        font-size: 0.84rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
        opacity: 0.9;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 1.7rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        margin-bottom: 6px;
    }

    .stat-foot {
        color: #64748b;
        font-size: 0.88rem;
    }

    .stat-primary .stat-icon { background: linear-gradient(135deg, #2563eb, #60a5fa); }
    .stat-info .stat-icon { background: linear-gradient(135deg, #0891b2, #38bdf8); }
    .stat-success .stat-icon { background: linear-gradient(135deg, #059669, #34d399); }
    .stat-warning .stat-icon { background: linear-gradient(135deg, #d97706, #fbbf24); }
    .stat-orange .stat-icon { background: linear-gradient(135deg, #ea580c, #fb923c); }
    .stat-danger .stat-icon { background: linear-gradient(135deg, #dc2626, #f87171); }
    .stat-purple .stat-icon { background: linear-gradient(135deg, #7c3aed, #a78bfa); }
    .stat-dark .stat-icon { background: linear-gradient(135deg, #0f172a, #475569); }

    .dashboard-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.07);
        border: 1px solid #eef2f7;
        padding: 24px;
    }

    .card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .card-head h5 {
        font-weight: 800;
        color: #0f172a;
    }

    .quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 118px;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 700;
        padding: 18px 14px;
        transition: all 0.25s ease;
        border: 1px solid transparent;
    }

    .quick-action i {
        font-size: 1.6rem;
        margin-bottom: 12px;
    }

    .quick-action span {
        font-size: 0.9rem;
        text-align: center;
    }

    .quick-action:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.10);
    }

    .qa-primary { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
    .qa-info { background: #ecfeff; color: #0891b2; border-color: #a5f3fc; }
    .qa-success { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
    .qa-green { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
    .qa-warning { background: #fff7ed; color: #ea580c; border-color: #fed7aa; }
    .qa-secondary { background: #f8fafc; color: #475569; border-color: #cbd5e1; }
    .qa-danger { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
    .qa-dark { background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; }
    .qa-purple { background: #faf5ff; color: #7c3aed; border-color: #ddd6fe; }
    .qa-teal { background: #f0fdfa; color: #0f766e; border-color: #99f6e4; }
    .qa-indigo { background: #eef2ff; color: #4338ca; border-color: #c7d2fe; }
    .qa-sky { background: #f0f9ff; color: #0284c7; border-color: #bae6fd; }

    .report-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: linear-gradient(135deg, #f0fdf4, #ecfeff);
        border: 1px solid #d1fae5;
        transition: all 0.25s ease;
    }

    .report-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 35px rgba(16, 185, 129, 0.14);
    }

    .report-icon {
        width: 68px;
        height: 68px;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #10b981, #34d399);
        color: #fff;
        font-size: 1.55rem;
        margin-bottom: 18px;
        box-shadow: 0 12px 28px rgba(16, 185, 129, 0.25);
    }

    .report-link {
        font-weight: 700;
        color: #059669;
    }

    .chart-wrap {
        position: relative;
        height: 320px;
    }

    .medicine-table thead th {
        background: #f8fafc;
        color: #334155;
        font-size: 0.86rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border: none;
        padding: 16px 14px;
    }

    .medicine-table tbody td {
        padding: 16px 14px;
        border-top: 1px solid #eef2f7;
    }

    .medicine-table tbody tr:hover {
        background: #f8fbff;
    }

    .rank-pill {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 700;
    }

    .soft-badge {
        display: inline-block;
        background: #f1f5f9;
        color: #334155;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #64748b;
    }

    .empty-state i {
        font-size: 2rem;
        margin-bottom: 12px;
        color: #94a3b8;
    }

    @media (max-width: 768px) {
        .dashboard-title {
            font-size: 1.5rem;
        }

        .dashboard-hero,
        .dashboard-card,
        .stat-card {
            border-radius: 18px;
        }

        .chart-wrap {
            height: 260px;
        }
    }
</style>
@endsection