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


    {{-- ✅ DATE FILTER --}}
<div class="card shadow-sm rounded-4 mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">

            <div class="col-md-3">
                <label class="fw-semibold">Start Date</label>
                <input type="date" name="start_date" class="form-control"
                       value="{{ $startDate }}">
            </div>

            <div class="col-md-3">
                <label class="fw-semibold">End Date</label>
                <input type="date" name="end_date" class="form-control"
                       value="{{ $endDate }}">
            </div>

            <div class="col-md-3">
                <button class="btn btn-primary w-100">
                    Apply Filter
                </button>
            </div>

            <div class="col-md-3">
                <a href="{{ route('patients.history.index') }}" class="btn btn-light w-100">
                    Reset
                </a>
            </div>

            {{-- 🔥 QUICK BUTTONS --}}
            <div class="col-md-12 mt-2">
                <a href="?start_date={{ now()->toDateString() }}&end_date={{ now()->toDateString() }}"
                   class="btn btn-sm btn-outline-primary">Today</a>

                <a href="?start_date={{ now()->startOfMonth()->toDateString() }}&end_date={{ now()->endOfMonth()->toDateString() }}"
                   class="btn btn-sm btn-outline-success">This Month</a>

                <a href="?start_date={{ now()->startOfYear()->toDateString() }}&end_date={{ now()->endOfYear()->toDateString() }}"
                   class="btn btn-sm btn-outline-dark">This Year</a>
            </div>

        </form>
    </div>
</div>
    {{-- ✅ SUMMARY CARDS --}}
<div class="row mb-3">

    <div class="col-md-3">
        <div class="card shadow-sm rounded-4 text-center p-3">
            <small class="text-muted">Total Patients</small>
            <h4 class="fw-bold text-primary mb-0">{{ $totalPatients ?? 0 }}</h4>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm rounded-4 text-center p-3">
            <small class="text-muted">Total Visits</small>
            <h4 class="fw-bold text-success mb-0">{{ $totalVisits ?? 0 }}</h4>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm rounded-4 text-center p-3">
            <small class="text-muted">New Patients</small>
            <h4 class="fw-bold text-warning mb-0">{{ $newPatients ?? 0 }}</h4>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm rounded-4 text-center p-3">
            <small class="text-muted">Returning Patients</small>
            <h4 class="fw-bold text-info mb-0">{{ $returningPatients ?? 0 }}</h4>
        </div>
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
                                    <th>Lab Tests & Results</th>
                                    <th>Medicines</th>
                                    <th>Medicine Payment / Issued Details</th>
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

// auto open from query string
$(function () {
    const params = new URLSearchParams(window.location.search);
    const patientId = params.get('open');
    if (patientId) {
        openHistoryModal(patientId);
        new bootstrap.Modal('#patientHistoryModal').show();
    }
});

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    return d.toISOString().split('T')[0];
}

function formatDateTime(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    return d.toLocaleString();
}

function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function money(value) {
    const num = parseFloat(value);
    return isNaN(num) ? '0.00' : num.toFixed(2);
}

function getLabTestName(lab) {
    return lab?.test?.name
        ?? lab?.test?.test_name
        ?? lab?.test?.lab_test_name
        ?? 'Lab Test';
}

function getMedicineName(item) {
    return item?.medicine?.name
        ?? item?.medicine?.medicine_name
        ?? item?.medicine?.drug_name
        ?? 'Medicine';
}

