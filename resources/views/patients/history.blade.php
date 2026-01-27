@extends('components.main-layout')
@section('title', 'Patient History')

@section('content')
<div class="container-fluid py-3 py-md-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h4 class="mb-1 text-primary fw-bold">Patient History</h4>
            <p class="text-muted mb-0 small">Search any patient and view full hospital history</p>
        </div>
    </div>

    {{-- Select2 Search --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3 p-md-4">
            <label class="form-label fw-semibold">Search patient</label>
            <select id="historyPatientSearch" class="form-select form-select-lg"></select>
        </div>
    </div>

    {{-- Patients Table A-Z --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:70px;">#</th>
                            <th>Patient</th>
                            <th>Patient ID</th>
                            <th>Phone</th>
                            <th>Age</th>
                            <th style="width:120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($patients as $i => $p)
                        <tr>
                            <td>{{ $patients->firstItem() + $i }}</td>
                            <td class="fw-semibold">{{ $p->name }}</td>
                            <td class="text-primary fw-bold">{{ $p->patient_id }}</td>
                            <td>{{ $p->phone ?? '—' }}</td>
                            <td>{{ $p->age_display ?? '—' }}</td>
                            <td>
                                <button class="btn btn-outline-info btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#patientHistoryModal"
                                        onclick="openHistoryModal({{ $p->id }})"
                                        title="View history">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">No patients found</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $patients->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>

{{-- Modal --}}
<div class="modal fade" id="patientHistoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0">Patient History</h5>
          <small class="text-muted" id="hmSubtitle">Loading...</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div id="hmLoading" class="text-center py-5">
          <div class="spinner-border" role="status"></div>
          <div class="mt-2 text-muted">Loading history...</div>
        </div>

        <div id="hmError" class="alert alert-danger d-none"></div>

        <div id="hmContent" style="display:none;">
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <div class="p-3 border rounded-4">
                <div class="text-muted small">Patient</div>
                <div class="fw-bold" id="hmName">—</div>
                <div class="text-muted small" id="hmId">—</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3 border rounded-4">
                <div class="text-muted small">Phone</div>
                <div class="fw-bold" id="hmPhone">—</div>
                <div class="text-muted small">Age: <span id="hmAge">—</span></div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3 border rounded-4">
                <div class="text-muted small">Total Visits</div>
                <div class="fw-bold" id="hmVisitsCount">0</div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th style="width:120px;">Date</th>
                  <th style="width:90px;">Time</th>
                  <th style="width:160px;">Doctor</th>
                  <th>Clinical Notes</th>
                  <th>Lab Tests</th>
                  <th>Medicines</th>
                  <th style="width:170px;">Payment/Receipt</th>
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

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#historyPatientSearch').select2({
        placeholder: 'Type name / patient id / phone...',
        allowClear: true,
        ajax: {
            url: "{{ route('patients.history.search') }}",
            dataType: 'json',
            delay: 250,
            data: params => ({ term: params.term }),
            processResults: data => data
        }
    });

    $('#historyPatientSearch').on('select2:select', function (e) {
        const patientId = e.params.data.id;
        const modal = new bootstrap.Modal(document.getElementById('patientHistoryModal'));
        modal.show();
        openHistoryModal(patientId);
    });
});

