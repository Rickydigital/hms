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
                    Add Item
                </button>
            </div>
            <div class="card-body">
                <div id="itemsContainer">
                    <!-- Rows will be added here dynamically -->
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
                <div class="row g-4">
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
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-teal btn-lg w-100 shadow-lg">
                            Complete Sale & Print Receipt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Hidden Template (Invisible - Only for cloning) --}}
{{-- Replace the entire <template> options loop with this cleaner version --}}
<template id="itemTemplate">
    <div class="item-row mb-4 p-4 border rounded bg-light position-relative">
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-bold">Medicine</label>
                <select name="items[0][medicine_id]" class="form-select form-select-lg medicine-select" required>
                    <option value="">Type to search medicine (in stock only)...</option>
                </select>
                <small class="text-muted stock-info mt-1 d-block"></small>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Quantity</label>
                <input type="number" name="items[0][quantity]" class="form-control qty-input" min="1" value="1" required>
                <small class="text-danger qty-warning mt-1" style="display:none;"></small>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Unit Price</label>
                <input type="text" class="form-control price-display" readonly>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12 text-end">
                <strong>Line Total: Tsh <span class="line-total">0</span></strong>
            </div>
        </div>
    </div>
</template>

{{-- Load jQuery + Select2 --}}
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
</style>

<script>
let itemIndex = 0;

function addNewRow() {
    const template = document.getElementById('itemTemplate').content.cloneNode(true);
    const row = template.querySelector('.item-row');

    // Update index
    row.querySelectorAll('[name*="items"]').forEach(el => {
        el.name = el.name.replace('[0]', '[' + itemIndex + ']');
    });

    document.getElementById('itemsContainer').appendChild(row);
    itemIndex++;

    const $select = $(row).find('.medicine-select');
    const $qty = $(row).find('.qty-input');
    const $stockInfo = $(row).find('.stock-info');
    const $qtyWarning = $(row).find('.qty-warning');

    // Initialize Select2 with AJAX
    $select.select2({
        placeholder: "Type to search medicine (in stock only)...",
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("pharmacy.sales.search") }}',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return { q: params.term };
            },
            processResults: function(data) {
                return { results: data };
            }
        }
    });

    // On medicine select
    $select.on('select2:select', function(e) {
        const data = e.params.data;
        const priceInput = row.querySelector('.price-display');
        priceInput.value = parseFloat(data.price).toLocaleString('en-TZ');

        // Show stock
        $stockInfo.text(`Available Stock: ${data.stock}`).removeClass('text-danger text-success')
            .addClass(data.stock > 0 ? 'text-success' : 'text-danger');

        // Set max qty
        $qty.attr('max', data.stock);
        if (parseInt($qty.val()) > data.stock) {
            $qty.val(data.stock);
        }

        validateQuantity();
        updateTotals();
    });

    // Quantity validation
    $qty.on('input', function() {
        validateQuantity();
        updateTotals();
    });

    function validateQuantity() {
        const selected = $select.select2('data')[0];
        const qty = parseInt($qty.val()) || 0;
        const stock = selected ? selected.stock : 0;

        if (selected && qty > stock) {
            $qtyWarning.text(`Only ${stock} in stock!`).show();
            row.classList.add('border-danger');
        } else {
            $qtyWarning.text('').hide();
            row.classList.remove('border-danger');
        }

        toggleSubmitButton();
    }

    // Remove row
    row.querySelector('.remove-item').addEventListener('click', function() {
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
        const $select = $(row).find('.medicine-select');
        const selected = $select.select2('data')[0];
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const price = selected ? parseFloat(selected.price || 0) : 0;
        const lineTotal = qty * price;

        row.querySelector('.price-display').value = price.toLocaleString('en-TZ');
        row.querySelector('.line-total').textContent = lineTotal.toLocaleString('en-TZ');
        grandTotal += lineTotal;

        if (selected && qty > selected.stock) hasError = true;
    });

    document.getElementById('grandTotal').textContent = grandTotal.toLocaleString('en-TZ');
    document.getElementById('amountPaid').value = grandTotal;
    calculateChange();

    toggleSubmitButton(hasError);
}

function toggleSubmitButton(hasError = false) {
    const rows = document.querySelectorAll('.item-row');
    const hasItems = rows.length > 0;
    const anySelected = Array.from(rows).some(r => $(r).find('.medicine-select').val());

    const submitBtn = document.querySelector('button[type="submit"]');
    const error = hasError || !hasItems || !anySelected;

    submitBtn.disabled = error;
    submitBtn.innerHTML = error
        ? '<i class="bi bi-exclamation-triangle"></i> Fix errors or add items'
        : 'Complete Sale & Print Receipt';

    if (error) {
        submitBtn.classList.remove('btn-teal');
        submitBtn.classList.add('btn-secondary');
    } else {
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('btn-teal');
    }
}

function calculateChange() {
    const total = parseFloat(document.getElementById('grandTotal').textContent.replace(/,/g, '')) || 0;
    const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const change = paid - total;
    document.getElementById('changeDue').textContent = change >= 0 ? change.toLocaleString('en-TZ') : '0';
}

$(document).ready(function() {
    addNewRow();

    document.getElementById('addItem').addEventListener('click', addNewRow);
    document.getElementById('amountPaid').addEventListener('input', calculateChange);
});
</script>
@endsection