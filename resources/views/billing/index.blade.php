{{-- resources/views/billing/index.blade.php --}}
@extends('components.main-layout')
@section('title', 'Billing Center • Mana Dispensary')

@section('content')
<div class="min-vh-100 bg-gradient-to-b from-blue-50 to-white">

    <!-- HEADER -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-6 shadow-2xl">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="display-5 fw-bold mb-1">Hospital Billing Center</h1>
                    <p class="lead mb-0 opacity-90">Generate Receipts • Record Payments • Track Services</p>
                </div>
                <div class="col-auto">
                    <h3>Pending Bills: <span class="badge bg-warning text-dark fs-4 px-4">{{ $pendingCount }}</span></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-5">

        {{-- =============================================== --}}
        {{-- 1. SHOW DETAILED BILL WHEN A VISIT IS SELECTED --}}
        {{-- =============================================== --}}
        @if(isset($visit))
            <div class="card border-0 shadow-lg rounded-4 mb-5">
                <div class="card-header bg-success text-white py-4 d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        Bill for Patient: <strong>{{ $visit->patient->name }}</strong> 
                        ({{ $visit->patient->patient_id }})
                    </h3>
                    <a href="{{ route('billing.index') }}" class="btn btn-light btn-sm px-4">
                        Back to List
                    </a>
                </div>

                <div class="card-body p-5">

                    <!-- Patient Info -->
                    <div class="row mb-4 text-muted">
                        <div class="col-md-6">
                            <p><strong>Visit Date:</strong> {{ $visit->visit_date->format('d F Y') }}</p>
                            <p><strong>Doctor:</strong> Dr. {{ $visit->doctor->name ?? 'Not Assigned' }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p><strong>Visit ID:</strong> #{{ $visit->id }}</p>
                            {{--  @if($receiptGenerated)
                                <span class="badge bg-success fs-5 px-4">Receipt Generated</span>
                            @endif  --}}
                        </div>
                    </div>

                    <!-- Bill Table -->
<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
            <tr>
                <th style="width: 45%">Description</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Unit Price</th>
                <th class="text-end">Total</th>
                @if(auth()->user()->hasRole('Admin'))
                    <th class="text-center">Action</th>
                @endif
            </tr>
        </thead>
        <tbody>

            @foreach($medicines as $m)
                @php
                    $issuedQty = $m->pharmacyIssues->sum('quantity_issued') ?? 1;
                    $totalPrice = $issuedQty * $m->medicine->price;
                    $canRemoveMedicine = !$m->is_paid && !$m->is_issued;
                @endphp
                <tr>
                    <td>
                        {{ $m->medicine->medicine_name }}
                        @if(!$canRemoveMedicine && auth()->user()->hasRole('Admin'))
                            <span class="badge bg-secondary ms-2 small">Issued / Paid</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $issuedQty }}</td>
                    <td class="text-end">Tsh{{ number_format($m->medicine->price, 0) }}</td>
                    <td class="text-end">Tsh{{ number_format($totalPrice, 0) }}</td>
                    @if(auth()->user()->hasRole('Admin'))
                        <td class="text-center">
                            @if($canRemoveMedicine)
                                <button type="button" class="btn btn-sm btn-danger rounded-pill"
                                        onclick="removeBillItem({{ $m->id }}, 'medicine', '{{ addslashes($m->medicine->medicine_name) }}')">
                                    <i class="bi bi-trash me-1"></i> Remove
                                </button>
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach

            @foreach($labTests as $t)
                @php
                    $canRemoveLab = !$t->is_paid;
                @endphp
                <tr>
                    <td>{{ $t->test->test_name }} (Lab Test)</td>
                    <td class="text-center">1</td>
                    <td class="text-end">Tsh{{ number_format($t->test->price, 0) }}</td>
                    <td class="text-end">Tsh{{ number_format($t->test->price, 0) }}</td>
                    @if(auth()->user()->hasRole('Admin'))
                        <td class="text-center">
                            @if($canRemoveLab)
                                <button type="button" class="btn btn-sm btn-danger rounded-pill"
                                        onclick="removeBillItem({{ $t->id }}, 'lab', '{{ addslashes($t->test->test_name) }}')">
                                    <i class="bi bi-trash me-1"></i> Remove
                                </button>
                            @else
                                <span class="badge bg-secondary small">Paid</span>
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach

            @foreach($injections as $i)
                <tr>
                    <td>{{ $i->medicine->medicine_name }} (Injection)</td>
                    <td class="text-center">1</td>
                    <td class="text-end">Tsh{{ number_format($i->medicine->price, 0) }}</td>
                    <td class="text-end">Tsh{{ number_format($i->medicine->price, 0) }}</td>
                    <!-- Add injection remove logic later if needed -->
                    @if(auth()->user()->hasRole('Admin'))
                        <td class="text-center">
                            <span class="badge bg-secondary small">Locked</span>
                        </td>
                    @endif
                </tr>
            @endforeach

            @if($bedCharges > 0)
                <tr>
                    <td>Bed/Ward Charges ({{ $bedDays }} day{{ $bedDays > 1 ? 's' : '' }})</td>
                    <td class="text-center">1</td>
                    <td class="text-end">Tsh{{ number_format($bedCharges, 0) }}</td>
                    <td class="text-end">Tsh{{ number_format($bedCharges, 0) }}</td>
                    @if(auth()->user()->hasRole('Admin'))
                        <td class="text-center">
                            <span class="badge bg-secondary small">Locked</span>
                        </td>
                    @endif
                </tr>
            @endif

        </tbody>
        <tfoot class="table-dark">
            <tr>
                <th colspan="{{ auth()->user()->hasRole('Admin') ? 4 : 3 }}" class="text-end fs-5">GRAND TOTAL</th>
                <th class="text-end fs-4 fw-bold text-white">
                    Tsh{{ number_format($grandTotal, 0) }}
                </th>
            </tr>
        </tfoot>
    </table>
