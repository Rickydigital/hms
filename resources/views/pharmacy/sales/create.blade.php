{{-- resources/views/pharmacy/sales/create.blade.php --}}
@extends('components.main-layout')
@section('title', 'Direct Sale (OTC) • Mana Dispensary')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    .select2-container--bootstrap5 .select2-selection {
        border: 1.5px solid #ced4da;
        border-radius: 0.75rem;
        height: 58px;
        padding-top: 0.65rem;
        font-size: 1.1rem;
    }
    .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0.75rem;
        color: #2d3748;
    }
    .select2-container--bootstrap5.select2-container--focus .select2-selection,
    .select2-container--bootstrap5.select2-container--open .select2-selection {
        border-color: #14b8a6;
        box-shadow: 0 0 0 0.25rem rgba(20, 184, 166, 0.25);
    }
    .select2-dropdown {
        border: 1.5px solid #14b8a6;
        border-radius: 0.75rem;
        margin-top: 0.5rem;
    }
    .item-row {
        background: linear-gradient(135deg, #f8fffe 0%, #f0fdfa 100%);
        border: 2px solid #ccfbf1;
        transition: all 0.3s ease;
    }
    .item-row:hover {
        border-color: #14b8a6;
        box-shadow: 0 10px 25px rgba(20, 184, 166, 0.15);
        transform: translateY(-2px);
    }
    .line-total {
        font-size: 1.4rem;
        font-weight: 800;
        color: #0d9488;
    }
    #grandTotal {
        font-size: 2.2rem;
        font-weight: 900;
        background: linear-gradient(90deg, #0d9488, #14b8a6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 2px 10px rgba(20, 184, 166, 0.3);
    }
    .btn-teal {
        background: linear-gradient(135deg, #0d9488, #14b8a6);
        border: none;
        border-radius: 1rem;
        padding: 1rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-teal:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(20, 184, 166, 0.4);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h2 fw-bold text-teal-800 mb-2">
                <i class="bi bi-cart-plus-fill text-teal-600 me-3"></i>
                Direct Medicine Sale (OTC)
            </h1>
            <p class="text-muted fs-5">Fast & accurate walk-in customer sales</p>
        </div>
        <a href="{{ route('pharmacy.index') }}" class="btn btn-outline-teal btn-lg px-4">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <form action="{{ route('pharmacy.sales.store') }}" method="POST" id="saleForm">
        @csrf

        <!-- Customer Info -->
        <div class="card shadow-lg border-0 mb-5 rounded-4 overflow-hidden">
            <div class="card-header bg-teal text-white py-4 px-5">
                <h4 class="mb-0"><i class="bi bi-person-badge me-3"></i>Customer Information <small class="opacity-75">(Optional)</small></h4>
            </div>
            <div class="card-body p-5">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-teal-700">Customer Name</label>
                        <input type="text" name="customer_name" class="form-control form-control-lg rounded-3" placeholder="e.g. John Doe">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-teal-700">Phone Number</label>
                        <input type="text" name="customer_phone" class="form-control form-control-lg rounded-3" placeholder="e.g. 0755 123 456">
                    </div>
                </div>
            </div>
        </div>

        <!-- Medicine Items -->
        <div class="card shadow-lg border-0 mb-5 rounded-4 overflow-hidden">
            <div class="card-header bg-teal text-white py-4 px-5 d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-prescription2 me-3"></i>Medicines to Sell</h4>
                <button type="button" class="btn btn-light btn-lg shadow-sm px-4" id="addItem">
                    <i class="bi bi-plus-circle-fill me-2"></i>Add Medicine
                </button>
            </div>
            <div class9,card-body p-5">
                <div id="itemsContainer" class="mb-4">
                    <!-- Single Item Row -->
                    <div class="item-row rounded-4 p-4 mb-4 position-relative">
                        <div class="row g-4 align-items-end">
                            <div class="col-lg-6">
                                <label class="form-label fw-bold text-teal-700 mb-3">
                                    <i class="bi bi-search me-2"></i>Search Medicine
                                </label>
                                <select name="items[0][medicine_id]" 
                                        class="medicine-select2 form-select form-select-lg" 
                                        required>
                                    <option value="">Start typing medicine name...</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label fw-bold text-teal-700">Qty</label>
                                <input type="number" name="items[0][quantity]" 
                                       class="form-control form-control-lg text-center qty-input fw-bold" 
                                       min="1" value="1" required>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label fw-bold text-teal-700">Unit Price</label>
                                <input type="text" class="form-control form-control-lg text-end price-display bg-white" readonly>
                            </div>
                            <div class="col-lg-1">
                                <button type="button" class="btn btn-danger btn-lg remove-item shadow-sm">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <h5 class="mb-0">
                                Line Total: <strong class="text-teal-600 line-total">Tsh 0</strong>
                            </h5>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-5 pt-4 border-top border-teal border-3">
                    <h2 class="mb-0">
                        Grand Total: <span class="text-teal-600" id="grandTotal">Tsh 0</span>
                    </h2>
                </div>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-teal text-white py-4 px-5">
                <h4 class="mb-0"><i class="bi bi-cash-coin me-3"></i>Payment Details</h4>
            </div>
            <div class="card-body p-5 bg-gradient" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%)">
                <div class="row g-5 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-teal-800 fs-5">Amount Paid</label>
                        <input type="number" name="amount_paid" id="amountPaid" 
                               class="form-control form-control-lg text-end fs-3 fw-bold text-teal-700 border-3 border-teal" 
                               value="0" readonly required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-teal-800 fs-5">Change Due</label>
                        <div class="bg-white rounded-4 shadow-sm p-4 text-end border border-success border-3">
                            <h3 class="mb-0 text-success fw-bold" id="changeDue">Tsh 0</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" id="submitBtn" class="btn btn-teal btn-lg w-100 shadow-lg py-4 fs-4 fw-bold">
                            <i class="bi bi-printer-fill me-3"></i>
                            Complete Sale & Print Receipt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let itemIndex = 1;

function initSelect2($element) {
    $element.select2({
        theme: 'bootstrap-5',
        placeholder: "🔍 Search by brand, generic, strength...",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: '{{ route("pharmacy.medicines.search") }}',
            dataType: 'json',
            delay: 300,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data }),
            cache: true
        },
        templateResult: data => data.loading 
            ? '<span class="text-muted"><i class="bi bi-hourglass-split"></i> Searching...</span>'
            : `<div class="py-2">
                   <div class="fw-bold text-teal-700">${data.text.split('—')[0]}</div>
                   <small class="text-success">Stock: ${data.stock} units • Price: Tsh ${parseFloat(data.price).toLocaleString()}</small>
               </div>`,
        templateSelection: data => data.text ? data.text.split('—')[0].trim() : "Select medicine..."
    });

    $element.on('select2:select', function(e) {
        const data = e.params.data;
        const $row = $(this).closest('.item-row');
        $row.find('.price-display').val(parseFloat(data.price).toLocaleString());
        updateTotals();
    });

    $element.on('select2:clear', () => updateTotals());
}

