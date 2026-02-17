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

<!-- Live Search Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="input-group input-group-lg">
            <span class="input-group-text bg-white border-end-0">Search</span>
            <input type="text" id="patientSearchInput"
                   class="form-control border-start-0 rounded-end-3"
                   placeholder="Search by Name, ID or Phone..."
                   value="{{ request('search') }}"
                   autocomplete="off">
            <!-- No submit button needed anymore -->
        </div>
    </div>
</div>

    <!-- Patients Grid -->
    <div class="row g-3 g-md-4" id="patientsGrid">
        @include('patients.partials.patients-grid')
    </div>

     <div class="mt-4" id="pagination-container">
            {{ $patients->links('pagination::bootstrap-5') }}
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
// ────────────────────────────────────────────────
//   Live Database Search + AJAX Pagination
// ────────────────────────────────────────────────

let searchTimer = null;
const DEBOUNCE_DELAY = 350; // ms

// Simple debounce function
function debounce(func, delay) {
    return function (...args) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => func.apply(this, args), delay);
    };
}

document.addEventListener('DOMContentLoaded', function () {

    const searchInput        = document.getElementById('patientSearchInput');
    const patientsGrid       = document.getElementById('patientsGrid');
    const paginationContainer = document.getElementById('pagination-container');

    if (!searchInput || !patientsGrid || !paginationContainer) {
        console.warn('Live search elements not found');
        return;
    }

    // ─── Main search handler ───────────────────────────────
    const doSearch = debounce(function () {
        const query = searchInput.value.trim();

        // Optional: minimal loading UI
        patientsGrid.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-3 text-muted">Searching patients...</p>
            </div>
        `;

        const url = new URL('{{ route("patients.index") }}', window.location.origin);
        if (query) {
            url.searchParams.set('search', query);
        }

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            patientsGrid.innerHTML = data.html || '<div class="col-12 text-center py-5 text-muted">No results</div>';
            paginationContainer.innerHTML = data.pagination || '';
        })
        .catch(err => {
            console.error('Live search failed:', err);
            patientsGrid.innerHTML = `
                <div class="col-12 text-center py-5 text-danger">
                    <p>Something went wrong while searching.</p>
                    <small>${err.message}</small>
                </div>
            `;
        });
    }, DEBOUNCE_DELAY);


    // Start searching when user types
    searchInput.addEventListener('input', () => {
        doSearch();
    });


    // ─── Handle pagination clicks via AJAX ──────────────────
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#pagination-container a');

        if (link) {
            e.preventDefault();
            const url = link.getAttribute('href');

            patientsGrid.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 text-muted">Loading page...</p>
                </div>
            `;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(r => {
                if (!r.ok) throw new Error('Pagination fetch failed');
                return r.json();
            })
            .then(data => {
                patientsGrid.innerHTML = data.html;
                paginationContainer.innerHTML = data.pagination;
                // Optional: scroll to top of grid
                patientsGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(err => {
                console.error(err);
                patientsGrid.innerHTML = `
                    <div class="col-12 text-center py-5 text-danger">
                        Failed to load page.
                    </div>
                `;
            });
        }
    });


    // ─── Your existing functions (unchanged) ────────────────
    window.reactivatePatient = function (id) {
        if (!confirm('Reactivate this patient card?')) return;

        fetch(`/patients/${id}/reactivate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Patient reactivated!');
            // Refresh current view
            doSearch(); // ← nice: refreshes search results without full reload
        })
        .catch(() => {
            alert('Failed to reactivate patient.');
        });
    };

    window.openEditModal = function (patient) {
        document.getElementById('editPatientId').value   = patient.id;
        document.getElementById('edit-name').value       = patient.name;
        document.getElementById('edit-age').value        = patient.age || '';
        document.getElementById('edit-age_months').value = patient.age_months || '';
        document.getElementById('edit-age_days').value   = patient.age_days || '';
        document.getElementById('edit-gender').value     = patient.gender;
        document.getElementById('edit-phone').value      = patient.phone || '';
        document.getElementById('edit-address').value    = patient.address || '';
    };

    // If you have showPatient() or openVisitModal() they should still work fine
    // as long as the HTML is re-rendered with the same onclick structure
});
</script>
@endsection