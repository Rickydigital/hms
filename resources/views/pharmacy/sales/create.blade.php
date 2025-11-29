{{-- resources/views/pharmacy/sales/create.blade.php --}}
@extends('components.main-layout')

@section('title', 'Direct Sale (OTC) • Mana Dispensary')

{{-- MOVE ALL JS/CSS TO THE BOTTOM (or to layout) --}}
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 48px !important;
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        font-size: 1.25rem;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-5">
    <!-- Your HTML exactly the same -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-teal-800 fw-bold">Direct Medicine Sale (OTC)</h1>
            <p class="text-muted">Sell medicines to walk-in customers</p>
        </div>
        <a href="{{ route('pharmacy.index') }}" class="btn btn-outline-teal">Back to Dashboard</a>
    </div>

    <form action="{{ route('pharmacy.sales.store') }}" method="POST" id="saleForm">
        @csrf

        <!-- Customer Info (unchanged) -->
        <div class="card shadow mb-4 border-teal">
            <div class="card-header bg-teal text-white py-3">
                <h5 class="mb-0">Customer Information (Optional)</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Customer Name</label>
                        <input type="text" name="customer_name" class="form-control form-control-lg">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Phone Number</label>
                        <input type="text" name="customer_phone" class="form-control form-control-lg">
                    </div>
                </div>
            </div>
        </div>

        <!-- Medicines -->
        <div class="card shadow mb-4 border-teal">
            <div class="card-header bg-teal text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Medicines to Sell</h5>
                <button type="button" class="btn btn-light btn-sm" id="addItem">Add Item</button>
            </div>
            <div class="card-body">
                <div id="itemsContainer">
                    <div class="item-row mb-4 p-4 border rounded bg-light">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Medicine</label>
                                <select name="items[0][medicine_id]" class="form-select form-select-lg medicine-select" required>
                                    <option value="">Type to search...</option>
                                    @foreach(\App\Models\MedicineMaster::active()->orderBy('medicine_name')->get() as $med)
                                        <option value="{{ $med->id }}"
                                            data-price="{{ $med->price }}"
                                            data-name="{{ $med->medicine_name }}"
                                            data-generic="{{ $med->generic_name ?? '' }}">
                                            {{ $med->medicine_name }} @if($med->generic_name) • {{ $med->generic_name }} @endif
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
                                <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
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
                    <h4 class="text-teal-700">Grand Total: Tsh <span id="grandTotal">0</span></h4>
                </div>
            </div>
        </div>

        <!-- Payment section unchanged -->
        <div class="card shadow mb-4 border-teal">
            <div class="card-header bg-teal text-white py-3">
                <h5 class="mb-0">Payment</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Amount Paid</label>
                        <input type="number" name="amount_paid" id="amountPaid" class="form-control form-control-lg text-end fw-bold text-teal-700" readonly required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Change Due</label>
                        <div class="form-control form-control-lg text-end fw-bold text-success fs-4" id="changeDue">0</div>
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
@endsection

{{-- LOAD SCRIPTS AT THE VERY END --}}
@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let itemIndex = 1;

function initSelect2() {
    $('.medicine-select').select2({
        placeholder: "Type medicine or generic name...",
        allowClear: true,
        width: '100%',
        templateResult: item => {
            if (!item.id) return item.text;
            const name = item.element.dataset.name;
            const generic = item.element.dataset.generic;
            const stock = item.text.match(/\(Stock:[^)]+\)/)?.[0] || '';
            return $(`<div class="d-flex justify-content-between"><div><strong>${name}</strong>${generic ? ' <small class="text-muted">• '+generic+'</small>' : ''}</div><span class="badge bg-primary">${stock}</span></div>`);
        },
        templateSelection: item => item.id ? (item.element.dataset.name + (item.element.dataset.generic ? ' • ' + item.element.dataset.generic : '')) : "Type to search..."
    });
}

// Add row
$('#addItem').on('click', function() {
    let row = $('.item-row').first().clone();
    let idx = itemIndex++;

    row.find('.medicine-select').attr('name', `items[${idx}][medicine_id]`).val('').trigger('change.select2');
    row.find('.qty-input').attr('name', `items[${idx}][quantity]`).val(1);
    row.find('.price-display').val('');
    row.find('.line-total').text('0');

    $('#itemsContainer').append(row);
    initSelect2();
    updateTotals();
});

// Calculate
function updateTotals() {
    let total = 0;
    $('.item-row').each(function() {
        let select = $(this).find('.medicine-select')[0];
        let qty = parseInt($(this).find('.qty-input').val()) || 0;
        let price = select.selectedIndex > 0 ? parseFloat(select.options[select.selectedIndex].dataset.price) || 0 : 0;
        let line = qty * price;

        $(this).find('.price-display').val(price > 0 ? price.toLocaleString() : '');
        $(this).find('.line-total').text(line.toLocaleString());
        total += line;
    });

    $('#grandTotal').text(total.toLocaleString());
    $('#amountPaid').val(total);
}

// Remove row
$(document).on('click', '.remove-item', function() {
    if ($('.item-row').length > 1) $(this).closest('.item-row').remove();
    updateTotals();
});

// Start
$(function() {
    initSelect2();
    updateTotals();

    $(document).on('change', '.medicine-select, .qty-input', updateTotals);
});
</script>
@endsection