</div>

                    <!-- Generate Receipt Button -->
 <div class="text-center mt-5">
    @if($showGenerateButton)
        <form action="{{ route('billing.generate', $visit) }}" method="POST" class="d-inline" id="generateForm">
    @csrf
    <button type="submit" class="btn btn-success btn-lg px-5 shadow" id="generateBtn">
        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        Generate New Receipt (Current Items)
    </button>
</form>

<script>
document.getElementById('generateForm').addEventListener('submit', function() {
    const btn = document.getElementById('generateBtn');
    btn.disabled = true;
    btn.querySelector('.spinner-border').classList.remove('d-none');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
});
</script>
        <small class="text-muted d-block mt-2">
            This will generate a full receipt and mark all items as paid.
        </small>
    @else
        <div class="alert alert-success fs-5 px-5 py-3">
            @if($visit->receipt()->exists())
                Full receipt already generated • Use "Record Payment" for additional payments
            @else
                All items paid • Visit complete
            @endif
        </div>
    @endif
</div>
                </div>
            </div>
        @endif

        {{-- =============================================== --}}
        {{-- 2. MAIN DASHBOARD (when no visit selected) --}}
        {{-- =============================================== --}}
        @if(!isset($visit))
            <!-- SEARCH BOX -->
            <div class="card border-0 shadow-lg rounded-4 mb-5">
                <div class="card-body p-5">
                    <form action="{{ route('billing.search') }}" method="GET">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-8">
                                <input type="text" name="q" class="form-control form-control-lg rounded-pill shadow-sm"
                                       placeholder="Search by Patient ID or Name..." value="{{ request('q') }}" autofocus>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 w-100 h-100 shadow-lg">
                                    Search Patient
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABS -->
            <ul class="nav nav-tabs mb-5" id="billingTabs">
                <li class="nav-item">
                    <a class="nav-link active fs-5 fw-bold" data-bs-toggle="tab" href="#pending">Pending Bills ({{ $pendingCount }})</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-5 fw-bold" data-bs-toggle="tab" href="#inprogress">In Progress ({{ $inProgressVisits->count() }})</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-5 fw-bold" data-bs-toggle="tab" href="#payments">Payments ({{ $receipts->count() }})</a>
                </li>
            </ul>

            <div class="tab-content">

                <!-- PENDING BILLS -->
                <div class="tab-pane fade show active" id="pending">
                    <div class="card border-0 shadow-lg rounded-4">
                        <div class="card-header bg-warning text-dark py-4">
                            <h4 class="mb-0">Ready for Receipt Generation</h4>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Patient ID</th>
                                        <th>Name</th>
                                        <th>Doctor</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingVisits as $v)
                                    <tr>
                                        <td>{{ $v->visit_date->format('d M Y') }}</td>
                                        <td><strong>{{ $v->patient->patient_id }}</strong></td>
                                        <td>{{ $v->patient->name }}</td>
                                        <td>{{ $v->doctor?->name ?? '—' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('billing.pending.show', $v) }}" 
                                               class="btn btn-success btn-sm rounded-pill px-4">
                                                Generate Bill
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-5 text-muted fs-4">No pending bills</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="card-footer bg-light">{{ $pendingVisits->links('pagination::bootstrap-5') }}</div>
                        </div>
                    </div>
                </div>

                <!-- IN PROGRESS -->
                <div class="tab-pane fade" id="inprogress">
                    <div class="card border-0 shadow-lg rounded-4">
                        <div class="card-header bg-warning-subtle text-dark py-4">
                            <h4 class="mb-0">Incomplete Services</h4>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Patient</th>
                                        <th>Stuck At</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inProgressVisits as $v)
                                    <tr>
                                        <td>{{ $v->visit_date->format('d M Y H:i') }}</td>
                                        <td><strong>{{ $v->patient->name }}</strong> ({{ $v->patient->patient_id }})</td>
                                        <td>
                                            @if($v->medicineIssues()->whereNull('pharmacy_issues.issued_at')->exists())
                                                <span class="badge bg-danger">Pharmacy</span>
                                            @elseif($v->labOrders()->where('is_completed', false)->exists())
                                                <span class="badge bg-warning text-dark">Lab</span>
                                            @elseif($v->injectionOrders()->where('is_given', false)->exists())
                                                <span class="badge bg-info">Injection</span>
                                            @elseif($v->bedAdmission && !$v->bedAdmission->is_discharged)
                                                <span class="badge bg-primary">In Ward</span>
                                            @else
                                                <span class="badge bg-secondary">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $v->all_services_completed ? 'success' : 'danger' }}">
                                                {{ $v->all_services_completed ? 'Completed' : 'In Progress' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-5 text-muted fs-4">All caught up!</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PAYMENTS HISTORY -->
                <div class="tab-pane fade" id="payments">
                    <div class="card border-0 shadow-lg rounded-4">
                        <div class="card-header bg-success text-white py-4">
                            <h4 class="mb-0">Receipt & Payment History</h4>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Receipt Date</th>
                                        <th>Patient</th>
                                        {{--  <th>Visit ID</th>  --}}
                                        <th>Total</th>
                                        @role('Admin')<th>Paid</th>@endrole
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
    @php
        // Group receipts by patient + date
        $groupedReceipts = $receipts->groupBy(function ($r) {
            return $r->visit->patient->id . '|' . $r->generated_at->format('Y-m-d');
        });
    @endphp

    @forelse($groupedReceipts as $key => $group)
        @php
            [$patientId, $date] = explode('|', $key);
            $firstReceipt = $group->first();
            $patient = $firstReceipt->visit->patient;
            $totalBilled = $group->sum('grand_total');
            $totalPaid = $firstReceipt->visit->payments()
                ->whereDate('paid_at', $date)
                ->sum('amount');
            $balance = $totalBilled - $totalPaid;
        @endphp
        <tr>
            <td>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</td>
            <td>
                <strong>{{ $patient->name }}</strong><br>
                <small class="text-muted">{{ $patient->patient_id }}</small>
            </td>
            <td>Tsh{{ number_format($totalBilled, 0) }}</td>
            @role('Admin')
            <td>Tsh{{ number_format($totalPaid, 0) }}</td>
            @endrole
            <td>
                @if($balance > 0)
                    <strong class="text-danger">Tsh{{ number_format($balance, 0) }}</strong>
                @else
                    <span class="text-success fw-bold">PAID</span>
                @endif
            </td>
            <td>
                @if($balance <= 0)
                    <span class="badge bg-success">PAID</span>
                @elseif($totalPaid > 0)
                    <span class="badge bg-warning text-dark">PARTIAL</span>
                @else
                    <span class="badge bg-danger">UNPAID</span>
                @endif
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-info btn-sm rounded-circle me-2"
                        data-bs-toggle="modal" data-bs-target="#viewPaymentModal"
                        onclick="loadPaymentDetails({{ $firstReceipt->visit->id }})">
                    View
                </button>

                @if($balance > 0)
                    <button type="button" class="btn btn-primary btn-sm rounded-pill"
                            data-bs-toggle="modal" data-bs-target="#paymentModal"
                            onclick="openRecordPayment({{ $firstReceipt->visit->id }}, '{{ addslashes($patient->name) }}', {{ $totalBilled }}, {{ $totalPaid }}, {{ $balance }})">
                        Pay
                    </button>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="8" class="text-center py-5 text-muted fs-4">No receipts yet</td></tr>
    @endforelse
</tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </div>
</div>

{{-- =============================================== --}}
{{-- MODALS - RECORD PAYMENT --}}
{{-- =============================================== --}}
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="paymentForm" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="paymentModalLabel">Record Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>Patient: <span id="recordPatient" class="text-primary fw-bold"></span></h5>
                    <div class="alert alert-info mb-4">
                        <strong>Amount Due:</strong> <span id="recordDue" class="fs-5 fw-bold"></span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Total Bill</label>
                            <input type="text" id="recordTotal" class="form-control bg-light" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Already Paid</label>
                            <input type="text" id="recordPaid" class="form-control bg-light" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Amount to Pay <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="recordAmount" class="form-control form-control-lg" 
                                   required min="1" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select form-select-lg" required>
                                <option value="cash">Cash</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="insurance">Insurance</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Transaction ID / Reference (Optional)</label>
                            <input type="text" name="transaction_id" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-lg px-5">Record Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- =============================================== --}}
{{-- MODAL - VIEW PAYMENT DETAILS --}}
{{-- =============================================== --}}
<div class="modal fade" id="viewPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold">Payment Details & History</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="paymentDetailsBody">
                <!-- Filled by JavaScript -->
            </div>
        </div>
    </div>
</div>

{{-- =============================================== --}}
{{-- JAVASCRIPT --}}
{{-- =============================================== --}}
<script>
function openRecordPayment(visitId, patientName, total, paid, balance) {
    const form = document.getElementById('paymentForm');
    form.action = `/billing/pay/${visitId}`;

    document.getElementById('recordPatient').textContent = patientName;
    document.getElementById('recordTotal').value = 'Tsh ' + total.toLocaleString();
    document.getElementById('recordPaid').value = 'Tsh ' + paid.toLocaleString();
    document.getElementById('recordDue').textContent = 'Tsh ' + balance.toLocaleString();

    const amountInput = document.getElementById('recordAmount');
    amountInput.value = balance;
    amountInput.max = balance;
    amountInput.min = 1;
}

function loadPaymentDetails(visitId) {
    fetch(`/billing/payment-details/${visitId}`)
        .then(response => response.json())
        .then(data => {
            let html = `
                <h5>Patient: <strong>${data.patient}</strong> | Visit #${visitId}</h5>
                <hr>
                <div class="row mb-4">
                    {{--  <div class="col-md-4"><strong>Total Bill:</strong> Tsh${data.total.toLocaleString()}</div>  --}}
                    <div class="col-md-4"><strong>Paid:</strong> Tsh${data.paid.toLocaleString()}</div>
                    {{--  <div class="col-md-4"><strong>Balance:</strong> 
                        <span class="${data.balance > 0 ? 'text-danger' : 'text-success'} fw-bold">
                            Tsh${data.balance.toLocaleString()}
                        </span>
                    </div>  --}}
                </div>
                <h6 class="mt-4 text-success fw-bold">Payment History</h6>
            `;

            if (data.payments.length > 0) {
                html += `
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Received By</th>
                                <th>Items</th>
                            </tr>
                        </thead>
                        <tbody>`;
                data.payments.forEach(p => {
                    const items = p.details.map(d => 
                        `${d.item_name} × ${d.quantity} @ Tsh${d.unit_price} = Tsh${d.line_total || d.total_price}`
                    ).join('<br>');
                    html += `
                        <tr>
                            <td>${p.date}</td>
                            <td><strong>Tsh${p.amount.toLocaleString()}</strong></td>
                            <td><span class="badge bg-info">${p.method}</span></td>
                            <td>${p.received_by}</td>
                            <td class="small">${items || '—'}</td>
                        </tr>`;
                });
                html += `</tbody></table></div>`;
            } else {
                html += `<p class="text-muted mt-3">No payments recorded yet.</p>`;
            }

            document.getElementById('paymentDetailsBody').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('paymentDetailsBody').innerHTML = '<p class="text-danger">Failed to load data.</p>';
        });
}

function removeBillItem(itemId, type, name) {
    Swal.fire({
        title: 'Remove this item?',
        html: `Are you sure you want to remove <strong>"${name}"</strong> from the bill?<br><br><small>This action cannot be undone.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',      // red for danger
        cancelButtonColor: '#6c757d',       // gray for cancel
        confirmButtonText: 'Yes, Remove It',
        cancelButtonText: 'Cancel',
        reverseButtons: true,               // puts Cancel on left
        focusCancel: true
    }).then((result) => {
        if (!result.isConfirmed) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            Swal.fire({
                icon: 'error',
                title: 'CSRF Error',
                text: 'CSRF token missing. Please refresh the page.',
            });
            return;
        }

        // Show loading state
        Swal.fire({
            title: 'Removing...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch("{{ route('billing.remove-item') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                visit_id: {{ $visit->id ?? 'null' }},
                item_id: itemId,
                item_type: type
            })
        })
        .then(async response => {
            console.log('Remove status:', response.status);

            if (!response.ok) {
                const text = await response.text();
                console.error('Non-OK body:', text.substring(0, 300));
                throw new Error(`Server error ${response.status}`);
            }

            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message || 'Item removed',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                }).then(() => {
                    location.reload(); // Refresh to show updated bill
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: data.message || 'Could not remove the item. Please try again.'
                });
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Connection Error',
                text: 'Failed to connect to server.\nItem may still be removed — please refresh to check.'
            });
        });
    });
}
</script>
@endsection