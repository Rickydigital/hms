@extends('components.main-layout')
@section('title', 'Pharmacy • Mana Dispensary')

@section('content')
<div class="min-vh-100 bg-light">

    <!-- HEADER -->
    <div class="bg-gradient text-white py-5 shadow" style="background: linear-gradient(90deg, #0d9488, #0f766e);">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row align-items-center py-3">
                <div class="col">
                    <h1 class="display-5 fw-bold mb-1">Pharmacy Module</h1>
                    <p class="lead mb-0 opacity-75">
                        Welcome, <strong>{{ Auth::user()->name }}</strong> 
                        • {{ now()->format('l, d F Y') }}
                    </p>
                </div>
                <div class="col-auto text-end">
                    <div class="mb-3">
                        <span class="me-2 opacity-75">Pending Visits:</span>
                        <span class="badge bg-warning text-dark fs-4 px-4 py-2">{{ $groupedPending->count() }}</span>
                    </div>
                    <a href="{{ route('pharmacy.history') }}" class="btn btn-light btn-lg shadow-sm">View Past Issues</a>
                    <a href="{{ route('pharmacy.sales.history') }}" class="btn btn-success btn-lg shadow-lg ms-3">Direct Sale (OTC)</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-5">
        <div class="row g-5">

            <!-- MAIN: Pending Prescriptions (Grouped by Patient) -->
            <div class="col-lg-8">
                <div class="card border-0 shadow rounded-4 overflow-hidden h-100">
                    <div class="card-header text-white py-4" style="background: linear-gradient(90deg, #0d9488, #0f766e);">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="mb-0 fw-bold">Medicines Workflow</h4>
                            </div>
                            <div class="col-auto">
                                <input type="text" id="searchInput" class="form-control form-control-lg" 
                                       placeholder="Search by Patient Name or ID..." autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0" style="max-height: 78vh; overflow-y: auto;">
                        <div id="ordersList">
                            @forelse($groupedPending as $visitId => $orders)
                                @php
                                    $visit = $orders->first()->visit;
                                    $patient = $visit->patient;
                                    $canIssueAny = $orders->where('is_issued', false)
                                                          ->where(fn($o) => ($o->medicine->total_stock ?? 0) >= 1)
                                                          ->count() > 0;
                                    $allIssued = $orders->where('is_issued', false)->count() === 0;
                                    $allPaid = $orders->where('is_paid', false)->count() === 0;
                                @endphp

                                <div class="patient-group p-4 border-bottom hover-bg-light"
                                     data-patient-name="{{ strtolower($patient->name) }}"
                                     data-patient-id="{{ strtolower($patient->patient_id) }}">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="fw-bold text-dark mb-1">
                                                {{ $patient->name }}
                                                <span class="text-secondary">({{ $patient->patient_id }})</span>
                                            </h5>
                                            <small class="text-muted">
                                                {{ $orders->count() }} medicine{{ $orders->count() > 1 ? 's' : '' }} prescribed
                                            </small>
                                        </div>

                                        <div>
                                            @if($allPaid && $allIssued)
                                                <span class="badge bg-success fs-6">READY TO HAND OVER</span>
                                            @elseif($allIssued)
                                                <span class="badge bg-warning text-dark fs-6">ISSUED • WAITING PAYMENT</span>
                                            @else
                                                <span class="badge bg-info text-white fs-6">PRESCRIBED</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- List of medicines -->
                                    <div class="ms-4 mb-3">
                                        @foreach($orders as $order)
                                            @php
                                                $m = $order->medicine;
                                                $stock = $m->total_stock ?? 0;
                                            @endphp
                                            <div class="d-flex justify-content-between align-items-center py-2">
                                                <div>
                                                    <strong>{{ $m->medicine_name }}</strong>
                                                    <span class="text-muted small ms-2">
                                                        {{ $order->dosage }} × {{ $order->duration_days }} days
                                                        @if($order->instruction) • {{ $order->instruction }} @endif
                                                    </span>
                                                    @if($order->is_issued)
                                                        <span class="badge bg-secondary ms-2">Issued: {{ $order->quantity_issued }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-end">
                                                    <div class="text-success fw-bold">Stock: {{ $stock }}</div>
                                                    <div>Tsh {{ number_format($m->price, 0) }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Action Button -->
                                    <div class="text-center">
                                        @if($canIssueAny)
                                            <button type="button"
                                                    class="btn btn-success btn-lg rounded-pill shadow w-100"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#issueModal"
                                                    data-visit-id="{{ $visitId }}"
                                                    data-patient-name="{{ $patient->name }}"
                                                    data-patient-id="{{ $patient->patient_id }}">
                                                Issue All Pending Medicines
                                            </button>
                                        @elseif($allIssued && !$allPaid)
                                            <div class="alert alert-warning py-3 mb-0 rounded-pill">
                                                <strong>Waiting for payment at billing counter</strong>
                                            </div>
                                        @elseif($allIssued && $allPaid)
                                            <form action="{{ route('pharmacy.handover.multiple', $visitId) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-lg rounded-pill shadow-lg w-100">
                                                    Hand Over All Medicines
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="bi bi-check-circle-fill text-success display-1 mb-4"></i>
                                    <h3 class="text-success fw-bold">All Clear!</h3>
                                    <p class="text-muted fs-5">No pending prescriptions.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Given to Patients Today -->
            <!-- RIGHT: Given to Patients Today -->
<div class="col-lg-4">
    <div class="card border-0 shadow rounded-4 h-100">
        <div class="card-header text-white py-4" style="background: linear-gradient(90deg, #0d9488, #0f766e);">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0 fw-bold">
                        Given to Patients Today
                        <span class="badge bg-light text-success ms-3">
                            {{ $groupedGivenToday->count() }} patient{{ $groupedGivenToday->count() != 1 ? 's' : '' }}
                        </span>
                    </h5>
                </div>
                <div class="col-auto">
                    <input type="text" id="givenSearchInput" class="form-control form-control-sm" 
                           placeholder="Search patient..." autocomplete="off">
                </div>
            </div>
        </div>
        <div class="card-body p-0" style="max-height: 78vh; overflow-y: auto;">
            <div id="givenList">
                @forelse($groupedGivenToday as $visitId => $orders)
                    @php 
                        $visit = $orders->first()->visit;
                        $patient = $visit->patient;
                        $handoverTime = $orders->first()->handed_over_at ?? $orders->first()->paid_at;
                    @endphp
                    <div class="given-item p-4 border-bottom hover-bg-light"
                         data-patient-name="{{ strtolower($patient->name) }}"
                         data-patient-id="{{ strtolower($patient->patient_id) }}">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-light">
                            <div>
                                <h6 class="fw-bold text-success mb-1">
                                    {{ $patient->name }}
                                    <span class="text-secondary fs-6">({{ $patient->patient_id }})</span>
                                </h6>
                                <small class="text-muted">
                                    Handed over at {{ $handoverTime->format('h:i A') }}
                                    <span class="badge bg-success ms-2">
                                        {{ $orders->count() }} item{{ $orders->count() > 1 ? 's' : '' }}
                                    </span>
                                </small>
                            </div>
                            <i class="bi bi-check-circle-fill text-success fs-2"></i>
                        </div>

                        <!-- Updated medicines list with quantity issued -->
                        <div class="mt-3">
                            @foreach($orders as $order)
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-dark">{{ $order->medicine->medicine_name }}</div>
                                        <div class="text-muted small">
                                            {{ $order->dosage }} × {{ $order->duration_days }} days
                                            @if($order->instruction)
                                                <span class="text-success">• {{ $order->instruction }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end ms-3">
                                        @php
    $issuedQty = $order->pharmacyIssues->sum('quantity_issued');
    $issuedQty = $issuedQty > 0 ? $issuedQty : 1; // fallback only if no issues (shouldn't happen)
@endphp
<span class="badge bg-success fs-6">
    Qty: {{ $issuedQty }}
</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-people-fill display-4 opacity-50"></i>
                        <p class="mt-3">No medicines handed over yet today</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

<!-- Multi-Issue Modal -->
<div class="modal fade" id="issueModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient text-white" style="background: linear-gradient(90deg, #0d9488, #0f766e);">
                <h5 class="modal-title fw-bold">Issue Medicines for <span id="modalPatientInfo"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="multiIssueForm" method="POST">
                @csrf
                <div class="modal-body" id="modalMedicinesList">
                    <!-- Medicines injected via JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-lg px-5 shadow">Issue Selected Medicines</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-gradient { background: linear-gradient(90deg, #0d9488, #0f766e) !important; }
    .hover-bg-light:hover { background-color: #f8f9fa !important; }
    .btn-success { background: linear-gradient(90deg, #0d9488, #0f766e); border: none; }
    .btn-success:hover { background: linear-gradient(90deg, #0a6d63, #0b574f); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(13,148,136,0.4); }
    .text-teal { color: #0d9488 !important; }

    .remove-medicine {
        opacity: 0.7;
        transition: all 0.2s;
    }
    .remove-medicine:hover {
        opacity: 1;
        background-color: #dc3545 !important;
        color: white !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const patientGroups = document.querySelectorAll('.patient-group');
    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        patientGroups.forEach(g => {
            const name = g.dataset.patientName;
            const id = g.dataset.patientId;
            g.style.display = (name.includes(q) || id.includes(q)) ? '' : 'none';
        });
    });

    const givenSearch = document.getElementById('givenSearchInput');
    const givenItems = document.querySelectorAll('.given-item');
    givenSearch.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        givenItems.forEach(i => {
            const name = i.dataset.patientName;
            const id = i.dataset.patientId;
            i.style.display = (name.includes(q) || id.includes(q)) ? '' : 'none';
        });
    });

    window.pendingOrders = @json($groupedPending->flatten());

    const modal = document.getElementById('issueModal');
    const submitBtn = modal.querySelector('button[type="submit"]');

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const visitId = button.dataset.visitId;
        const patientName = button.dataset.patientName;
        const patientId = button.dataset.patientId;

        document.getElementById('modalPatientInfo').textContent = `${patientName} (${patientId})`;

        const medicinesList = document.getElementById('modalMedicinesList');
        medicinesList.innerHTML = '';

        const visitOrders = window.pendingOrders.filter(o => o.visit_id == visitId && !o.is_issued);

        if (visitOrders.length === 0) {
            medicinesList.innerHTML = '<div class="text-center py-5 text-muted">No pending medicines to issue.</div>';
            submitBtn.disabled = true;
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Medicine</th>
                            <th>Dosage & Instruction</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>`;

        visitOrders.forEach(order => {
            const stock = order.medicine.total_stock || 0;
            const price = order.medicine.price;

            html += `
                <tr class="medicine-row" data-order-id="${order.id}">
                    <td>
                        <strong>${order.medicine.medicine_name}</strong>
                        <button type="button" class="btn btn-sm btn-outline-danger float-end remove-medicine" title="Permanently delete from prescription">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                    <td>
                        ${order.dosage} × ${order.duration_days} days
                        ${order.instruction ? '<br><small class="text-success">• ' + order.instruction + '</small>' : ''}
                    </td>
                    <td><span class="badge bg-${stock > 0 ? 'success' : 'danger'}">${stock}</span></td>
                    <td>Tsh <span class="item-price">${price.toLocaleString()}</span></td>
                    <td>
                        <input type="number" 
                               name="quantities[${order.id}]" 
                               class="form-control qty-input" 
                               value="1" 
                               min="1" 
                               max="${stock}" 
                               data-max-stock="${stock}"
                               ${stock == 0 ? 'disabled' : ''} 
                               required>
                        <small class="text-danger qty-warning mt-1" style="display:none;"></small>
                    </td>
                    <td class="item-total fw-bold text-teal">Tsh ${price.toLocaleString()}</td>
                    <input type="hidden" name="order_ids[]" value="${order.id}" class="order-id-input">
                </tr>`;
        });

        html += `
                    </tbody>
                </table>
            </div>
            <div class="text-end mt-4">
                <h3 class="fw-bold text-teal">Grand Total: Tsh <span id="grandTotal">0</span></h3>
            </div>`;

        medicinesList.innerHTML = html;

        document.getElementById('multiIssueForm').action = `/pharmacy/issue-multiple/${visitId}`;

        // Re-attach quantity listeners
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('input', function() {
                validateQuantity(this);
                updateTotals();
            });
        });

        updateTotals();
    });

    // === DELETE MEDICINE FROM PRESCRIPTION (AJAX + DB DELETE) ===
    document.getElementById('modalMedicinesList').addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-medicine');
        if (!removeBtn) return;

        const row = removeBtn.closest('.medicine-row');
        const orderId = row.dataset.orderId;

        if (!confirm('Permanently delete this medicine from the prescription?\n\nThis action cannot be undone.')) {
            return;
        }

        removeBtn.disabled = true;
        removeBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

        fetch(`/pharmacy/order/${orderId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                row.remove();
                updateTotals();
                alert(data.message);

                if (document.querySelectorAll('.medicine-row').length === 0) {
                    document.getElementById('modalMedicinesList').innerHTML = 
                        '<div class="alert alert-info text-center py-5">All prescribed medicines have been removed or issued.</div>';
                    submitBtn.disabled = true;
                }
            } else {
                alert(data.message || 'Failed to delete medicine.');
                removeBtn.disabled = false;
                removeBtn.innerHTML = '<i class="bi bi-trash"></i>';
            }
        })
        .catch(() => {
            alert('Network error. Please try again.');
            removeBtn.disabled = false;
            removeBtn.innerHTML = '<i class="bi bi-trash"></i>';
        });
    });

    function validateQuantity(input) {
        const row = input.closest('.medicine-row');
        const qty = parseInt(input.value) || 0;
        const maxStock = parseInt(input.dataset.maxStock) || 0;
        const warning = row.querySelector('.qty-warning');

        if (qty > maxStock) {
            warning.textContent = `Only ${maxStock} in stock!`;
            warning.style.display = 'block';
            row.classList.add('table-danger');
        } else {
            warning.style.display = 'none';
            row.classList.remove('table-danger');
        }

        toggleSubmitButton();
    }

    function updateTotals() {
        let grand = 0;
        let hasError = false;

        document.querySelectorAll('.qty-input').forEach(input => {
            const row = input.closest('.medicine-row');
            const qty = parseInt(input.value) || 0;
            const price = parseInt(row.querySelector('.item-price').textContent.replace(/,/g, '')) || 0;
            const itemTotal = qty * price;

            row.querySelector('.item-total').textContent = 'Tsh ' + itemTotal.toLocaleString('en-TZ');
            grand += itemTotal;

            if (qty > parseInt(input.dataset.maxStock)) {
                hasError = true;
            }
        });

        document.getElementById('grandTotal').textContent = grand.toLocaleString('en-TZ');
        toggleSubmitButton(hasError);
    }

    function toggleSubmitButton(hasError = false) {
        if (hasError) {
            submitBtn.disabled = true;
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-secondary');
            submitBtn.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Fix quantity errors above';
        } else {
            const rowsLeft = document.querySelectorAll('.medicine-row').length;
            submitBtn.disabled = rowsLeft === 0;
            submitBtn.classList.toggle('btn-secondary', rowsLeft === 0);
            submitBtn.classList.toggle('btn-success', rowsLeft > 0);
            submitBtn.innerHTML = rowsLeft > 0 ? 'Issue Selected Medicines' : 'No Medicines to Issue';
        }
    }
});
</script>
@endsection