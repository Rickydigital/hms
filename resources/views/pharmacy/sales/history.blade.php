{{-- resources/views/pharmacy/sales/history.blade.php --}}
@extends('components.main-layout')
@section('title', 'OTC Sales History • Mana Dispensary')

@section('content')
<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-teal-800 fw-bold">
                <i class="bi bi-receipt me-3"></i> Direct Sales (OTC) History
            </h1>
        </div>
        <div>
            <a href="{{ route('pharmacy.sales.create') }}" class="btn btn-teal me-2">
                <i class="bi bi-plus-circle"></i> New Sale
            </a>
            <a href="{{ route('pharmacy.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-teal text-white">
                        <tr>
                            <th>Date & Time</th>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th class="text-end">Total</th>
                            <th>Served By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sale->sold_at->format('d M Y') }}<br><small>{{ $sale->sold_at->format('h:i A') }}</small></td>
                            <td><span class="badge bg-primary">{{ $sale->invoice_no }}</span></td>
                            <td>{{ $sale->customer_name ?: 'Walk-in' }}</td>
                            <td>{{ $sale->items->count() }} item(s)</td>
                            <td class="text-end fw-bold text-teal-700">Tsh {{ number_format($sale->total_amount) }}</td>
                            <td>{{ $sale->soldBy->name }}</td>
                            <td>
                                <a href="{{ route('pharmacy.sales.receipt', $sale) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">No OTC sales yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</div>
@endsection