{{-- resources/views/dashboard.blade.php --}}
@extends('components.main-layout')
@section('title', 'Dashboard')

@section('content')
<style>
    body.modal-open {
        overflow: visible !important;
        padding-right: 0 !important;
    }
    .modal-backdrop {
        display: none !important;
    }

    /* Admin Panel Button - Top Right Corner */
    .admin-panel-float {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 1055;
        border: none;
        border-radius: 12px;
        padding: 0.85rem 1.5rem;
        font-weight: 600;
        font-size: 1rem;
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .admin-panel-float:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(220, 53, 69, 0.4) !important;
    }
    .admin-panel-float i {
        font-size: 1.2rem;
    }

    /* Hover effect for quick action buttons */
    .hover-shadow {
        transition: all 0.25s ease;
        border: 1px solid #e0e0e0 !important;
    }
    .hover-shadow:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        border-color: #ccc !important;
    }
</style>

<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Admin Panel Button - Top Right (Only for Admins) -->
    @if(auth()->user()->hasRole('Admin'))
        <a href="{{ route('admin.dashboard') }}" class="btn btn-danger admin-panel-float text-white">
            <i class="bi bi-shield-lock-fill"></i>
            Admin Panel
        </a>
    @endif

    <!-- Personalized Welcome Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h1 class="display-6 fw-bold text-dark mb-2">
                        Good {{ now()->format('A') == 'AM' ? 'Morning' : 'Afternoon' }},
                        {{ Str::title(auth()->user()->name) }}!
                    </h1>
                    <p class="lead text-muted mb-3">
                        Today is {{ now()->format('l, j F Y') }}
                    </p>

                    <div class="row g-4 text-center text-md-start mb-4">
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Role</small>
                            <strong class="text-dark">{{ auth()->user()->roles->first()->name ?? 'Staff' }}</strong>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Department</small>
                            <strong class="text-dark">{{ auth()->user()->department ?? '—' }}</strong>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Employee ID</small>
                            <strong class="text-dark">{{ auth()->user()->employee_code }}</strong>
                        </div>
                    </div>

                    <hr class="border-secondary-subtle">

                    <p class="fs-5 text-dark mb-0 fw-medium">
                        @php
                            $wishes = [
                                "Have a peaceful and productive duty today!",
                                "Wishing you a smooth and meaningful shift.",
                                "Thank you for your care and dedication.",
                                "May your day be filled with compassion and calm.",
                                "You're making a real difference — keep shining!",
                                "Wishing you strength and kindness throughout your shift."
                            ];
                            echo $wishes[array_rand($wishes)];
                        @endphp
                    </p>
                </div>

                <div class="col-lg-4 text-center">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mx-auto"
                         style="width: 140px; height: 140px;">
                        <i class="bi bi-person-heart text-muted opacity-75" style="font-size: 4.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- Live Clock -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center p-4">
                <i class="bi bi-clock text-muted mb-3" style="font-size: 2.8rem;"></i>
                <h6 class="text-muted small text-uppercase tracking-wider">Current Time</h6>
                <h3 class="fw-bold text-dark mb-1" id="liveClock">{{ now()->format('h:i A') }}</h3>
                <small class="text-muted">{{ now()->format('D, d M Y') }}</small>
            </div>
        </div>

        @if(auth()->user()->can('view visits') || auth()->user()->can('prescribe medicine'))
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center p-4">
                <i class="bi bi-people text-muted mb-3" style="font-size: 2.8rem;"></i>
                <h6 class="text-muted small text-uppercase tracking-wider">Today's Patients</h6>
                <h3 class="fw-bold text-dark mb-0 {{ $todayPatients >= 20 ? 'text-warning' : '' }}">
                    {{ $todayPatients ?? 0 }}
                </h3>
            </div>
        </div>
        @endif

        @if(auth()->user()->can('access billing'))
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center p-4">
                <i class="bi bi-wallet2 text-muted mb-3" style="font-size: 2.8rem;"></i>
                <h6 class="text-muted small text-uppercase tracking-wider">Today's Revenue</h6>
                <h4 class="fw-bold text-dark mb-0">Tsh {{ number_format($todayRevenue ?? 0) }}</h4>
            </div>
        </div>
        @endif

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center p-4">
                <i class="bi bi-sunrise text-muted mb-3" style="font-size: 2.8rem;"></i>
                <h6 class="text-muted small text-uppercase tracking-wider">Your Shift</h6>
                <h5 class="fw-bold text-dark mb-1">8:00 AM – 5:00 PM</h5>
                <span class="badge bg-success-subtle text-success px-3 py-2">Active Duty</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="mb-4 text-dark fw-semibold">Quick Access</h5>
            <div class="row g-3">

                @can('view patients')
                <div class="col-6 col-md-4 col-xl-3">
                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4 hover-shadow">
                        <i class="bi bi-person-heart fs-3 mb-2"></i>
                        <span>Patients</span>
                    </a>
                </div>
                @endcan

                @canany(['view visits', 'create visit', 'prescribe medicine'])
                <div class="col-6 col-md-4 col-xl-3">
                    <a href="{{ route('doctor.opd') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4 hover-shadow">
                        <i class="bi bi-calendar-check fs-3 mb-2"></i>
                        <span>Today OPD</span>
                    </a>
                </div>
                @endcanany

                @can('access billing')
                <div class="col-6 col-md-4 col-xl-3">
                    <a href="{{ route('billing.index') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4 hover-shadow">
                        <i class="bi bi-receipt fs-3 mb-2"></i>
                        <span>Billing</span>
                    </a>
                </div>
                @endcan

                @can('issue medicine')
                <div class="col-6 col-md-4 col-xl-3">
                    <a href="{{ route('pharmacy.index') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4 hover-shadow">
                        <i class="bi bi-capsule-pill fs-3 mb-2"></i>
                        <span>Pharmacy</span>
                    </a>
                </div>
                @endcan

                @can('enter lab results')
                <div class="col-6 col-md-4 col-xl-3">
                    <a href="{{ route('lab.index') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4 hover-shadow">
                        <i class="bi bi-vial fs-3 mb-2"></i>
                        <span>Laboratory</span>
                    </a>
                </div>
                @endcan

            </div>
        </div>
    </div>
</div>

{{-- Live Clock Script --}}
<script>
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        document.getElementById('liveClock').textContent = timeString;
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>

{{-- Final fix: Prevent modal scroll lock & backdrop issues --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });

    $(document).on('hidden.bs.modal', function () {
        if ($('.modal.show').length === 0) {
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            $('body').css({ overflow: '', paddingRight: '' });
        }
    });
</script>
@endsection