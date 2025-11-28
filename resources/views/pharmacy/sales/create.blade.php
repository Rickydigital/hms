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
                    <div class="item-row mb-4 p-4 border rounded bg-light">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Medicine</label>
                                <select name="items[0][medicine_id]" class="form-select form-select-lg medicine-select" required>
                                    <option value="">Select medicine...</option>
                                    @foreach(\App\Models\MedicineMaster::active()->orderBy('medicine_name')->get() as $med)
                                        <option value="{{ $med->id }}" data-price="{{ $med->price }}">
                                            {{ $med->medicine_name }}
                                            @if($med->generic_name) • {{ $med->generic_name }} @endif
                                            (Stock: {{ $med->currentStock() }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Quantity</label>
                                <input type="number" name="items[0][quantity]" class="form-control form-control-lg qty-input" min="1" value="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Price</label>
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
                               readonly required>
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

<script>
let itemIndex = 1;

document.getElementById('addItem').addEventListener('click', function() {
    const container = document.getElementById('itemsContainer');
    const row = document.querySelector('.item-row').cloneNode(true);
    
    row.querySelectorAll('input, select').forEach(el => {
        el.value = '';
        if (el.name.includes('[medicine_id]')) el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
        if (el.name.includes('[quantity]')) el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
    });
    
    row.querySelector('.line-total').textContent = '0';
    row.querySelector('.price-display').value = '';
    container.appendChild(row);
    itemIndex++;
    updateTotals();
});

document.addEventListener('change', updateTotals);
document.addEventListener('input', updateTotals);

function updateTotals() {
    let grandTotal = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        const select = row.querySelector('.medicine-select');
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(select.selectedOptions[0]?.dataset.price) || 0;
        const lineTotal = qty * price;

        row.querySelector('.price-display').value = price.toLocaleString();
        row.querySelector('.line-total').textContent = lineTotal.toLocaleString();
        grandTotal += lineTotal;
    });

    document.getElementById('grandTotal').textContent = grandTotal.toLocaleString();
    document.getElementById('amountPaid').value = grandTotal.toFixed(2); // Auto-set Amount Paid
    document.getElementById('changeDue').textContent = '0'; // Always 0 since paid = total
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item') && document.querySelectorAll('.item-row').length > 1) {
        e.target.closest('.item-row').remove();
        updateTotals();
    }
});
</script>
@endsection