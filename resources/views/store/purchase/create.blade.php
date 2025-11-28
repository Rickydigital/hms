@extends('components.main-layout')
@section('title', 'Add Medicine Stock • Mana Dispensary')

@section('content')
<div class="min-h-screen bg-light py-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-10">

                <!-- Header -->
                <div class="card border-0 shadow-sm rounded-3 mb-5">
                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h2 class="mb-2 fw-bold text-dark">Add New Medicine Stock</h2>
                                <p class="text-muted fs-5 mb-0">Auto invoice • Auto batch • Auto expiry (+3 years)</p>
                            </div>
                            <div class="col-lg-4 text-lg-end">
                                <span class="badge bg-primary fs-5 px-5 py-3 rounded-pill shadow">
                                    INV-{{ date('Y') }}-{{ str_pad(\App\Models\MedicinePurchase::count() + 1, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="purchaseForm" action="{{ route('store.purchase.store') }}" method="POST">
                    @csrf

                    <div class="row g-5 mb-5">
                        <!-- Supplier -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-header bg-white border-0 pb-0">
                                    <h5 class="mb-0 fw-bold text-primary">Supplier</h5>
                                </div>
                                <div class="card-body pt-3">
                                    <label class="form-label fw-bold">Select Supplier <span class="text-danger">*</span></label>
                                    <select name="supplier_id" class="form-select form-select-lg rounded-3" required>
                                        <option value="">Choose supplier...</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }} — {{ $s->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Medicine Items -->
                        <div class="col-lg-8">
                            <div class="card border-0 shadow rounded-3">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-bold text-primary">Medicine Items</h5>
                                    <button type="button" id="addItemBtn" class="btn btn-primary px-5">
                                        Add Medicine
                                    </button>
                                </div>

                                <div class="card-body p-0">
                                    <div id="itemsContainer"></div>

                                    <div id="emptyState" class="text-center py-12 text-muted">
                                        <i class="bi bi-capsule fs-1 mb-4 text-primary opacity-20"></i>
                                        <p class="mb-4 fw-medium fs-5">No medicines added yet</p>
                                        <button type="button" id="addFirstItem" class="btn btn-outline-primary px-5">
                                            Add First Medicine
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Discount & Remarks -->
                    <div class="row g-5">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body p-4">
                                    <label class="form-label fw-bold">Discount (Optional)</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-primary text-white fw-bold">Tsh</span>
                                        <input type="number" step="0.01" name="discount" class="form-control fs-5 fw-bold" value="0" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body p-4">
                                    <label class="form-label fw-bold">Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="5" placeholder="Any notes about this purchase..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-5">
                        <button type="submit" class="btn btn-success btn-lg px-6 py-3 fs-5 fw-bold shadow">
                            Save Purchase & Update Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Perfect Item Row Template – SELLING PRICE FROM MASTER ONLY -->
<template id="itemTemplate">
    <div class="item-row border-bottom bg-white position-relative">
        <button type="button" class="btn btn-danger rounded-circle position-absolute top-0 end-0 mt-4 me-4 shadow remove-btn" style="width: 44px; height: 44px; z-index: 10;">
            X
        </button>

        <div class="p-5">
            <!-- Medicine Select -->
            <div class="row mb-4">
                <div class="col-12">
                    <label class="form-label fw-bold text-dark fs-5">Medicine Name</label>
                    <select name="items[INDEX][medicine_id]" class="form-select select2-medicine" data-index="INDEX" required style="width: 100%;">
                        <option value="">Type to search medicine...</option>
                        @foreach($medicines as $m)
                            <option value="{{ $m->id }}" 
                                    data-selling-price="{{ $m->price }}">
                                {{ $m->medicine_name }} @if($m->generic_name) • {{ $m->generic_name }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Batch & Expiry (Auto) -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-success">Batch Number</label>
                    <div class="bg-success bg-opacity-10 text-white fw-bold text-center py-3 rounded-3 border border-success border-opacity-25 fs-5">
                        Auto-generated
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-info">Expiry Date</label>
                    <div class="bg-info bg-opacity-10 text-white fw-bold text-center py-3 rounded-3 border border-info border-opacity-25 fs-5">
                        +3 Years Auto
                    </div>
                </div>
            </div>

            <!-- Quantity -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark fs-5">Quantity</label>
                    <input type="number" name="items[INDEX][quantity]" class="form-control form-control-lg text-center fw-bold fs-4" min="1" value="100" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-primary fs-5">Purchase Price (Buying)</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-primary text-white fw-bold">Tsh</span>
                        <input type="number" step="0.01" name="items[INDEX][purchase_price]" class="form-control text-end fw-bold fs-4 text-primary" placeholder="0.00" required>
                    </div>
                </div>
            </div>

            <!-- SELLING PRICE FROM MASTER – DISPLAY ONLY -->
            <div class="row">
                <div class="col-12">
                    <label class="form-label fw-bold text-success fs-5">
                        Selling Price (Fixed from Master)
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-success text-white fw-bold">Tsh</span>
                        <input type="text" class="form-control text-end fw-bold fs-4 text-success bg-light" 
                               id="selling-price-INDEX" readonly value="">
                        <input type="hidden" name="items[INDEX][selling_price]" id="hidden-selling-price-INDEX">
                    </div>
                    <small class="text-muted">This price is fixed in Medicine Master and cannot be changed here</small>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Styles -->
<style>
    .item-row:hover { background-color: #f8f9fa; }
    .remove-btn:hover { transform: scale(1.15); background-color: #c82333 !important; }
    .select2-container--default .select2-selection--single {
        height: 48px !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.5rem !important;
        padding: 0.375rem 0.75rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        font-size: 1.1rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
    }
</style>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('itemsContainer');
    const emptyState = document.getElementById('emptyState');
    let itemIndex = 0;

    function addItem() {
        const template = document.getElementById('itemTemplate').content.cloneNode(true);
        const row = template.querySelector('.item-row');
        const index = itemIndex++;
        row.innerHTML = row.innerHTML.replace(/INDEX/g, index);
        container.appendChild(row);
        emptyState.style.display = 'none';

        // Initialize Select2
        const select = $(row).find('.select2-medicine');
        select.select2({
            placeholder: "Type to search medicine...",
            allowClear: true,
            width: '100%'
        });

        // When medicine is selected → auto-fill selling price
        select.on('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const sellingPrice = selectedOption.getAttribute('data-selling-price') || 0;

            document.getElementById(`selling-price-${index}`).value = Number(sellingPrice).toLocaleString('en-TZ', {minimumFractionDigits: 0});
            document.getElementById(`hidden-selling-price-${index}`).value = sellingPrice;
        });
    }

    document.getElementById('addItemBtn').onclick = addItem;
    document.getElementById('addFirstItem').onclick = addItem;

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-btn')) {
            e.target.closest('.item-row').remove();
            if (container.children.length === 0) {
                emptyState.style.display = 'block';
            }
        }
    });

    // Form submit: reindex items
    document.getElementById('purchaseForm').onsubmit = function(e) {
        const rows = container.querySelectorAll('.item-row');
        let validCount = 0;

        rows.forEach(row => {
            const select = row.querySelector('select[name$="[medicine_id]"]');
            if (select && select.value) {
                row.querySelectorAll('input[name], select[name]').forEach(el => {
                    el.name = el.name.replace(/\[[0-9]+\]/, `[${validCount}]`);
                });
                validCount++;
            } else {
                row.remove();
            }
        });

        if (validCount === 0) {
            e.preventDefault();
            alert('Please add and select at least one medicine.');
        }
    };

    // Add 2 items on load
    addItem();
    addItem();
});
</script>
@endsection