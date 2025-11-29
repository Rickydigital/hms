{{-- resources/views/pharmacy/sales/create.blade.php --}}
@extends('components.main-layout')
@section('title', 'Direct Sale (OTC) • Mana Dispensary')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        height: 58px;
        border-radius: 12px;
        border: 2px solid #14b8a6;
        font-size: 1.1rem;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        box-shadow: 0 0 0 0.3rem rgba(20, 184, 166, 0.25);
        border-color: #0d9488;
    }
    .select2-dropdown {
        border: 2px solid #14b8a6;
        border-radius: 12px;
        margin-top: 8px;
    }
    .select2-results__option {
        padding: 12px 16px;
        font-size: 1rem;
    }
    .select2-results__option--highlighted {
        background: #ccfbf1 !important;
        color: #0f766e;
    }
</style>
@endpush

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
        <div class="card shadow mb-4 border-teal rounded-3">
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
        <div class="card shadow mb-4 border-teal rounded-3">
            <div class="card-header bg-teal text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Medicines to Sell</h5>
                <button type="button" class="btn btn-light btn-sm" id="addItem">
                    Add Item
                </button>
            </div>
            <div class="card-body">
                <div id="itemsContainer">
                    <div class="item-row mb-4 p-4 border rounded-3 bg-light border-teal">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-teal-700">Medicine (Type to search...)</label>
                                <select name="items[0][medicine_id]" 
                                        class="form-select form-select-lg medicine-select2" 
                                        data-price-placeholder="0" 
                                        required>
                                    <option value="">Type medicine name...</option>
                                    @foreach(\App\Models\MedicineMaster::active()->orderBy('medicine_name')->get() as $med)
                                        <option value="{{ $med->id }}" 
                                                data-price="{{ $med->price }}"
                                                data-stock="{{ $med->currentStock() }}">
                                            {{ $med->medicine_name }}
                                            @if($med->generic_name) • {{ $med->generic_name }} @endif
                                            @if($med->strength) {{ $med->strength }} @endif
                                            (Stock: {{ $med->currentStock() }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Quantity</label>
                                <input type="number" name="items[0][quantity]" class="form-control form-control-lg qty-input text-center" min="1" value="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Price</label>
                                <input type="text" class="form-control form-control-lg price-display text-end fw-bold text-teal" readonly>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                    Trash
                                </button>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 text-end">
                                <strong class="fs-5">Line Total: Tsh <span class="line-total text-teal fw-bold">0</span></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-5 pt-4 border-top border-3 border-teal">
                    <h3 class="text-teal fw-bold">
                        Grand Total: Tsh <span id="grandTotal">0</span>
                    </h3>
                </div>
            </div>
        </div>

        <!-- Payment -->
        <div class="card shadow mb-4 border-teal rounded-3">
            <div class="card-header bg-teal text-white py-3">
                <h5 class="mb-0">Payment</h5>
            </div>
            <div class="card-body bg-light">
                <div class="row g-4 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Amount Paid</label>
                        <input type="number" name="amount_paid" id="amountPaid" 
                               class="form-control form-control-lg text-end fw-bold text-teal-700" 
                               value="0" readonly required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Change Due</label>
                        <div class="form-control form-control-lg text-end fw-bold text-success fs-3 bg-white" id="changeDue">
                            0
                        </div>
                    </div>
                    <div class="col-md-4">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let itemIndex = 1;

    // Initialize Select2 on all medicine dropdowns
    function initSelect2() {
        $('.medicine-select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Type medicine name to search...",
            allowClear: true,
            width: '100%',
            matcher: function(params, data) {
                if ($.trim(params.term) === '') return data;
                const term = params.term.toLowerCase();
                const text = data.text.toLowerCase();
                if (text.includes(term)) return data;
                return null;
            }
        }).on('select2:select', function () {
            const price = $(this).find(':selected').data('price');
            $(this).closest('.item-row').find('.price-display').val(price ? Number(price).toLocaleString() : '');
            updateTotals();
        }).on('select2:clear', function () {
            $(this).closest('.item-row').find('.price-display').val('');
            updateTotals();
        });
    }

    // Initial load
    initSelect2();

    // Add new item
    document.getElementById('addItem').addEventListener('click', function () {
        const container = document.getElementById('itemsContainer');
        const row = document.querySelector('.item-row').cloneNode(true);
        
        // Update names
        row.querySelectorAll('select, input').forEach(el => {
            if (el.name.includes('[medicine_id]')) {
                el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
            }
            if (el.name.includes('[quantity]')) {
                el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
            }
            el.value = el.tagName === 'SELECT' ? '' : (el.classList.contains('qty-input') ? 1 : '');
        });

        row.querySelector('.price-display').value = '';
        row.querySelector('.line-total').textContent = '0';
        
        container.appendChild(row);
        itemIndex++;

        // Re-init Select2 on new row
        $(row).find('.medicine-select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Type medicine name to search...",
            allowClear: true,
            width: '100%'
        }).on('select2:select select2:clear', updateTotals);
    });

    // Remove item
    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-item') && document.querySelectorAll('.item-row').length > 1) {
            e.target.closest('.item-row').remove();
            updateTotals();
        }
    });

    // Update totals
    function updateTotals() {
        let grandTotal = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const select = row.querySelector('.medicine-select2');
            const selected = select.options[select.selectedIndex];
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            const price = selected ? parseFloat(selected.dataset.price) || 0 : 0;
            const lineTotal = qty * price;

            row.querySelector('.price-display').value = price ? price.toLocaleString() : '';
            row.querySelector('.line-total').textContent = lineTotal.toLocaleString();
            grandTotal += lineTotal;
        });

        const formatted = grandTotal.toLocaleString();
        document.getElementById('grandTotal').textContent = formatted;
        document.getElementById('amountPaid').value = grandTotal;
        document.getElementById('changeDue').textContent = '0';
    }

    // Re-calculate on quantity change
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('qty-input')) {
            updateTotals();
        }
    });

    // Initial calculation
    updateTotals();
});
</script>
@endpush