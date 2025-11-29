{{-- resources/views/pharmacy/sales/create.blade.php --}}
@extends('components.main-layout')
@section('title', 'Direct Sale (OTC) • Mana Dispensary')

@section('content')
<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-teal-800 fw-bold">
                <i class="bi bi-cart-plus me-3"></i> Direct Medicine Sale (OTC)
            </h1>
            <p class="text-muted">Sell medicines to walk-in customers</p>
        </div>
        <a href="{{ route('pharmacy.index') }}" class="btn btn-outline-teal">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <form action="{{ route('pharmacy.sales.store') }}" method="POST" id="saleForm">
        @csrf

        <!-- Customer Info -->
        <div class="card shadow mb-4 border-teal">
            <div class="card-header bg-teal text-white py-3">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i> Customer Information (Optional)</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Customer Name</label>
                        <input type="text" name="customer_name" class="form-control form-control-lg" placeholder="Enter name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Phone Number</label>
                        <input type="text" name="customer_phone" class="form-control form-control-lg" placeholder="e.g. 0755 123 456">
                    </div>
                </div>
            </div>
        </div>

        <!-- Medicine Items -->
        <div class="card shadow mb-4 border-teal">
            <div class="card-header bg-teal text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-prescription2 me-2"></i> Medicines to Sell</h5>
                <button type="button" class="btn btn-light btn-sm" id="addItem">
                    <i class="bi bi-plus-circle"></i> Add Item
                </button>
            </div>
            <div class="card-body">
                <div id="itemsContainer">
                    <!-- First Row (Template) -->
                    <div class="item-row mb-4 p-4 border rounded bg-light position-relative">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Medicine</label>
                                <select name="items[0][medicine_id]" class="form-select form-select-lg medicine-select" required>
                                    <option value="">Type to search medicine...</option>
                                    @foreach(\App\Models\MedicineMaster::active()->orderBy('medicine_name')->get() as $med)
                                        <option value="{{ $med->id }}"
                                                data-price="{{ $med->price }}"
                                                data-stock="{{ $med->currentStock() }}">
                                            {{ $med->medicine_name }}
                                            @if($med->generic_name) • {{ $med->generic_name }} @endif
                                            (Stock: {{ $med->currentStock() }} | Tsh {{ number_format($med->price) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Quantity</label>
                                <input type="number" name="items[0][quantity]" class="form-control form-control-lg qty-input" min="1" value="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Unit Price</label>
                                <input type="text" class="form-control form-control-lg price-display" readonly>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 text-end">
                                <strong>Line Total: Tsh <span class="line-total">0</span></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <h4 class="text-teal-700">
                        Grand Total: Tsh <span id="grandTotal">0</span>
                    </h4>
                </div>
            </div>
        </div>

        <!-- Payment -->
        <div class="card shadow mb-4 border-teal">
            <div class="card-header bg-teal text-white py-3">
                <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i> Payment</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Amount Paid</label>
                        <input type="number" name="amount_paid" id="amountPaid" 
                               class="form-control form-control-lg text-end fw-bold text-teal-700" 
                               value="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Change Due</label>
                        <div class="form-control form-control-lg text-end fw-bold text-success fs-4" id="changeDue">
                            0
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-teal btn-lg w-100 shadow-lg">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Complete Sale & Print Receipt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Select2 CSS & JS (already in your main layout, but safe to include) --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container--default .select2-selection--single {
        height: 48px !important;
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px;
        font-size: 1.1rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
    }
</style>

<script>
let itemIndex = 1;

// Clone and initialize new row
document.getElementById('addItem').addEventListener('click', function () {
    const container = document.getElementById('itemsContainer');
    const firstRow = container.querySelector('.item-row');
    const newRow = firstRow.cloneNode(true);

    // Reset values
    newRow.querySelectorAll('input, select').forEach(el => {
        if (el.name.includes('[medicine_id]')) el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
        if (el.name.includes('[quantity]')) el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
        el.value = el.classList.contains('medicine-select') ? '' : (el.classList.contains('qty-input') ? 1 : '');
    });

    newRow.querySelector('.price-display').value = '';
    newRow.querySelector('.line-total').textContent = '0';

    container.appendChild(newRow);
    itemIndex++;

    // Initialize Select2 on the new select
    $(newRow).find('.medicine-select').select2({
        placeholder: "Type to search medicine...",
        allowClear: true,
        width: '100%'
    });

    updateTotals();
});

// Initialize Select2 on page load
$(document).ready(function () {
    $('.medicine-select').select2({
        placeholder: "Type to search medicine...",
        allowClear: true,
        width: '100%'
    });
});

// Update price, line total, grand total on change
function updateTotals() {
    let grandTotal = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        const select = row.querySelector('.medicine-select');
        const selectedOption = select.options[select.selectedIndex];
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const price = selectedOption ? parseFloat(selectedOption.dataset.price || 0) : 0;
        const lineTotal = qty * price;

        row.querySelector('.price-display').value = price.toLocaleString();
        row.querySelector('.line-total').textContent = lineTotal.toLocaleString();
        grandTotal += lineTotal;
    });

    const grandTotalEl = document.getElementById('grandTotal');
    grandTotalEl.textContent = grandTotal.toLocaleString();

    const amountPaidInput = document.getElementById('amountPaid');
    amountPaidInput.value = grandTotal; // Auto-fill amount paid
    amountPaidInput.dispatchEvent(new Event('input')); // Trigger change for change calculation
}

// Calculate change when amount paid changes
document.getElementById('amountPaid').addEventListener('input', function () {
    const paid = parseFloat(this.value) || 0;
    const total = parseFloat(document.getElementById('grandTotal').textContent.replace(/,/g, '')) || 0;
    const change = paid - total;
    document.getElementById('changeDue').textContent = change >= 0 ? change.toLocaleString() : '0';
});

// Remove row
document.addEventListener('click', function (e) {
    if (e.target.closest('.remove-item') && document.querySelectorAll('.item-row').length > 1) {
        e.target.closest('.item-row').remove();
        updateTotals();
    }
});

// Re-calculate on any change
document.addEventListener('change', updateTotals);
document.addEventListener('input', updateTotals);

// Initial calculation
updateTotals();
</script>
@endsection