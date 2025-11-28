{{-- resources/views/pharmacy/history.blade.php --}}
@extends('components.main-layout')
@section('title', 'Pharmacy History • Mana Dispensary')

@section('content')
<div class="container-fluid py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-teal-800 fw-bold">
                <i class="bi bi-clock-history me-3"></i> Pharmacy Issue History
            </h1>
            <p class="text-muted">Complete record of all medicines issued from pharmacy</p>
        </div>
        <a href="{{ route('pharmacy.index') }}" class="btn btn-outline-teal">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="card shadow mb-4 border-teal">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-lg" 
                           placeholder="Search patient or medicine..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" class="form-control form-control-lg" 
                           value="{{ request('date') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-teal btn-lg px-5">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <a href="{{ route('pharmacy.history') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- History Table -->
    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-teal text-white">
                        <tr>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Medicine</th>
                            <th>Batch</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Amount</th>
                            <th>Issued By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($issues as $issue)
                        <tr class="hover:bg-teal-50">
                            <td>
                                <div class="fw-bold">{{ $issue->issued_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $issue->issued_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $issue->order->visit->patient->name }}</div>
                                <small class="text-muted">{{ $issue->order->visit->patient->patient_id }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $issue->medicine->medicine_name }}</div>
                                @if($issue->medicine->generic_name)
                                    <small class="text-muted">{{ $issue->medicine->generic_name }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $issue->batch_no }}</span><br>
                                <small>Exp: {{ \Carbon\Carbon::parse($issue->expiry_date)->format('M/y') }}</small>
                            </td>
                            <td class="text-center fw-bold text-xl">{{ $issue->quantity_issued }}</td>
                            <td class="text-end fw-bold text-teal-700">
                                Tsh {{ number_format($issue->total_amount, 0) }}
                            </td>
                            <td>
                                <small>{{ $issue->issuedBy->name }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fa-3x mb-3 opacity-20"></i>
                                <h5>No issue history found</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light">
                @if(is_object($issues) && method_exists($issues, 'links'))
                    {{ $issues->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection