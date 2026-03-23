@extends('components.main-layout')
@section('title', 'Patient History')

@section('content')
<div class="container-fluid py-3 py-md-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="text-primary fw-bold mb-1">Patient History</h4>
            <small class="text-muted">Search any patient and view full hospital history</small>
        </div>
    </div>

    {{-- Search --}}
    <div class="card shadow-sm rounded-4 mb-3">
        <div class="card-body">
            <label class="fw-semibold">Search patient</label>
            <select id="historyPatientSearch" class="form-select form-select-lg"></select>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Patient</th>
                        <th>ID</th>
                        <th>Phone</th>
                        <th>Age</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $i => $p)
                    <tr>
                        <td>{{ $patients->firstItem() + $i }}</td>
                        <td>{{ $p->name }}</td>
                        <td class="text-primary fw-bold">{{ $p->patient_id }}</td>
                        <td>{{ $p->phone ?? '—' }}</td>
                        <td>{{ $p->age_display ?? '—' }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info"
                                data-bs-toggle="modal"
                                data-bs-target="#patientHistoryModal"
                                onclick="openHistoryModal({{ $p->id }})">
                                View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No patients</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3">
            {{ $patients->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

{{-- MODAL --}}
<div class="modal fade" id="patientHistoryModal">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content rounded-4">

            <div class="modal-header">
                <div>
                    <h5 class="mb-0">Patient History</h5>
                    <small id="hmSubtitle" class="text-muted"></small>
                </div>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div id="hmLoading" class="text-center py-5">
                    <div class="spinner-border"></div>
                </div>

                <div id="hmError" class="alert alert-danger d-none"></div>

                <div id="hmContent" style="display:none;">

                    {{-- Patient Info --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small>Patient</small>
                                <div id="hmName" class="fw-bold"></div>
                                <small id="hmId"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small>Phone</small>
                                <div id="hmPhone" class="fw-bold"></div>
                                <small>Age: <span id="hmAge"></span></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small>Total Visits</small>
                                <div id="hmVisitsCount" class="fw-bold"></div>
                            </div>
                        </div>
                    </div>

                    {{-- ✅ PROCEDURES --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="text-primary fw-bold mb-2">Procedures</h6>
                            <div id="hmProcedures">—</div>
                        </div>
                    </div>

                    {{-- Visits --}}
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Doctor</th>
                                    <th>Notes</th>
                                    <th>Labs</th>
                                    <th>Medicines</th>
                                    <th>Payments</th>
                                </tr>
                            </thead>
                            <tbody id="hmVisitsBody"></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function () {

    $('#historyPatientSearch').select2({
        placeholder: 'Search patient...',
        minimumInputLength: 1,
        ajax: {
            url: "{{ route('patients.history.search') }}",
            dataType: 'json',
            data: params => ({ term: params.term }),
            processResults: data => ({
                results: data.results ?? []
            })
        }
    });

    $('#historyPatientSearch').on('select2:select', function (e) {
        openHistoryModal(e.params.data.id);
        new bootstrap.Modal('#patientHistoryModal').show();
    });

});

// Check URL query param to auto-open modal
$(function() {
    const params = new URLSearchParams(window.location.search);
    const patientId = params.get('open');
    if (patientId) {
        openHistoryModal(patientId);
        new bootstrap.Modal('#patientHistoryModal').show();
    }
});

function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toISOString().split('T')[0]; // ✅ FIXED DATE
}

async function openHistoryModal(id) {

    $('#hmLoading').show();
    $('#hmContent').hide();
    $('#hmError').addClass('d-none');

    $('#hmProcedures').html('Loading...');

    try {
        const url = "{{ route('patients.history.data', ':id') }}".replace(':id', id);

        const res = await fetch(url);
        const data = await res.json();

        if (!data.success) throw new Error(data.message);

        const p = data.patient;

        $('#hmName').text(p.name);
        $('#hmId').text(p.patient_id);
        $('#hmPhone').text(p.phone);
        $('#hmAge').text(p.age_display);

        const visits = p.visits || [];
        $('#hmVisitsCount').text(visits.length);
        $('#hmSubtitle').text(p.name + ' • ' + p.patient_id);

        let procedures = [];

        let rows = '';

        visits.forEach(v => {

            // collect procedures
            (v.procedures || v.procedureOrders || []).forEach(pr => {
                procedures.push({
                    name: pr.procedure?.procedure_name ?? 'Procedure', 
                    date: formatDate(v.visit_date),
                    doctor: v.doctor?.name ?? ''
                });
            });

            rows += `
                <tr>
                    <td>${formatDate(v.visit_date)}</td>
                    <td>${v.visit_time ?? '—'}</td>
                    <td>${v.doctor?.name ?? '—'}</td>
                    <td>${v.vitals?.diagnosis ?? '—'}</td>
                    <td>${(v.labOrders||[]).length}</td>
                    <td>${(v.medicineOrders||[]).length}</td>
                    <td>${(v.payments||[]).length}</td>
                </tr>
            `;
        });

        $('#hmVisitsBody').html(rows || '<tr><td colspan="7">No visits</td></tr>');

        // render procedures
        if (!procedures.length) {
            $('#hmProcedures').html('<span class="text-muted">No procedures</span>');
        } else {
            $('#hmProcedures').html(procedures.map(p => `
                <div class="border rounded p-2 mb-2">
                    <b>${p.name}</b><br>
                    <small>${p.date} • ${p.doctor}</small>
                </div>
            `).join(''));
        }

        $('#hmLoading').hide();
        $('#hmContent').show();

    } catch (e) {
        $('#hmLoading').hide();
        $('#hmError').removeClass('d-none').text(e.message);
    }
}
</script>
@endsection