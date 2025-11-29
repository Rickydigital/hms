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

                    <!-- Single Item Row -->
                    <div class="item-row mb-4 p-4 border rounded bg-light">
                        <!-- Medicine Selection -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-11">
                                <label class="form-label fw-bold">Medicine</label>
                                <select name="items[0][medicine_id]" class="form-select form-select-lg medicine-select" required>
                                    <option value="">Select medicine...</option>
                                    @foreach(\App\Models\MedicineMaster::active()->orderBy('medicine_name')->get() as $med)
                                        <option value="{{ $med->id }}"
                                                data-price="{{ $med->price }}"
                                                data-stock="{{ $med->currentStock() }}">
                                            {{ $med->medicine_name }}
                                            @if($med->generic_name) • {{ $med->generic_name }} @endif
                                            (Stock: {{ $med->currentStock() }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-danger btn-sm remove-item mt-4">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Quantity & Price (Below Medicine) -->
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Quantity</label>
                                <input type="number" name="items[0][quantity]" class="form-control form-control-lg qty-input" min="1" value="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Unit Price (Tsh)</label>
                                <input type="text" class="form-control form-control-lg price-display" readonly placeholder="0">
                            </div>
                            <div class="col-md-4 text-end">
                                <strong class="fs-5">Line Total: Tsh <span class="line-total text-teal-700">0</span></strong>
                            </div>
                        </div>
                    </div>
                    <!-- End of Item Row -->

                </div>

                <div class="text-end mt-4">
                    <h3 class="text-teal-800 fw-bold">
                        Grand Total: Tsh <span id="grandTotal">0</span>
                    </h3>
                </div>
            </div>
        </div>

        <!-- Payment -->
        <div class="card shadow mb-4 border-teal">
            <div class="card-header bg-teal text-white py-3">
                <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i> Payment</h5>
            </div>
            <div class="card-body">
                <div class="row g-4 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Amount Paid</label>
                        <input type="number" name="amount_paid" id="amountPaid" 
                               class="form-control form-control-lg text-end fw-bold text-teal-700" 
                               value="0" readonly required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Change Due</label>
                        <div class="form-control form-control-lg text-end fw-bold text-success fs-4" id="changeDue">
                            0
                        </div>
                    </div>
                    <div class="col-md-4">
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

<script>
let itemIndex = 1;

// Add New Item Row
document.getElementById('addItem').addEventListener('click', function() {
    const container = document.getElementById('itemsContainer');
    const template = document.querySelector('.item-row');
    const clone = template.cloneNode(true);

    // Reset values
    clone.querySelector('.medicine-select').value = '';
    clone.querySelector('.medicine-select').name = clone.querySelector('.medicine-select').name.replace(/\[\d+\]/, `[${itemIndex}]`);
    clone.querySelector('.qty-input').value = '1';
    clone.querySelector('.qty-input').name = clone.querySelector('.qty-input').name.replace(/\[\d+\]/, `[${itemIndex}]`);
    clone.querySelector('.price-display').value = '';
    clone.querySelector('.line-total').textContent = '0';

    container.appendChild(clone);
    itemIndex++;
});

// Update all calculations
function updateTotals() {
    let grandTotal = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        const select = row.querySelector('.medicine-select');
        const qtyInput = row.querySelector('.qty-input');
        const priceDisplay = row.querySelector('.price-display');
        const lineTotalSpan = row.querySelector('.line-total');

        const selectedOption = select.selectedOptions[0];
        const price = selectedOption ? parseFloat(selectedOption.dataset.price) || 0 : 0;
        const qty = parseInt(qtyInput.value) || 0;
        const lineTotal = price * qty;

        // Auto-fill unit price when medicine is selected
        if (selectedOption && priceDisplay.value === '') {
            priceDisplay.value = price.toLocaleString();
        }

        // Auto-set quantity to 1 if medicine selected and qty is empty
        if (selectedOption && qtyInput.value === '') {
            qtyInput.value = 1;
        }

        lineTotalSpan.textContent = lineTotal.toLocaleString();
        grandTotal += lineTotal;
    });

    document.getElementById('grandTotal').textContent = grandTotal.toLocaleString();
    document.getElementById('amountPaid').value = grandTotal;
    document.getElementById('changeDue').textContent = '0';
}

// Triggers
document.addEventListener('change', updateTotals);
document.addEventListener('input', updateTotals);

// Remove item
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item') && document.querySelectorAll('.item-row').length > 1) {
        e.target.closest('.item-row').remove();
        updateTotals();
    }
});

// Initial calculation
updateTotals();
</script>
@endsection