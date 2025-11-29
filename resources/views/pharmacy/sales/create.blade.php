{{-- resources/views/pharmacy/sales/create.blade.php --}}
@extends('components.main-layout')
@section('title', 'Direct Sale (OTC) • Mana Dispensary')

@section('content')
<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-teal-800 fw-bold">
                <i class="bi bi-cart-plus me-3"></i> Direct Direct Medicine Sale (OTC)
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
                    <!-- Template Row -->
                    <div class="item-row mb-4 p-4 border rounded bg-light">
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
                                            (Stock: {{ $med->currentStock() }} | Tsh {{ number_format((float)$med->price) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Quantity</label>
                                <input type="number" name="items[0][quantity]" class="form-control qty-input" min="1" value="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Unit Price</label>
                                <input type="text" class="form-control price-display" readonly>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                    Remove
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
                <h5 class="mb-0">Payment</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Amount Paid</label>
                        <input type="number" name="amount_paid" id="amountPaid" 
                               class="form-control form-control-lg text-end fw-bold text-teal-700" 
                               value="0" step="0.01" required>
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

{{-- Select2 CSS (use local or CDN - both work) --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

{{-- Load jQuery FIRST, then Select2 --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
let itemIndex = 1;

// Add new row
document.getElementById('addItem').addEventListener('click', function () {
    const container = document.getElementById('itemsContainer');
    const template = container.querySelector('.item-row');
    const newRow = template.cloneNode(true);

    // Update names with new index
    newRow.querySelectorAll('select[name*="medicine_id"], input[name*="quantity"]').forEach(el => {
        const name = el.getAttribute('name');
        el.setAttribute('name', name.replace(/\[\d+\]/, '[' + itemIndex + ']'));
    });

    // Reset values
    newRow.querySelector('.medicine-select').value = '';
    newRow.querySelector('.qty-input').value = 1;
    newRow.querySelector('.price-display').value = '';
    newRow.querySelector('.line-total').textContent = '0';

    container.appendChild(newRow);
    itemIndex++;

    // Initialize Select2 on new row
    $(newRow).find('.medicine-select').select2({
        placeholder: "Type to search medicine...",
        allowClear: true,
        width: '100%'
    });

    attachRowEvents(newRow);
    updateTotals();
});

// Attach events to a row (quantity change, select change, remove)
function attachRowEvents(row) {
    row.querySelector('.medicine-select').addEventListener('change', updateTotals);
    row.querySelector('.qty-input').addEventListener('input', updateTotals);
    row.querySelector('.remove-item').addEventListener('click', function () {
        if (document.querySelectorAll('.item-row').length > 1) {
            row.remove();
            updateTotals();
        }
    });
}

// Update all calculations
function updateTotals() {
    let grandTotal = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        const select = row.querySelector('.medicine-select');
        const option = select.options[select.selectedIndex];
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = option ? parseFloat(option.dataset.price || 0) : 0;
        const lineTotal = qty * price;

        row.querySelector('.price-display').value = price.toLocaleString('en-TZ', { minimumFractionDigits: 0 });
        row.querySelector('.line-total').textContent = lineTotal.toLocaleString('en-TZ');
        grandTotal += lineTotal;
    });

    document.getElementById('grandTotal').textContent = grandTotal.toLocaleString('en-TZ');
    document.getElementById('amountPaid').value = grandTotal;

    calculateChange();
}

// Calculate change
function calculateChange() {
    const total = parseFloat(document.getElementById('grandTotal').textContent.replace(/,/g, '')) || 0;
    const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const change = paid - total;
    document.getElementById('changeDue').textContent = change >= 0 ? change.toLocaleString('en-TZ') : '0';
}

// Initialize everything when page loads
$(document).ready(function () {
    // Initialize Select2 on all medicine selects (including first row)
    $('.medicine-select').select2({
        placeholder: "Type to search medicine...",
        allowClear: true,
        width: '100%'
    });

    // Attach events to all rows
    document.querySelectorAll('.item-row').forEach(attachRowEvents);

    // Listen to amount paid changes
    document.getElementById('amountPaid').addEventListener('input', calculateChange);

    // Initial calculation
    updateTotals();
});
</script>
@endsection