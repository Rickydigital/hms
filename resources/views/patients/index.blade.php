@extends('components.main-layout')
@section('title', 'Patients Management')
@section('content')

<div class="container-fluid py-3 py-md-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <div>
            <h4 class="mb-1 text-primary fw-bold">
                <i class="bi bi-person-heart-fill me-2"></i> Patients
            </h4>
            <p class="text-muted mb-0 small">Search, register & manage all patients</p>
        </div>
        <button type="button" class="btn btn-primary d-none d-sm-flex align-items-center shadow-sm"
                data-bs-toggle="modal" data-bs-target="#registerPatientModal">
            <i class="bi bi-person-plus-fill me-2"></i> Register Patient
        </button>
    </div>

<div class="alert alert-success d-flex align-items-center rounded-4 mb-4 shadow-sm">
    <i class="bi bi-people-fill fs-3 me-3"></i>
    <div>
        <strong>{{ \App\Models\Visit::whereDate('visit_date', today())->where('status', 'in_opd')->count() }}</strong> 
        patients waiting in OPD today
    </div>
    <div class="ms-auto">
        <span class="badge bg-white text-success fs-6 px-3 py-2 rounded-pill">
            Live Queue
        </span>
    </div>
</div>

    <!-- Search Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('patients.index') }}" method="GET">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="form-control border-start-0 rounded-end-3" 
                           @if(request('search')) border-primary @endif" 
                           placeholder="Search by ID, Name or Phone..." autofocus>
                    @if(request('search'))
                        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary rounded-start-0">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Patients Grid -->
    <div class="row g-3 g-md-4">
        @forelse($patients as $patient)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative hover-lift">
                    <!-- Status Badge -->
                    <div class="position-absolute top-0 start-0 p-2 z-3">
                        @if($patient->isExpired())
                            <span class="badge bg-danger rounded-pill px-3 py-2 small fw-medium">
                                <i class="bi bi-exclamation-triangle"></i> Expired
                            </span>
                        @elseif($patient->is_active)
                            <span class="badge bg-success rounded-pill px-3 py-2 small fw-medium">
                                <i class="bi bi-check-circle"></i> Active
                            </span>
                        @else
                            <span class="badge bg-warning rounded-pill px-3 py-2 small fw-medium">
                                <i class="bi bi-clock-history"></i> Inactive
                            </span>
                        @endif
                    </div>

                    <div class="card-body p-4 d-flex flex-column text-center">
                        <div class="avatar-lg mx-auto mb-3 bg-soft-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi {{ $patient->gender == 'Male' ? 'bi-person' : 'bi-person-fill' }} text-primary" style="font-size: 2.8rem;"></i>
                        </div>

                        <h5 class="card-title mb-1 fw-bold">{{ $patient->name }}</h5>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-calendar-event"></i> Age: {{ $patient->age }} yrs
                        </p>
                        <p class="text-primary small fw-bold mb-3">
                            {{ $patient->patient_id }}
                        </p>

                        <div class="d-flex gap-2 justify-content-center mt-auto">
    <button type="button" class="btn btn-outline-info btn-sm rounded-pill"
            data-bs-toggle="modal" data-bs-target="#viewPatientModal"
            onclick='showPatient({!! json_encode($patient) !!})'>
        <i class="bi bi-eye"></i>
    </button>

    @if($patient->is_active && !$patient->isExpired())
        @if(!$patient->visits()->whereDate('visit_date', today())->exists())
            <button type="button" class="btn btn-success btn-sm rounded-pill"
                    data-bs-toggle="modal" data-bs-target="#createVisitModal"
                    onclick="openVisitModal({{ $patient->id }}, '{{ $patient->name }}', '{{ $patient->patient_id }}')">
                <i class="bi bi-file-medical"></i> Start Visit
            </button>
        @else
            <span class="btn btn-success btn-sm rounded-pill disabled">
                <i class="bi bi-check2-all"></i> In OPD
            </span>
        @endif
    @else
        <button type="button" class="btn btn-outline-success btn-sm rounded-pill"
                onclick="reactivatePatient({{ $patient->id }})">
            <i class="bi bi-arrow-repeat"></i> Reactivate
        </button>
    @endif
</div>

                        <small class="text-muted mt-2">
                            Expires: {{ $patient->expiry_date->format('d M Y') }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 my-4">
                    <i class="bi bi-person-x text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                    <h5 class="text-muted mb-2">No patients found</h5>
                    <p class="text-muted small">Start by registering your first patient</p>
                    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#registerPatientModal">
                        <i class="bi bi-person-plus me-2"></i> Register First Patient
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $patients->links() }}
    </div>
</div>

<!-- Floating Button Mobile -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <button class="btn btn-primary rounded-circle shadow-lg d-sm-none p-3 lh-1"
            data-bs-toggle="modal" data-bs-target="#registerPatientModal"
            style="width: 56px; height: 56px;">
        <i class="bi bi-plus fs-4"></i>
    </button>
</div>

@include('patients.modals.register')
@include('patients.modals.view')
@include('patients.modals.visit')
<style>
.hover-lift { transition: all .3s cubic-bezier(.34,1.56,.64,1); }
.hover-lift:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0,0,0,.1)!important;
}
.avatar-lg { width:5.5rem; height:5.5rem; }
.bg-soft-primary { background-color:rgba(13,110,253,.15)!important; }
</style>

<script>
function reactivatePatient(id) {
    if(!confirm('Reactivate this patient card?')) return;

    fetch(`/patients/${id}/reactivate`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}
</script>
@endsection