$(document).ready(function() {
    initSelect2($('.medicine-select2').first());

    $('#addItem').on('click', function() {
        const $template = $('.item-row').first().clone();
        const newIndex = itemIndex++;

        $template.find('.medicine-select2')
            .attr('name', `items[${newIndex}][medicine_id]`)
            .val(null).empty();

        $template.find('.qty-input')
            .attr('name', `items[${newIndex}][quantity]`)
            .val(1);

        $template.find('.price-display, .line-total').val('').text('Tsh 0');

        $('#itemsContainer').append($template);
        initSelect2($template.find('.medicine-select2'));
    });

    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            updateTotals();
        }
    });

    $(document).on('input', '.qty-input', updateTotals);
});

function updateTotals() {
    let grandTotal = 0;

    $('.item-row').each(function() {
        const $select = $(this).find('.medicine-select2');
        const data = $select.select2('data')[0];
        const qty = parseInt($(this).find('.qty-input').val()) || 0;
        const price = data ? parseFloat(data.price) || 0 : 0;
        const lineTotal = qty * price;

        $(this).find('.price-display').val(price > 0 ? price.toLocaleString() : '');
        $(this).find('.line-total').text('Tsh ' + lineTotal.toLocaleString());
        grandTotal += lineTotal;
    });

    const formatted = grandTotal.toLocaleString();
    $('#grandTotal').text('Tsh ' + formatted);
    $('#amountPaid').val(grandTotal);
    $('#changeDue').text('Tsh 0');
}
</script>
@endpush