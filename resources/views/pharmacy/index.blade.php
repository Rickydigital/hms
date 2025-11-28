@extends('components.main-layout')
@section('title', 'Pharmacy • Mana Dispensary')

@section('content')
<div class="min-vh-100 bg-light">

    <!-- HEADER – PURE TEAL THEME (Bootstrap) -->
    <div class="bg-gradient text-white py-5 shadow" style="background: linear-gradient(90deg, #F5F7F6FF, #F7FAFAFF);">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row align-items-center py-3">
                <div class="col">
                    <h1 class="display-5 fw-bold mb-1">
                        <i class="bi bi-prescription2 me-3"></i> Pharmacy Module
                    </h1>
                    <p class="lead mb-0 opacity-75">
                        Welcome, <strong>{{ Auth::user()->name }}</strong> 
                        • {{ now()->format('l, d F Y') }}
                    </p>
                </div>
                <div class="col-auto text-end">
                    <div class="mb-3">
                        <span class="me-2 opacity-75">Pending Prescriptions:</span>
                        <span class="badge bg-warning text-dark fs-4 px-4 py-2">{{ $pending->count() }}</span>
                    </div>
                    <a href="{{ route('pharmacy.history') }}" class="btn btn-light btn-lg shadow-sm">
                        <i class="bi bi-clock-history me-2"></i>
                        <strong>View Past Issues</strong>
                    </a>

                    <a href="{{ route('pharmacy.sales.history') }}" class="btn btn-success btn-lg shadow-lg ms-3">
                        <i class="bi bi-cart-plus me-2"></i>
                        <strong>Direct Sale (OTC)</strong>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-5">
        <div class="row g-5">

            <!-- PENDING MEDICINES -->
            <div class="col-lg-8">
                <div class="card border-0 shadow rounded-4 overflow-hidden h-100">
                    <div class="card-header text-white py-4" style="background: linear-gradient(90deg, #0d9488, #0f766e);">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-prescription2 me-3"></i>
                            Pending Medicines to Issue
                        </h4>
                    </div>

                    <div class="card-body p-0" style="max-height: 78vh; overflow-y: auto;">
                        @forelse($pending as $order)
                            @php
                                $medicine = $order->medicine;
                                $totalStock = $medicine->currentStock();
                                $canIssue = $totalStock >= 1;
                            @endphp

                            <div class="p-4 border-bottom hover-bg-light" style="transition: background 0.3s;">
                                <div class="row align-items-center">

                                    <!-- Medicine & Patient Info -->
                                    <div class="col-lg-8">
                                        <h5 class="fw-bold text-dark mb-3">
                                            {{ $medicine->medicine_name }}
                                            @if($medicine->price > 0)
                                                <span class="text-success fw-normal">
                                                    • {{ number_format($medicine->price) }}/= TSh
                                                </span>
                                            @endif
                                        </h5>

                                        <div class="text-muted small">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-person-fill text-success me-3"></i>
                                                <div>
                                                    <strong>Patient:</strong> {{ $order->visit->patient->name }}
                                                    <span class="text-secondary ms-2">({{ $order->visit->patient->patient_id }})</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar-check text-success me-3"></i>
                                                <div>
                                                    <strong>Dosage:</strong> {{ $order->dosage }} × {{ $order->duration_days }} days
                                                    @if($order->instruction)
                                                        <span class="text-success ms-2">• {{ $order->instruction }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stock + Issue Button -->
                                    <div class="col-lg-4 text-center">
                                        @if($canIssue)
                                            <div class="bg-light border border-success border-3 rounded-pill p-4 mb-4 shadow-sm">
                                                <div class="text-success fw-bold mb-2">Available Stock</div>
                                                <div class="display-5 fw-bold text-teal" style="color:#0d9488">{{ $totalStock }}</div>
                                            </div>

                                            <form action="{{ route('pharmacy.issue', $order) }}" method="POST" class="mt-3">
                                                @csrf
                                                <div class="row g-3 justify-content-center">
                                                    <div class="col-5">
                                                        <input type="number" name="quantity_issued" 
                                                               class="form-control form-control-lg text-center fw-bold border-success"
                                                               value="1" min="1" max="{{ $totalStock }}" required>
                                                    </div>
                                                    <div class="col-7">
                                                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow">
                                                            <i class="bi bi-check-circle-fill me-2"></i>
                                                            Issue Medicine
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        @else
                                            <div class="bg-danger bg-opacity-10 border border-danger border-3 rounded-pill p-4 shadow-sm">
                                                <div class="text-danger fw-bold fs-4 mb-2">OUT OF STOCK</div>
                                                <div class="text-danger">Only {{ $totalStock }} left</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-check-circle-fill text-success display-1 mb-4"></i>
                                <h3 class="text-success fw-bold">All Clear!</h3>
                                <p class="text-muted fs-5">No pending prescriptions. Great job!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- TODAYS ISSUED -->
            <div class="col-lg-4">
                <div class="card border-0 shadow rounded-4 h-100">
                    <div class="card-header text-white py-4" style="background: linear-gradient(90deg, #0d9488, #0f766e);">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-check2-all me-3"></i>
                            Issued Today 
                            <span class="badge bg-light text-success ms-3">{{ $issuedToday->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 78vh; overflow-y: auto;">
                        @forelse($issuedToday as $order)
                            <div class="p-4 border-bottom hover-bg-light">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold text-success">{{ $order->medicine->medicine_name }}</div>
                                        <small class="text-muted">
                                            {{ $order->visit->patient->name }} • {{ $order->issued_at->format('h:i A') }}
                                        </small>
                                    </div>
                                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-clock-history display-4 opacity-50"></i>
                                <p class="mt-3">No medicines issued yet today</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Teal Button & Hover Effects (Bootstrap compatible) -->
<style>
    .bg-gradient { background: linear-gradient(90deg, #0d9488, #0f766e) !important; }
    .hover-bg-light:hover { background-color: #f8f9fa !important; }
    .btn-success {
        background: linear-gradient(90deg, #0d9488, #0f766e);
        border: none;
    }
    .btn-success:hover {
        background: linear-gradient(90deg, #0a6d63, #0b574f);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(13,148,136,0.4);
    }
</style>
@endsection