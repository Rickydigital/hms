{{-- resources/views/pharmacy/sales/create.blade.php --}}
@extends('components.main-layout')
@section('title', 'Direct Sale (OTC) • Mana Dispensary')

@section('content')
<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-teal-800 fw-bold">
                Direct Medicine Sale (OTC)
            </h1>
            <p class="text-muted">Sell medicines to walk-in customers</p>
        </div>
        <a href="{{ route('pharmacy.index') }}" class="btn btn-outline-teal">
            Back to Dashboard
        </a>
    </div>

    <form action="{{ route('pharmacy.sales.store') }}" method="POST" id="saleForm">
        @csrf

        <!-- Customer Info -->
        <div class="card shadow mb-4 border-teal">
            <div class="card-header bg-teal text-white py-3">
                <h5 class="mb-0">Customer Information (Optional)</h5>
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
                <h5 class="mb-0">Medicines to Sell</h5>
                <button type="button" class="btn btn-light btn-sm" id="addItem">
                    <i class="bi bi-plus-lg"></i> Add Item
                </button>
            </div>
            <div class="card-body">
                <div id="itemsContainer">
                    <!-- Dynamic rows added here -->
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
                <h5 class="mb-0">Payment</h5>
            </div>
            <div class="card-body">
                <div class="row g-4 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Amount Paid</label>
                        <input type="number" name="amount_paid" id="amountPaid" 
                               class="form-control form-control-lg text-end fw-bold text-teal-700" 
                               value="0" step="1" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Change Due</label>
                        <div class="form-control form-control-lg text-end fw-bold text-success fs-4" id="changeDue">
                            0
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" id="submitBtn" class="btn btn-teal btn-lg w-100 shadow-lg">
                            <i class="bi bi-check2-circle"></i> Complete Sale & Print Receipt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Hidden Template --}}
<template id="itemTemplate">
    <div class="item-row mb-4 p-4 border rounded bg-light position-relative border">
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-bold">Medicine</label>
                <select name="items[0][medicine_id]" class="form-select form-select-lg medicine-select" required>
                    <option value="">Select medicine (in stock only)...</option>
                    @foreach(\App\Models\MedicineMaster::active()
                        ->withSum('batches', 'current_stock')
                        ->having('batches_sum_current_stock', '>', 0)
                        ->orderBy('medicine_name')
                        ->get() as $med)

                        @php
                            $stock = $med->batches_sum_current_stock ?? 0;
                        @endphp

                        <option value="{{ $med->id }}"
                                data-price="{{ $med->price }}"
                                data-stock="{{ $stock }}">
                            {{ $med->medicine_name }}
                            @if($med->generic_name) • {{ $med->generic_name }} @endif
                            (Stock: {{ $stock }} | Tsh {{ number_format((float)$med->price) }})
                        </option>
                    @endforeach
                </select>
                <small class="text-muted stock-info mt-1 d-none"></small>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Quantity</label>
                <input type="number" name="items[0][quantity]" class="form-control qty-input" min="1" value="1" required>
                <small class="text-danger qty-warning mt-1" style="display:none; font-weight:600;"></small>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Unit Price</label>
                <input type="text" class="form-control price-display" readonly placeholder="0">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 text-end">
                <strong>Line Total: Tsh <span class="line-total">0</span></strong>
            </div>
        </div>
    </div>
</template>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .select2-container--default .select2-selection--single {
        height: 48px !important;
        padding: 8px 12px;
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
        font-size: 1.1rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
    }
    .item-row.border-danger {
        border-color: #dc3545 !important;
        background-color: #fdf0f0;
    }
</style>

<script>
let itemIndex = 0;

