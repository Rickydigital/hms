@extends('components.main-layout')
@section('title', 'Medicine Purchases • Mana Dispensary')

@section('content')
<div class="min-vh-100 bg-gray-50">

    <!-- CLEAN & ELEGANT HEADER -->
    <div class="bg-white border-bottom shadow-sm">
        <div class="container-fluid py-5">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="mb-1 fw-bold text-dark display-6">
                        Medicine Purchase History
                    </h2>
                    <p class="text-muted mb-0 fs-5">
                        Total Records: <strong class="text-primary">{{ $purchases->total() }}</strong> •
                        Store Manager: <strong>{{ Auth::user()->name }}</strong>
                    </p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('store.purchase.create') }}"
                       class="btn btn-primary rounded-pill px-5 py-3 shadow-lg fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>New Purchase
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-5">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-body p-0">

                <!-- RESPONSIVE TABLE -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <tr>
                                <th class="ps-5 py-4 fw-bold">Date</th>
                                <th class="py-4 fw-bold">Invoice No</th>
                                <th class="py-4 fw-bold">Supplier</th>
                                <th class="py-4 text-center fw-bold">Items</th>
                                <th class="py-4 text-end fw-bold">Total</th>
                                <th class="py-4 text-end fw-bold">Net Amount</th>
                                <th class="py-4 fw-bold">Received By</th>
                                <th class="py-4 text-center fw-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">

                            @forelse($purchases as $purchase)
                                <tr class="border-bottom hover-shadow transition-all">
                                    <td class="ps-5 py-4">
                                        <div class="fw-bold text-dark">{{ $purchase->invoice_date->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $purchase->invoice_date->format('l') }}</small>
                                    </td>
                                    <td class="py-4">
                                        <span class="badge bg-primary rounded-pill px-4 py-2 fw-bold fs-6">
                                            {{ $purchase->invoice_no }}
                                        </span>
                                    </td>
                                    <td class="py-4">
                                        <div class="fw-semibold text-dark">{{ $purchase->supplier->name }}</div>
                                        <small class="text-muted">{{ $purchase->supplier->company_name }}</small>
                                    </td>
                                    <td class="py-4 text-center">
                                        <span class="badge bg-info text-dark rounded-pill px-4 py-3 fs-6 fw-bold">
                                            {{ $purchase->batches->count() }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-end fw-bold text-danger">
                                        Tsh{{ number_format($purchase->total_amount) }}
                                    </td>
                                    <td class="py-4 text-end">
                                        <div class="fw-bold text-success fs-5">
                                            Tsh{{ number_format($purchase->net_amount) }}
                                        </div>
                                        @if($purchase->discount > 0)
                                            <small class="text-success fw-bold">
                                                Saved Tsh{{ number_format($purchase->discount) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="py-4">
                                        <div class="fw-semibold">{{ $purchase->receivedBy?->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $purchase->received_at?->format('h:i A') }}</small>
                                    </td>
                                    <td class="py-4 text-center">
                                        <button class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold shadow-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#view{{ $purchase->id }}">
                                            View
                                        </button>
                                    </td>
                                </tr>

                                <!-- MODAL MOVED OUTSIDE <tr> - NOW EVERY ROW WORKS PERFECTLY -->
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-6">
                                        <div class="py-5">
                                            <div class="text-muted mb-4">
                                                <i class="bi bi-inbox display-1 opacity-25"></i>
                                            </div>
                                            <h4 class="text-muted fw-bold">No purchase records found</h4>
                                            <a href="{{ route('store.purchase.create') }}" 
                                               class="btn btn-primary rounded-pill mt-4 px-5 py-3 fw-bold">
                                                Add First Purchase
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="card-footer bg-white border-top px-5 py-4">
                    {{ $purchases->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- ALL MODALS RENDERED HERE - AFTER THE TABLE (One per purchase) -->
    @foreach($purchases as $purchase)
        <div class="modal fade" id="view{{ $purchase->id }}" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content rounded-4 shadow-xl border-0 overflow-hidden">
                    <!-- Modal Header -->
                    <div class="modal-header border-0 text-white position-relative" 
                         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h4 class="modal-title fw-bold fs-3">
                            Purchase Invoice • {{ $purchase->invoice_no }}
                        </h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-5 bg-light">
                        <div class="row g-5 mb-5">
                            <div class="col-md-6">
                                <h6 class="text-muted text-uppercase small tracking-wider">Supplier</h6>
                                <p class="fw-bold fs-3 mb-1 text-dark">{{ $purchase->supplier->name }}</p>
                                <p class="text-muted fs-5">{{ $purchase->supplier->company_name }}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h6 class="text-muted text-uppercase small tracking-wider">Purchase Date</h6>
                                <p class="fw-bold fs-3 text-dark">{{ $purchase->invoice_date->format('d F Y') }}</p>
                                <p class="text-muted">Received: {{ $purchase->received_at?->format('d M Y • h:i A') }}</p>
                            </div>
                        </div>

                        <h5 class="fw-bold text-primary mb-4">
                            Items Received ({{ $purchase->batches->count() }})
                        </h5>

                        <div class="table-responsive rounded-3 overflow-hidden shadow-sm">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="fw-bold">Medicine</th>
                                        <th class="fw-bold">Batch</th>
                                        <th class="fw-bold">Expiry</th>
                                        <th class="text-center fw-bold">Qty</th>
                                        <th class="text-end fw-bold">Purchase Price</th>
                                        <th class="text-end fw-bold">Selling Price</th>
                                        <th class="text-end fw-bold">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchase->batches as $batch)
                                        <tr class="hover-bg-light">
                                            <td class="fw-semibold">{{ $batch->medicine->medicine_name }}</td>
                                            <td><span class="badge bg-dark">{{ $batch->batch_no }}</span></td>
                                            <td><span class="badge bg-warning text-dark">{{ $batch->expiry_date->format('M Y') }}</span></td>
                                            <td class="text-center fw-bold text-success fs-4">{{ $batch->initial_quantity }}</td>
                                            <td class="text-end">Tsh{{ number_format($batch->purchase_price) }}</td>
                                            <td class="text-end text-success fw-bold">Tsh{{ number_format($batch->selling_price) }}</td>
                                            <td class="text-end fw-bold text-primary">Tsh{{ number_format($batch->purchase_price * $batch->initial_quantity) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($purchase->remarks)
                            <div class="alert alert-info border-start border-primary border-5 mt-4 fs-5">
                                <strong>Note:</strong> {{ $purchase->remarks }}
                            </div>
                        @endif

                        <!-- Total Summary -->
                        <div class="mt-5 p-5 bg-white rounded-4 shadow-sm border">
                            <div class="text-end">
                                <div class="fs-4 mb-2">
                                    <span class="text-muted">Subtotal:</span>
                                    <strong class="text-danger ms-3 fs-3">Tsh{{ number_format($purchase->total_amount) }}</strong>
                                </div>
                                @if($purchase->discount > 0)
                                    <div class="text-success fs-4 mb-3 fw-bold">
                                        Discount: <span class="fs-3">-Tsh{{ number_format($purchase->discount) }}</span>
                                    </div>
                                @endif
                                <div class="fs-2 fw-black text-primary">
                                    Net Amount: Tsh{{ number_format($purchase->net_amount) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Print Footer -->
                    <div class="modal-footer border-0 bg-light py-4">
                        <button onclick="window.print()" 
                                class="btn btn-success rounded-pill px-5 py-3 fw-bold shadow-lg">
                            Print Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
    .hover-shadow:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important; }
    .hover-bg-light:hover { background-color: #f8f9ff !important; }
    .transition-all { transition: all 0.3s ease; }
    .table thead th { letter-spacing: 0.5px; }
    .card { border-radius: 1.5rem !important; }

    @media print {
        body * { visibility: hidden; }
        .modal-content, .modal-content * { visibility: visible; }
        .modal-content {
            position: absolute;
            left: 0; top: 0; right: 0;
            box-shadow: none; border: none;
        }
        .modal-header { background: white !important; color: black !important; }
        .modal-footer { display: none; }
    }
</style>
@endsection