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

        <!-- Medicine Selection (Separate Row with Select2) -->
        <div class="card shadow mb-4 border-teal">
            <div class="card-header bg-teal text-white py-3">
                <h5 class="mb-0"><i class="bi bi-search me-2"></i> Select Medicine to Add</h5>
            </div>
            <div class="card-body bg-light">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Search Medicine</label>
                        <select id="medicineSearch" class="form-select form-select-lg">
                            <option value="">Type to search medicine...</option>
                            @foreach(\App\Models\MedicineMaster::active()->orderBy('medicine_name')->get() as $med)
                                <option value="{{ $med->id }}"
                                        data-price="{{ $med->price }}"
                                        data-name="{{ $med->medicine_name }} @if($med->generic_name) • {{ $med->generic_name }} @endif"
                                        data-stock="{{ $med->currentStock() }}">
                                    {{ $med->medicine_name }}
                                    @if($med->generic_name) • {{ $med->generic_name }} @endif
                                    (Stock: {{ $med->currentStock() }} | Price: Tsh {{ number_format($med->price) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="addSelectedMedicine" class="btn btn-teal btn-lg w-100">
                            <i class="bi bi-plus-circle"></i> Add
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selected Medicines (Table Style) -->
        <div class="card shadow mb-4 border-teal">
            <div class="card-header bg-teal text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-prescription2 me-2"></i> Medicines in This Sale</h5>
                <span class="badge bg-light text-teal fs-6">Total Items: <span id="itemCount">0</span></span>
            </div>
            <div class="card-body">
                <div id="itemsContainer">
                    <!-- Items will be added here dynamically -->
                </div>

                <div class="text-end mt-4">
                    <h3 class="text-teal-700">
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
                <div class="row g-4">
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

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let itemIndex = 0;

$(document).ready(function() {
    // Initialize Select2
    $('#medicineSearch').select2({
        placeholder: "Type medicine name...",
        allowClear: true,
        width: '100%'
    });

    // Add selected medicine to sale
    $('#addSelectedMedicine').on('click', function() {
        const select = $('#medicineSearch');
        const option = select.find(':selected');

        if (!option.val()) {
            alert('Please select a medicine first');
            return;
        }

        const medId = option.val();
        const medName = option.data('name');
        const price = parseFloat(option.data('price'));
        const stock = parseInt(option.data('stock'));

        // Prevent adding same medicine twice
        if ($(`input[name="items[${itemIndex}][medicine_id]"][value="${medId}"]`).length) {
            alert('This medicine is already added!');
            return;
        }

        const row = `
        <div class="item-row mb-3 p-4 border rounded bg-white shadow-sm">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Medicine</label>
                    <input type="hidden" name="items[${itemIndex}][medicine_id]" value="${medId}">
                    <div class="form-control form-control-lg bg-light">${medName} (Stock: ${stock})</div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Qty</label>
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control qty-input" min="1" max="${stock}" value="1" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Price</label>
                    <input type="text" class="form-control price-display" value="${price.toLocaleString()}" readonly>
                </div>
                <div class="col-md-1">
                    <label class="form-label fw-bold text-danger">Line Total</label>
                    <div class="fs-5 fw-bold text-teal-700 line-total">0</div>
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>`;

        $('#itemsContainer').append(row);
        itemIndex++;

        // Clear selection
        select.val(null).trigger('change');

        updateTotals();
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.item-row').remove();
        updateTotals();
    });

    // Update on quantity change
    $(document).on('input change', '.qty-input', updateTotals);

    function updateTotals() {
        let grandTotal = 0;
        let itemCount = 0;

        $('.item-row').each(function() {
            const qty = parseInt($(this).find('.qty-input').val()) || 0;
            const priceText = $(this).find('.price-display').val().replace(/,/g, '');
            const price = parseFloat(priceText) || 0;
            const lineTotal = qty * price;

            $(this).find('.line-total').text(lineTotal.toLocaleString());
            grandTotal += lineTotal;
            itemCount++;
        });

        $('#grandTotal').text(grandTotal.toLocaleString());
        $('#amountPaid').val(grandTotal);
        $('#changeDue').text('0');
        $('#itemCount').text(itemCount);
    }
});
</script>
@endsection