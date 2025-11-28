{{-- resources/views/components/sidebar.blade.php --}}
@php
    $user = auth()->user();
@endphp

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav d-lg-none">
    <div class="nav-items-wrapper">
        <div class="nav-items">

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            @can('view patients')
            <a href="{{ route('patients.index') }}" class="nav-item {{ request()->is('patients*') ? 'active' : '' }}">
                <i class="bi bi-person-heart"></i>
                <span>Patients</span>
            </a>
            @endcan

            @canany(['view visits', 'create visit'])
            <a href="{{ route('doctor.opd') }}" class="nav-item {{ request()->is('doctor/opd*') || request()->is('visits*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Today OPD</span>
            </a>
            @endcanany

            @can('access billing')
            <a href="{{ route('billing.index') }}" class="nav-item {{ request()->is('billing*') ? 'active' : '' }}">
                <i class="bi bi-credit-card-2-front-fill text-danger"></i>
                <span>Payment</span>
            </a>
            @endcan

            @can('issue medicine')
            <a href="{{ route('pharmacy.index') }}" class="nav-item {{ request()->is('pharmacy*') ? 'active' : '' }}">
                <i class="bi bi-capsule"></i>
                <span>Pharmacy</span>
            </a>
            @endcan

            @can('enter lab results')
            <a href="{{ route('lab.index') }}" class="nav-item {{ request()->is('lab*') ? 'active' : '' }}">
                <i class="bi bi-vial"></i>
                <span>Lab</span>
            </a>
            @endcan

            @can('issue store items')
            <a href="{{ route('store.purchase.index') }}" class="nav-item {{ request()->is('store*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>Store</span>
            </a>
            @endcan

            @can('manage users')
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->is('users*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i>
                <span>Users</span>
            </a>
            @endcan

            @can('manage settings')
            <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->is('admin/settings*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i>
                <span>Settings</span>
            </a>
            @endcan

        </div>
    </div>
</div>

<!-- Desktop Sidebar -->
<div class="left-side-menu d-none d-lg-block">
    <div class="slimscroll-menu">
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">

                <li class="menu-title text-muted small text-uppercase">Main Menu</li>

                <li>
                    <a href="{{ route('dashboard') }}" class="waves-effect {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 text-primary"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @can('view patients')
                <li>
                    <a href="{{ route('patients.index') }}" class="waves-effect {{ request()->is('patients*') ? 'active' : '' }}">
                        <i class="bi bi-person-heart text-success"></i>
                        <span>Patients</span>
                    </a>
                </li>
                @endcan

                @canany(['view visits', 'create visit'])
                <li>
                    <a href="{{ route('doctor.opd') }}" class="waves-effect {{ request()->is('doctor/opd*') || request()->is('visits*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check text-info"></i>
                        <span>Today OPD</span>
                    </a>
                </li>
                @endcanany

                @can('access billing')
                <li>
                    <a href="{{ route('billing.index') }}" class="waves-effect {{ request()->is('billing*') ? 'active' : '' }}">
                        <i class="bi bi-credit-card-2-front-fill text-danger fs-5"></i>
                        <span class="fw-bold">Payment & Billing</span>
                    </a>
                </li>
                @endcan

                @can('issue medicine')
                <li>
                    <a href="{{ route('pharmacy.index') }}" class="waves-effect {{ request()->is('pharmacy*') ? 'active' : '' }}">
                        <i class="bi bi-capsule text-warning"></i>
                        <span>Pharmacy</span>
                    </a>
                </li>
                @endcan

                @can('enter lab results')
                <li>
                    <a href="{{ route('lab.index') }}" class="waves-effect {{ request()->is('lab*') ? 'active' : '' }}">
                        <i class="bi bi-eyedropper text-purple"></i>
                        <span>Laboratory</span>
                    </a>
                </li>
                @endcan

                @can('issue store items')
                <li>
                    <a href="{{ route('store.purchase.index') }}" class="waves-effect {{ request()->is('store*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam text-secondary"></i>
                        <span>Store & Purchase</span>
                    </a>
                </li>
                @endcan

                @canany(['manage users', 'manage settings'])
                <li class="menu-title text-muted small mt-3">Administration</li>
                @endcanany

                @can('manage users')
                <li>
                    <a href="{{ route('users.index') }}" class="waves-effect {{ request()->is('users*') ? 'active' : '' }}">
                        <i class="bi bi-person-gear text-warning"></i>
                        <span>Manage Users</span>
                    </a>
                </li>
                @endcan

                @can('manage settings')
                <li>
                    <a href="{{ route('admin.settings') }}" class="waves-effect {{ request()->is('admin/settings*') ? 'active' : '' }}">
                        <i class="bi bi-gear-fill text-secondary"></i>
                        <span>Hospital Settings</span>
                    </a>
                </li>
                @endcan

            </ul>
        </div>
    </div>
</div>