async function openHistoryModal(patientId) {
    const loading = document.getElementById('hmLoading');
    const content = document.getElementById('hmContent');
    const errorBox = document.getElementById('hmError');

    loading.style.display = '';
    content.style.display = 'none';
    errorBox.classList.add('d-none');
    errorBox.innerText = '';

    try {
        const res = await fetch(`/patient-history/${patientId}/data`, { headers: { 'Accept': 'application/json' }});
        const data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load history');

        const p = data.patient;
        document.getElementById('hmName').innerText = p.name ?? '—';
        document.getElementById('hmId').innerText = p.patient_id ?? '—';
        document.getElementById('hmPhone').innerText = p.phone ?? '—';
        document.getElementById('hmAge').innerText = p.age_display ?? '—';

        const visits = p.visits || [];
        document.getElementById('hmVisitsCount').innerText = visits.length;
        document.getElementById('hmSubtitle').innerText = `${p.name} • ${p.patient_id}`;

        const tbody = document.getElementById('hmVisitsBody');
        tbody.innerHTML = '';

        if (!visits.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">No visit history</td></tr>`;
        } else {
            visits.forEach(v => {
                const doctor = v.doctor?.name ?? '—';

                // Clinical notes from vitals (your schema)
                const vt = v.vitals;
                const clinicalHtml = vt ? `
                    <div class="small">
                        <b>Chief:</b> ${esc(vt.chief_complaint ?? '—')}<br>
                        <b>History:</b> ${esc(vt.history ?? '—')}<br>
                        <b>Exam:</b> ${esc(vt.examination ?? '—')}<br>
                        <b>Dx:</b> ${esc(vt.diagnosis ?? '—')}<br>
                        <hr class="my-2">
                        BP: <b>${esc(vt.bp ?? '—')}</b>,
                        Temp: <b>${esc(vt.temperature ?? '—')}</b>,
                        Pulse: <b>${esc(vt.pulse ?? '—')}</b>,
                        Wt: <b>${esc(vt.weight ?? '—')}</b>
                    </div>
                ` : `<span class="text-muted">No clinical notes</span>`;

                // Lab tests + result (VisitLabOrder -> test + result)
                const labs = v.lab_orders || v.labOrders || [];
                const labsHtml = labs.length ? labs.map(l => {
                    const testName = l.test?.name ?? 'Lab Test';
                    const r = l.result;
                    const resultText = r ? (
                        (r.result_value ? `Value: ${r.result_value}` : '') +
                        (r.result_text ? `${r.result_value ? '<br>' : ''}${esc(r.result_text)}` : '') +
                        (r.normal_range ? `<br><span class="text-muted">NR: ${esc(r.normal_range)}</span>` : '') +
                        (r.remarks ? `<br><span class="text-muted">Remarks: ${esc(r.remarks)}</span>` : '') +
                        (r.is_abnormal ? `<br><span class="badge bg-danger">Abnormal</span>` : '')
                    ) : `<span class="text-muted">No result</span>`;

                    return `<div class="small mb-2">
                        <b>${esc(testName)}</b>
                        <div class="text-muted">Paid: ${l.is_paid ? 'Yes' : 'No'} • Completed: ${l.is_completed ? 'Yes' : 'No'}</div>
                        <div>${resultText}</div>
                    </div>`;
                }).join('') : `<span class="text-muted">—</span>`;

                // Medicines
                const meds = v.medicine_orders || v.medicineOrders || [];
                const medsHtml = meds.length ? meds.map(m => {
                    const name = m.medicine?.name ?? 'Medicine';
                    return `<div class="small mb-2">
                        <b>${esc(name)}</b><br>
                        Dosage: ${esc(m.dosage ?? '—')} • Days: ${esc(m.duration_days ?? '—')}<br>
                        <span class="badge bg-${m.is_issued ? 'info' : 'secondary'}">${m.is_issued ? 'Issued' : 'Not Issued'}</span>
                        <span class="badge bg-${m.is_paid ? 'success' : 'warning'} text-dark">${m.is_paid ? 'Paid' : 'Unpaid'}</span>
                    </div>`;
                }).join('') : `<span class="text-muted">—</span>`;

                // Payments + Receipt
                const payments = v.payments || [];
                const receipt = v.receipt;
                const payHtml = `
                    <div class="small">
                        <b>Payments:</b> ${payments.length}<br>
                        ${payments.length ? payments.map(pmt => `• ${esc(pmt.type ?? '—')}: ${esc(pmt.amount ?? '—')} (${esc(pmt.payment_method ?? '—')})`).join('<br>') : '<span class="text-muted">No payments</span>'}
                        <hr class="my-2">
                        <b>Receipt:</b> ${receipt?.receipt_no ? esc(receipt.receipt_no) : '—'}<br>
                        <span class="text-muted">Total: ${receipt?.grand_total ?? '—'}</span>
                    </div>
                `;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${esc(v.visit_date ?? '—')}</td>
                    <td>${esc(v.visit_time ?? '—')}</td>
                    <td>${esc(doctor)}</td>
                    <td>${clinicalHtml}</td>
                    <td>${labsHtml}</td>
                    <td>${medsHtml}</td>
                    <td>${payHtml}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        loading.style.display = 'none';
        content.style.display = '';
    } catch (e) {
        loading.style.display = 'none';
        content.style.display = 'none';
        errorBox.classList.remove('d-none');
        errorBox.innerText = e.message || 'Error loading history';
    }
}

function esc(str) {
    return String(str ?? '')
        .replaceAll('&','&amp;')
        .replaceAll('<','&lt;')
        .replaceAll('>','&gt;')
        .replaceAll('"','&quot;')
        .replaceAll("'","&#039;");
}
</script>
@endsection