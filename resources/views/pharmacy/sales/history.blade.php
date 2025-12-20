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
                            <th>Medicine Name</th>
                            <th>Customer</th>
                            <th>Items</th>
                            @role('Admin')<th class="text-end">Total</th>@endrole
                            <th>Served By</th>
                            @role('Admin')<th>Action</th>@endrole
                        </tr>
                    </thead>
                    <tbody>
    @forelse($sales as $sale)
        <tr>
            <td>
                {{ $sale->sold_at->format('d M Y') }}<br>
                <small class="text-muted">{{ $sale->sold_at->format('h:i A') }}</small>
            </td>

            <td>
                @if($sale->items->count() == 1)
                    <span class="badge bg-primary">
                        {{ $sale->items->first()->medicine?->medicine_name ?? 'Deleted Medicine' }}
                    </span>
                @else
                    <span class="badge bg-info">
                        {{ $sale->items->count() }} Medicines
                    </span>
                @endif
            </td>

            <td>
                <strong>{{ $sale->customer_name ?: 'Walk-in' }}</strong>
                @if($sale->customer_phone)
                    <br><small class="text-muted">{{ $sale->customer_phone }}</small>
                @endif
            </td>

            <td>
                <ul class="mb-0 ps-3 small">
                    @foreach($sale->items as $item)
                        <li>
                            <strong>{{ $item->medicine?->medicine_name ?? 'Deleted Medicine' }}</strong>
                            @if($item->medicine?->generic_name)
                                <small class="text-muted">• {{ $item->medicine->generic_name }}</small>
                            @endif
                            <br>
                            <small class="text-muted">
                                Qty: {{ $item->quantity }} 
                            </small>
                        </li>
                    @endforeach
                </ul>
            </td>

            @role('Admin')
            <td class="text-end fw-bold text-teal-700">
                {{ number_format($sale->total_amount) }} Tshs
            </td>
            @endrole

            <td>{{ $sale->soldBy?->name ?? 'Unknown' }}</td>

            @role('Admin')
            <td>
                <a href="{{ route('pharmacy.sales.receipt', $sale) }}" 
                   class="btn btn-sm btn-outline-primary" title="Print Receipt">
                    <i class="bi bi-printer"></i>
                </a>
            </td>
            @endrole
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center py-5 text-muted">
                No OTC sales recorded yet
            </td>
        </tr>
    @endforelse
</tbody>
                </table>
            </div>
            <div class="card-footer bg-light">
                {{ $sales->links('pagination::bootstrap-5')  }}
            </div>
        </div>
    </div>
</div>
@endsection