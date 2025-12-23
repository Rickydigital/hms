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
                        <span class="me-2 opacity-75">Pending:</span>
                        <span class="badge bg-warning text-dark fs-4 px-4 py-2">{{ $pending->count() }}</span>
                    </div>
                    <a href="{{ route('pharmacy.history') }}" class="btn btn-light btn-lg shadow-sm">
                        View Past Issues
                    </a>
                    <a href="{{ route('pharmacy.sales.history') }}" class="btn btn-success btn-lg shadow-lg ms-3">
                        Direct Sale (OTC)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-5">
        <div class="row g-5">

            <!-- MAIN: Medicines Workflow -->
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
                            @forelse($pending->merge($readyForCollection) as $order)
                                @php
                                    $medicine = $order->medicine;
                                    $totalStock = $medicine->currentStock();
                                    $unitPrice = $medicine->price;
                                    $canIssue = !$order->is_issued && $totalStock >= 1;
                                    $isIssued = $order->is_issued;
                                    $isPaid = $order->is_paid;
                                    $issuedQty = $order->quantity_issued ?? 1;
                                @endphp

                                <div class="order-item p-4 border-bottom hover-bg-light" 
                                     data-patient-name="{{ strtolower($order->visit->patient->name) }}"
                                     data-patient-id="{{ strtolower($order->visit->patient->patient_id) }}">
                                    <div class="row align-items-center">

                                        <!-- Medicine & Patient Info -->
                                        <div class="col-lg-7">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="fw-bold text-dark mb-0">
                                                    {{ $medicine->medicine_name }}
                                                    @if($medicine->generic_name)
                                                        <small class="text-muted">• {{ $medicine->generic_name }}</small>
                                                    @endif
                                                </h5>
                                                <div>
                                                    @if($isPaid)
                                                        <span class="badge bg-success fs-6">PAID</span>
                                                    @elseif($isIssued)
                                                        <span class="badge bg-warning text-dark fs-6">ISSUED (NOT PAID)</span>
                                                    @else
                                                        <span class="badge bg-info text-white fs-6">PRESCRIBED</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-muted small">
                                                <div class="d-flex align-items-center mb-2">
                                                    Patient: <strong>{{ $order->visit->patient->name }}</strong>
                                                    <span class="text-secondary ms-2">({{ $order->visit->patient->patient_id }})</span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    Dosage: <strong>{{ $order->dosage }}</strong> × {{ $order->duration_days }} days
                                                    @if($order->instruction)
                                                        <span class="text-success ms-2">• {{ $order->instruction }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Button -->
                                        <div class="col-lg-5 text-center">
                                            @if($canIssue)
                                                <button type="button" class="btn btn-success btn-lg rounded-pill shadow w-100"
                                                        data-bs-toggle="modal" data-bs-target="#issueModal"
                                                        data-order-id="{{ $order->id }}"
                                                        data-medicine="{{ $medicine->medicine_name }}"
                                                        data-stock="{{ $totalStock }}"
                                                        data-price="{{ $unitPrice }}"
                                                        data-dosage="{{ $order->dosage }}"
                                                        data-duration="{{ $order->duration_days }}"
                                                        data-instruction="{{ $order->instruction ?? '' }}"
                                                        data-max-qty="{{ $totalStock }}">
                                                    Issue Medicine
                                                </button>
                                            @elseif($isIssued && !$isPaid)
                                                <div class="bg-warning bg-opacity-10 border border-warning rounded-pill p-4 shadow-sm">
                                                    <div class="text-warning fw-bold">Waiting for Payment</div>
                                                    <div class="mt-2">Qty: {{ $order->quantity_issued }} × Tsh {{ number_format($unitPrice, 0) }}</div>
                                                    <strong>Total: Tsh {{ number_format($order->quantity_issued * $unitPrice, 0) }}</strong>
                                                </div>
                                            @elseif($isIssued && $isPaid)
                                                <form action="{{ route('pharmacy.handover', $order) }}" method="POST" class="mt-3">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow-lg">
                                                        Give to Patient
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
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

            <!-- RIGHT: Todays Given -->
            <div class="col-lg-4">
                <div class="card border-0 shadow rounded-4 h-100">
                    <div class="card-header text-white py-4" style="background: linear-gradient(90deg, #0d9488, #0f766e);">
                        <h5 class="mb-0 fw-bold">
                            Given to Patients Today 
                            <span class="badge bg-light text-success ms-3">
                                {{ $givenToday->count() }} order{{ $givenToday->count() != 1 ? 's' : '' }}
                            </span>
                        </h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 78vh; overflow-y: auto;">
                        @forelse($givenToday as $order)
                        <div class="p-4 border-bottom hover-bg-light">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold text-success">
                                        {{ $order->medicine->medicine_name }}
                                        <span class="badge bg-success ms-2">Qty: {{ $order->quantity_issued ?? 1 }}</span>
                                    </div>
                                    <small class="text-muted">
                                        {{ $order->visit->patient->name }} • {{ $order->paid_at->format('h:i A') }}
                                    </small>
                                </div>
                                <i class="bi bi-person-check-fill text-success fs-3"></i>
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

<!-- Issue Medicine Modal -->
<div class="modal fade" id="issueModal" tabindex="-1" aria-labelledby="issueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient text-white" style="background: linear-gradient(90deg, #0d9488, #0f766e);">
                <h5 class="modal-title fw-bold" id="issueModalLabel">Issue Medicine</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="issueForm" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-teal" id="modalMedicineName"></h4>
                        <p class="text-muted">
                            Dosage: <span id="modalDosage"></span> × <span id="modalDuration"></span> days
                            <span id="modalInstruction" class="text-success ms-2"></span>
                        </p>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Available Stock</label>
                            <div class="display-5 text-success fw-bold" id="modalStock"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Unit Price</label>
                            <div class="display-5 text-primary fw-bold">Tsh <span id="modalPrice"></span></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Quantity to Issue</label>
                            <input type="number" name="quantity_issued" id="modalQty" 
                                   class="form-control form-control-lg text-center fw-bold" 
                                   min="1" value="1" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="text-end">
                        <h3 class="fw-bold text-teal">
                            Total Amount: Tsh <span id="modalTotal">0</span>
                        </h3>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-lg px-5 shadow">Issue & Send to Billing</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-gradient { background: linear-gradient(90deg, #0d9488, #0f766e) !important; }
    .hover-bg-light:hover { background-color: #f8f9fa !important; }
    .btn-success {
        background: linear-gradient(90deg, #0d9488, #0f766e);
        border: none;
    }
    .btn-success:hover {
        background: linear-gradient(90deg, #0a6d63, #0b574f);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(13,148,136,0.4);
    }
    .text-teal { color: #0d9488 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Live Search
    const searchInput = document.getElementById('searchInput');
    const orders = document.querySelectorAll('.order-item');

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        orders.forEach(order => {
            const patientName = order.dataset.patientName;
            const patientId = order.dataset.patientId;
            const matches = patientName.includes(query) || patientId.includes(query);
            order.style.display = matches ? '' : 'none';
        });
    });

    // Modal Population & Calculation
    const modal = document.getElementById('issueModal');
    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const orderId = button.dataset.orderId;

        document.getElementById('modalMedicineName').textContent = button.dataset.medicine;
        document.getElementById('modalStock').textContent = button.dataset.stock;
        document.getElementById('modalPrice').textContent = parseInt(button.dataset.price).toLocaleString();
        document.getElementById('modalDosage').textContent = button.dataset.dosage;
        document.getElementById('modalDuration').textContent = button.dataset.duration;
        document.getElementById('modalInstruction').textContent = button.dataset.instruction ? '• ' + button.dataset.instruction : '';

        const qtyInput = document.getElementById('modalQty');
        qtyInput.max = button.dataset.maxQty;
        qtyInput.value = 1;

        const form = document.getElementById('issueForm');
        form.action = `/pharmacy/issue/${orderId}`;

        updateTotal();
    });

    function updateTotal() {
        const qty = parseInt(document.getElementById('modalQty').value) || 0;
        const price = parseInt(document.getElementById('modalPrice').textContent.replace(/,/g, '')) || 0;
        const total = qty * price;
        document.getElementById('modalTotal').textContent = total.toLocaleString('en-TZ');
    }

    document.getElementById('modalQty').addEventListener('input', updateTotal);
});
</script>
@endsection