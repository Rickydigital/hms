@extends('components.main-layout')
@section('title', 'Patients Management')
@section('content')

<div class="container-fluid py-3 py-md-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <div>
            <h4 class="mb-1 text-primary fw-bold">Patients</h4>
            <p class="text-muted mb-0 small">Search, register & manage all patients</p>
        </div>
        <button type="button" class="btn btn-primary d-none d-sm-flex align-items-center shadow-sm"
                data-bs-toggle="modal" data-bs-target="#registerPatientModal">
            Register Patient
        </button>
    </div>

    <div class="alert alert-success d-flex align-items-center rounded-4 mb-4 shadow-sm">
        <div>
            <strong>{{ \App\Models\Visit::whereDate('visit_date', today())->where('status', 'in_opd')->count() }}</strong> 
            patients waiting in OPD today
        </div>
        <div class="ms-auto">
            <span class="badge bg-white text-success fs-6 px-3 py-2 rounded-pill">Live Queue</span>
        </div>
    </div>

    <!-- Live Search Bar (Like Pharmacy) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-white border-end-0">Search</span>
                <input type="text" id="patientSearchInput" class="form-control border-start-0 rounded-end-3" 
                       placeholder="Search by Patient Name or ID..." autocomplete="off">
            </div>
        </div>
    </div>

    <!-- Patients Grid -->
    <div class="row g-3 g-md-4" id="patientsGrid">
        @forelse($patients as $patient)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3 patient-card"
                 data-patient-name="{{ strtolower($patient->name) }}"
                 data-patient-id="{{ strtolower($patient->patient_id) }}">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative hover-lift">
                    <div class="position-absolute top-0 start-0 p-2 z-3">
                        @if($patient->isExpired())
                            <span class="badge bg-danger rounded-pill px-3 py-2 small fw-medium">Expired</span>
                        @elseif($patient->is_active)
                            <span class="badge bg-success rounded-pill px-3 py-2 small fw-medium">Active</span>
                        @else
                            <span class="badge bg-warning rounded-pill px-3 py-2 small fw-medium">Inactive</span>
                        @endif
                    </div>

                    <div class="card-body p-4 d-flex flex-column text-center">
                        <div class="avatar-lg mx-auto mb-3 bg-soft-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi {{ $patient->gender == 'Male' ? 'bi-person' : 'bi-person-fill' }} text-primary" style="font-size: 2.8rem;"></i>
                        </div>

                        <h5 class="card-title mb-1 fw-bold">{{ $patient->name }}</h5>
                        <p class="text-muted small mb-2">
                            Age: 
                            @if($patient->age_months || $patient->age_days)
                                {{ $patient->age_months ? $patient->age_months . ' months' : '' }}
                                {{ $patient->age_days ? $patient->age_days . ' days' : '' }}
                            @else
                                {{ $patient->age ?? '—' }} yrs
                            @endif
                        </p>
                        <p class="text-primary small fw-bold mb-3">{{ $patient->patient_id }}</p>

                        <!-- Buttons Section -->
                        <div class="d-flex gap-2 justify-content-center mt-auto flex-wrap">
                            <button type="button" class="btn btn-outline-info btn-sm rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#viewPatientModal"
                                    onclick='showPatient({!! json_encode($patient->append(['age_display'])) !!})'>
                                View
                            </button>

                            <!-- Edit Button -->
                            <button type="button" class="btn btn-outline-warning btn-sm rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#editPatientModal"
                                    onclick='openEditModal({!! json_encode($patient) !!})'>
                                <i class="bi bi-pencil"></i> Edit
                            </button>

                            @if($patient->is_active && !$patient->isExpired())
                                @if(!$patient->visits()->whereDate('visit_date', today())->exists())
                                    <button type="button" class="btn btn-success btn-sm rounded-pill"
                                            data-bs-toggle="modal" data-bs-target="#createVisitModal"
                                            onclick="openVisitModal({{ $patient->id }}, '{{ $patient->name }}', '{{ $patient->patient_id }}')">
                                        Start Visit
                                    </button>
                                @else
                                    <span class="btn btn-success btn-sm rounded-pill disabled">In OPD</span>
                                @endif
                            @else
                                <button type="button" class="btn btn-outline-success btn-sm rounded-pill"
                                        onclick="reactivatePatient({{ $patient->id }})">
                                    Reactivate
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
            <div class="col-12 text-center py-5">
                <h5 class="text-muted">No patients found</h5>
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#registerPatientModal">
                    Register First Patient
                </button>
            </div>
        @endforelse
    </div>

   
</div>

<!-- Mobile FAB -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <button class="btn btn-primary rounded-circle shadow-lg d-sm-none p-3" data-bs-toggle="modal" data-bs-target="#registerPatientModal"
            style="width: 56px; height: 56px;">
        Add
    </button>
</div>

@include('patients.modals.register')
@include('patients.modals.view')
@include('patients.modals.visit')
@include('patients.modals.edit')

<style>
.hover-lift { transition: all .3s cubic-bezier(.34,1.56,.64,1); }
.hover-lift:hover { transform: translateY(-8px) scale(1.02); box-shadow: 0 15px 35px rgba(0,0,0,.1)!important; }
.avatar-lg { width:5.5rem; height:5.5rem; }
.bg-soft-primary { background-color:rgba(13,110,253,.15)!important; }
</style>

<script>
// Live Search - Exactly like Pharmacy
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('patientSearchInput');
    const patientCards = document.querySelectorAll('.patient-card');

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();

        patientCards.forEach(card => {
            const name = card.dataset.patientName;
            const id = card.dataset.patientId;

            const matches = name.includes(query) || id.includes(query);
            card.style.display = matches ? '' : 'none';
        });
    });
});

// Reactivate Patient
function reactivatePatient(id) {
    if (!confirm('Reactivate this patient card?')) return;

    fetch(`/patients/${id}/reactivate`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || 'Patient reactivated!');
        location.reload();
    })
    .catch(() => alert('Failed to reactivate.'));
}

// Open Edit Modal with Pre-filled Data
function openEditModal(patient) {
    document.getElementById('editPatientId').value = patient.id;
    document.getElementById('edit-name').value = patient.name;
    document.getElementById('edit-age').value = patient.age || '';
    document.getElementById('edit-age_months').value = patient.age_months || '';
    document.getElementById('edit-age_days').value = patient.age_days || '';
    document.getElementById('edit-gender').value = patient.gender;
    document.getElementById('edit-phone').value = patient.phone;
    document.getElementById('edit-address').value = patient.address || '';
}
</script>
@endsection