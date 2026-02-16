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

    <div class="mt-4">{{ $patients->links('pagination::bootstrap-5') }}</div>
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
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('patientSearchInput');
    const grid = document.getElementById('patientsGrid');
    const paginationWrap = document.querySelector('.mt-4'); // where links are

    let t = null;
    let controller = null;

    function escapeHtml(str = '') {
        return String(str)
          .replaceAll('&','&amp;')
          .replaceAll('<','&lt;')
          .replaceAll('>','&gt;')
          .replaceAll('"','&quot;')
          .replaceAll("'",'&#039;');
    }

    function patientCardHtml(p) {
        // badge
        let badge = '';
        if (p.expired) badge = `<span class="badge bg-danger rounded-pill px-3 py-2 small fw-medium">Expired</span>`;
        else if (p.is_active) badge = `<span class="badge bg-success rounded-pill px-3 py-2 small fw-medium">Active</span>`;
        else badge = `<span class="badge bg-warning rounded-pill px-3 py-2 small fw-medium">Inactive</span>`;

        const icon = (p.gender === 'Male') ? 'bi-person' : 'bi-person-fill';

        // action buttons
        let actions = `
            <button type="button" class="btn btn-outline-info btn-sm rounded-pill"
                    data-bs-toggle="modal" data-bs-target="#viewPatientModal"
                    onclick='showPatient(${JSON.stringify(p)})'>
                View
            </button>

            <button type="button" class="btn btn-outline-warning btn-sm rounded-pill"
                    data-bs-toggle="modal" data-bs-target="#editPatientModal"
                    onclick='openEditModal(${JSON.stringify(p)})'>
                <i class="bi bi-pencil"></i> Edit
            </button>
        `;

        if (p.is_active && !p.expired) {
            if (!p.has_visit_today) {
                actions += `
                    <button type="button" class="btn btn-success btn-sm rounded-pill"
                            data-bs-toggle="modal" data-bs-target="#createVisitModal"
                            onclick="openVisitModal(${p.id}, '${escapeHtml(p.name)}', '${escapeHtml(p.patient_id)}')">
                        Start Visit
                    </button>
                `;
            } else {
                actions += `<span class="btn btn-success btn-sm rounded-pill disabled">In OPD</span>`;
            }
        } else {
            actions += `
                <button type="button" class="btn btn-outline-success btn-sm rounded-pill"
                        onclick="reactivatePatient(${p.id})">
                    Reactivate
                </button>
            `;
        }

        return `
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative hover-lift">
                <div class="position-absolute top-0 start-0 p-2 z-3">
                    ${badge}
                </div>

                <div class="card-body p-4 d-flex flex-column text-center">
                    <div class="avatar-lg mx-auto mb-3 bg-soft-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi ${icon} text-primary" style="font-size: 2.8rem;"></i>
                    </div>

                    <h5 class="card-title mb-1 fw-bold">${escapeHtml(p.name)}</h5>
                    <p class="text-muted small mb-2">Age: ${escapeHtml(p.age_display)}</p>
                    <p class="text-primary small fw-bold mb-3">${escapeHtml(p.patient_id)}</p>

                    <div class="d-flex gap-2 justify-content-center mt-auto flex-wrap">
                        ${actions}
                    </div>

                    <small class="text-muted mt-2">
                        Expires: ${escapeHtml(p.expiry_date || '—')}
                    </small>
                </div>
            </div>
        </div>
        `;
    }

    async function fetchPatients(searchValue, urlOverride = null) {
        if (controller) controller.abort();
        controller = new AbortController();

        const url = new URL(urlOverride || window.location.href);
        if (searchValue) url.searchParams.set('search', searchValue);
        else url.searchParams.delete('search');
        url.searchParams.delete('page'); // reset to first page on new search

        const res = await fetch(url.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            signal: controller.signal
        });

        if (!res.ok) throw new Error('Request failed');
        return res.json();
    }

    async function render(searchValue, urlOverride = null) {
        try {
            const data = await fetchPatients(searchValue, urlOverride);

            // grid
            if (!data.patients.length) {
                grid.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <h5 class="text-muted">No patients found</h5>
                        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#registerPatientModal">
                            Register First Patient
                        </button>
                    </div>
                `;
            } else {
                grid.innerHTML = data.patients.map(patientCardHtml).join('');
            }

            // pagination html from server
            paginationWrap.innerHTML = data.pagination;

        } catch (e) {
            if (e.name !== 'AbortError') console.error(e);
        }
    }

    // typing debounce
    searchInput.addEventListener('input', function () {
        clearTimeout(t);
        const q = this.value.trim();
        t = setTimeout(() => render(q), 250);
    });

    // pagination clicks should also load via ajax (still same controller/index)
    document.addEventListener('click', function (e) {
        const a = e.target.closest('.pagination a');
        if (!a) return;

        e.preventDefault();
        const q = searchInput.value.trim();
        render(q, a.href);
    });
});
</script>

@endsection