function renderLabs(labOrders) {
    if (!labOrders || !labOrders.length) {
        return '<span class="text-muted">No lab tests</span>';
    }

    return labOrders.map(lab => {
        const testName = getLabTestName(lab);

        const paidBadge = lab.is_paid
            ? '<span class="badge bg-success">Paid</span>'
            : '<span class="badge bg-danger">Unpaid</span>';

        const completedBadge = lab.is_completed
            ? '<span class="badge bg-primary">Completed</span>'
            : '<span class="badge bg-warning text-dark">Pending</span>';

        const abnormalBadge = lab.result?.is_abnormal
            ? '<span class="badge bg-danger">Abnormal</span>'
            : (lab.result ? '<span class="badge bg-secondary">Reported</span>' : '');

        let resultHtml = '<div class="small text-muted">No result yet</div>';

        if (lab.result) {
            resultHtml = `
                <div class="small">
                    <div><span class="text-muted">Value:</span> ${escapeHtml(lab.result.result_value ?? '—')}</div>
                    <div><span class="text-muted">Text:</span> ${escapeHtml(lab.result.result_text ?? '—')}</div>
                    <div><span class="text-muted">Normal Range:</span> ${escapeHtml(lab.result.normal_range ?? '—')}</div>
                    <div><span class="text-muted">Remarks:</span> ${escapeHtml(lab.result.remarks ?? '—')}</div>
                </div>
            `;
        }

        return `
            <div class="border rounded p-2 mb-2 bg-light">
                <div class="d-flex flex-wrap gap-1 mb-1">
                    <strong>${escapeHtml(testName)}</strong>
                    ${paidBadge}
                    ${completedBadge}
                    ${abnormalBadge}
                </div>
                ${resultHtml}
                ${lab.paid_at ? `<div class="small text-muted mt-1">Paid at: ${escapeHtml(formatDateTime(lab.paid_at))}</div>` : ''}
                ${lab.completed_at ? `<div class="small text-muted">Completed at: ${escapeHtml(formatDateTime(lab.completed_at))}</div>` : ''}
            </div>
        `;
    }).join('');
}

function renderMedicines(medicineOrders) {
    if (!medicineOrders || !medicineOrders.length) {
        return '<span class="text-muted">No medicines</span>';
    }

    return medicineOrders.map(item => {
        const medName = getMedicineName(item);

        const issueBadge = item.is_issued
            ? '<span class="badge bg-success">Issued</span>'
            : '<span class="badge bg-warning text-dark">Not Issued</span>';

        const paidBadge = item.is_paid
            ? '<span class="badge bg-primary">Paid</span>'
            : '<span class="badge bg-danger">Unpaid</span>';

        const handedBadge = item.handed_over_at
            ? '<span class="badge bg-info text-dark">Handed Over</span>'
            : '';

        return `
            <div class="border rounded p-2 mb-2">
                <div class="d-flex flex-wrap gap-1 mb-1">
                    <strong>${escapeHtml(medName)}</strong>
                    ${issueBadge}
                    ${paidBadge}
                    ${handedBadge}
                </div>

                <div class="small">
                    <div><span class="text-muted">Dosage:</span> ${escapeHtml(item.dosage ?? '—')}</div>
                    <div><span class="text-muted">Duration:</span> ${escapeHtml(item.duration_days ?? '—')} day(s)</div>
                    <div><span class="text-muted">Instruction:</span> ${escapeHtml(item.instruction ?? '—')}</div>
                </div>

                ${item.issued_at ? `<div class="small text-muted mt-1">Issued at: ${escapeHtml(formatDateTime(item.issued_at))}</div>` : ''}
                ${item.paid_at ? `<div class="small text-muted">Paid at: ${escapeHtml(formatDateTime(item.paid_at))}</div>` : ''}
                ${item.handed_over_at ? `<div class="small text-muted">Handed over at: ${escapeHtml(formatDateTime(item.handed_over_at))}</div>` : ''}
            </div>
        `;
    }).join('');
}

function renderMedicinePaymentDetails(medicineOrders) {
    if (!medicineOrders || !medicineOrders.length) {
        return '<span class="text-muted">No medicine payment details</span>';
    }

    let grandTotal = 0;

    const html = medicineOrders.map(item => {
        const medName = getMedicineName(item);
        const issues = item.pharmacyIssues || [];

        let orderTotal = 0;

        const issuesHtml = issues.length
            ? issues.map(issue => {
                const qty = parseFloat(issue.quantity_issued ?? 0) || 0;
                const unitPrice = parseFloat(issue.unit_price ?? 0) || 0;
                const totalAmount = parseFloat(issue.total_amount ?? (qty * unitPrice)) || 0;

                orderTotal += totalAmount;

                return `
                    <div class="border rounded p-2 mb-2 bg-light">
                        <div><strong>Batch:</strong> ${escapeHtml(issue.batch_no ?? '—')}</div>
                        <div class="small"><span class="text-muted">Qty Issued:</span> ${escapeHtml(issue.quantity_issued ?? '—')}</div>
                        <div class="small"><span class="text-muted">Unit Price:</span> ${money(issue.unit_price)}</div>
                        <div class="small"><span class="text-muted">Total Amount:</span> ${money(totalAmount)}</div>
                        <div class="small"><span class="text-muted">Expiry:</span> ${escapeHtml(formatDate(issue.expiry_date))}</div>
                        <div class="small"><span class="text-muted">Issued At:</span> ${escapeHtml(formatDateTime(issue.issued_at))}</div>
                        <div class="small"><span class="text-muted">Issued By:</span> ${escapeHtml(issue.issued_by?.name ?? issue.issuedBy?.name ?? '—')}</div>
                    </div>
                `;
            }).join('')
            : '<div class="small text-muted">No pharmacy issue records</div>';

        grandTotal += orderTotal;

        return `
            <div class="border rounded p-2 mb-2 bg-white">
                <div class="mb-1"><strong>${escapeHtml(medName)}</strong></div>
                <div class="small mb-2">
                    <span class="text-muted">Payment:</span> ${item.is_paid ? 'Paid' : 'Unpaid'}
                    &nbsp;|&nbsp;
                    <span class="text-muted">Issue:</span> ${item.is_issued ? 'Issued' : 'Not Issued'}
                </div>
                ${issuesHtml}
                <div class="alert alert-secondary py-2 px-3 mb-0">
                    <strong>${escapeHtml(medName)} Total: ${money(orderTotal)}</strong>
                </div>
            </div>
        `;
    }).join('');

    return `
        ${html}
        <div class="alert alert-success py-2 px-3 mt-2 mb-0">
            <strong>Total Medicine Amount: ${money(grandTotal)}</strong>
        </div>
    `;
}

async function openHistoryModal(id) {
    $('#hmLoading').show();
    $('#hmContent').hide();
    $('#hmError').addClass('d-none').text('');
    $('#hmProcedures').html('Loading...');

    try {
        const url = "{{ route('patients.history.data', ':id') }}".replace(':id', id);

        const res = await fetch(url);
        const data = await res.json();

        if (!data.success) {
            throw new Error(data.message || 'Failed to load history');
        }

        const p = data.patient;
        const visits = p.visits || [];

        $('#hmName').text(p.name ?? '—');
        $('#hmId').text(p.patient_id ?? '—');
        $('#hmPhone').text(p.phone ?? '—');
        $('#hmAge').text(p.age_display ?? '—');
        $('#hmVisitsCount').text(visits.length);
        $('#hmSubtitle').text((p.name ?? '') + ' • ' + (p.patient_id ?? ''));

        let procedures = [];
        let rows = '';

        visits.forEach(v => {
            (v.procedures || v.procedureOrders || []).forEach(pr => {
                procedures.push({
                    name: pr.procedure?.procedure_name ?? pr.procedure?.name ?? 'Procedure',
                    date: formatDate(v.visit_date),
                    doctor: v.doctor?.name ?? ''
                });
            });

            rows += `
                <tr>
                    <td>${formatDate(v.visit_date)}</td>
                    <td>${escapeHtml(v.visit_time ?? '—')}</td>
                    <td>${escapeHtml(v.doctor?.name ?? '—')}</td>
                    <td>${escapeHtml(v.vitals?.diagnosis ?? v.notes ?? '—')}</td>
                    <td>${renderLabs(v.labOrders || [])}</td>
                    <td>${renderMedicines(v.medicineOrders || [])}</td>
                    <td>${renderMedicinePaymentDetails(v.medicineOrders || [])}</td>
                </tr>
            `;
        });

        $('#hmVisitsBody').html(
            rows || '<tr><td colspan="7" class="text-center text-muted">No visits</td></tr>'
        );

        if (!procedures.length) {
            $('#hmProcedures').html('<span class="text-muted">No procedures</span>');
        } else {
            $('#hmProcedures').html(procedures.map(p => `
                <div class="border rounded p-2 mb-2">
                    <b>${escapeHtml(p.name)}</b><br>
                    <small>${escapeHtml(p.date)} • ${escapeHtml(p.doctor)}</small>
                </div>
            `).join(''));
        }

        $('#hmLoading').hide();
        $('#hmContent').show();

    } catch (e) {
        $('#hmLoading').hide();
        $('#hmError').removeClass('d-none').text(e.message || 'An error occurred');
    }
}
</script>

<style>
    #hmVisitsBody td {
        vertical-align: top;
        min-width: 180px;
    }
</style>
@endsection