function addNewRow() {
    const template = document.getElementById('itemTemplate').content.cloneNode(true);
    const row = template.querySelector('.item-row');

    // Update name indices
    row.querySelectorAll('[name*="items"]').forEach(el => {
        el.name = el.name.replace('[0]', '[' + itemIndex + ']');
    });

    document.getElementById('itemsContainer').appendChild(row);
    itemIndex++;

    const select = row.querySelector('.medicine-select');
    const qtyInput = row.querySelector('.qty-input');
    const priceDisplay = row.querySelector('.price-display');
    const lineTotal = row.querySelector('.line-total');
    const stockInfo = row.querySelector('.stock-info');
    const qtyWarning = row.querySelector('.qty-warning');

    // Initialize Select2
    $(select).select2({
        placeholder: "Select medicine (in stock only)...",
        allowClear: true,
        width: '100%'
    });

    // On change
    select.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (!option.value) {
            priceDisplay.value = '';
            stockInfo.classList.add('d-none');
            lineTotal.textContent = '0';
            qtyInput.removeAttribute('max');
            return;
        }

        const price = parseFloat(option.dataset.price || 0);
        const stock = parseInt(option.dataset.stock || 0);

        priceDisplay.value = price.toLocaleString('en-TZ');
        stockInfo.textContent = `Available Stock: ${stock}`;
        stockInfo.classList.remove('d-none');
        stockInfo.classList.toggle('text-success', stock > 5);
        stockInfo.classList.toggle('text-warning', stock <= 5 && stock > 0);

        qtyInput.max = stock;
        if (parseInt(qtyInput.value) > stock) qtyInput.value = stock;

        validateQuantity();
        updateTotals();
    });

    qtyInput.addEventListener('input', () => {
        validateQuantity();
        updateTotals();
    });

    function validateQuantity() {
        const selectedOption = select.options[select.selectedIndex];
        const qty = parseInt(qtyInput.value) || 0;
        const stock = selectedOption ? parseInt(selectedOption.dataset.stock || 0) : 0;

        if (selectedOption && qty > stock) {
            qtyWarning.textContent = `Only ${stock} in stock!`;
            qtyWarning.style.display = 'block';
            row.classList.add('border-danger');
        } else {
            qtyWarning.style.display = 'none';
            row.classList.remove('border-danger');
        }
        toggleSubmitButton();
    }

    row.querySelector('.remove-item').addEventListener('click', () => {
        row.remove();
        updateTotals();
        toggleSubmitButton();
    });

    updateTotals();
}

function updateTotals() {
    let grandTotal = 0;
    let hasError = false;

    document.querySelectorAll('.item-row').forEach(row => {
        const select = row.querySelector('.medicine-select');
        const option = select.options[select.selectedIndex];
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const price = option ? parseFloat(option.dataset.price || 0) : 0;
        const lineTotalValue = qty * price;

        row.querySelector('.line-total').textContent = lineTotalValue.toLocaleString('en-TZ');
        grandTotal += lineTotalValue;

        if (option && qty > parseInt(option.dataset.stock || 0)) {
            hasError = true;
        }
    });

    document.getElementById('grandTotal').textContent = grandTotal.toLocaleString('en-TZ');
    document.getElementById('amountPaid').value = grandTotal;
    calculateChange();
    toggleSubmitButton(hasError);
}

function toggleSubmitButton(hasError = false) {
    const rows = document.querySelectorAll('.item-row');
    const hasItems = rows.length > 0;
    const hasSelection = Array.from(rows).some(r => r.querySelector('.medicine-select').value);

    const submitBtn = document.getElementById('submitBtn');
    const disabled = hasError || !hasItems || !hasSelection;

    submitBtn.disabled = disabled;

    if (disabled) {
        submitBtn.classList.remove('btn-teal');
        submitBtn.classList.add('btn-secondary');
        submitBtn.innerHTML = hasError
            ? '<i class="bi bi-exclamation-triangle"></i> Insufficient stock in one or more items'
            : '<i class="bi bi-cart"></i> Please add items to proceed';
    } else {
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('btn-teal');
        submitBtn.innerHTML = '<i class="bi bi-check2-circle"></i> Complete Sale & Print Receipt';
    }
}

function calculateChange() {
    const total = parseFloat(document.getElementById('grandTotal').textContent.replace(/,/g, '')) || 0;
    const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const change = paid >= total ? paid - total : 0;
    document.getElementById('changeDue').textContent = change.toLocaleString('en-TZ');
}

$(document).ready(function() {
    addNewRow();
    document.getElementById('addItem').addEventListener('click', addNewRow);
    document.getElementById('amountPaid').addEventListener('input', calculateChange);
});
</script>
@endsection