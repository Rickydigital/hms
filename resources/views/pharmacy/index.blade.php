@extends('components.main-layout')
@section('title', 'Pharmacy • Mana Dispensary')

@section('content')
<div class="min-vh-100 bg-light">

    <!-- HEADER -->
    <div class="bg-gradient text-white py-5 shadow" style="background: linear-gradient(90deg, #0d9488, #0f766e);">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row align-items-center py-3">
                <div class="col">
                    <h1 class="display-5 fw-bold mb-1">
                        Pharmacy Module
                    </h1>
                    <p class="lead mb-0 opacity-75">
                        Welcome, <strong>{{ Auth::user()->name }}</strong> 
                        • {{ now()->format('l, d F Y') }}
                    </p>
                </div>
                <div class="col-auto text-end">
                    <div class="mb-3">
                        <span class="me-2 opacity-75">Pending:</span>
                        <span class="badge bg-warning text-dark fs-4 px-4 py-2">{{ $pending->count() }}</span>
                    </div>
                    <a href="{{ route('pharmacy.history') }}" class="btn btn-light btn-lg shadow-sm">
                        View Past Issues
                    </a>
                    <a href="{{ route('pharmacy.sales.history') }}" class="btn btn-success btn-lg shadow-lg ms-3">
                        Direct Sale (OTC)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-5">
        <div class="row g-5">

            <!-- MAIN: PENDING + READY FOR COLLECTION -->
            <div class="col-lg-8">
                <div class="card border-0 shadow rounded-4 overflow-hidden h-100">
                    <div class="card-header text-white py-4" style="background: linear-gradient(90deg, #0d9488, #0f766e);">
                        <h4 class="mb-0 fw-bold">
                            Medicines Workflow
                        </h4>
                    </div>

                    <div class="card-body p-0" style="max-height: 78vh; overflow-y: auto;">
                        @forelse($pending->merge($readyForCollection) as $order)
                            @php
                                $medicine = $order->medicine;
                                $totalStock = $medicine->currentStock();
                                $canIssue = !$order->is_issued && $totalStock >= 1;
                                $isIssued = $order->is_issued;
                                $isPaid = $order->is_paid;
                            @endphp

                            <div class="p-4 border-bottom hover-bg-light" style="transition: all 0.3s;">
                                <div class="row align-items-center">

                                    <!-- Medicine & Patient Info -->
                                    <div class="col-lg-7">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="fw-bold text-dark mb-0">
                                                {{ $medicine->medicine_name }}
                                                @if($medicine->generic_name)
                                                    <small class="text-muted">• {{ $medicine->generic_name }}</small>
                                                @endif
                                            </h5>
                                            <div>
                                                @if($isPaid)
                                                    <span class="badge bg-success fs-6">PAID</span>
                                                @elseif($isIssued)
                                                    <span class="badge bg-warning text-dark fs-6">ISSUED (NOT PAID)</span>
                                                @else
                                                    <span class="badge bg-info text-white fs-6">PRESCRIBED</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="text-muted small">
                                            <div class="d-flex align-items-center mb-2">
                                                Patient: <strong>{{ $order->visit->patient->name }}</strong>
                                                <span class="text-secondary ms-2">({{ $order->visit->patient->patient_id }})</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                Dosage: <strong>{{ $order->dosage }}</strong> × {{ $order->duration_days }} days
                                                @if($order->instruction)
                                                    <span class="text-success ms-2">• {{ $order->instruction }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stock + Actions -->
                                    <div class="col-lg-5 text-center">
                                        @if($canIssue)
                                            <!-- 1. Not issued yet → Show Issue Form -->
                                            <div class="bg-light border border-success border-3 rounded-pill p-4 mb-4 shadow-sm">
                                                <div class="text-success fw-bold mb-2">Available Stock</div>
                                                <div class="display-5 fw-bold text-teal">{{ $totalStock }}</div>
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
                                                            Issue Medicine
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>

                                        @elseif($isIssued && !$isPaid)
                                            <!-- 2. Issued but not paid → Waiting for payment -->
                                            <div class="bg-warning bg-opacity-10 border border-warning border-3 rounded-pill p-4 shadow-sm mb-3">
                                                <div class="text-warning fw-bold fs-5">
                                                    Medicine Issued
                                                </div>
                                                <div class="text-dark">Waiting for payment at billing...</div>
                                            </div>
                                            <button class="btn btn-secondary btn-lg w-100 rounded-pill" disabled>
                                                Payment Required
                                            </button>

                                        @elseif($isIssued && $isPaid)
                                            <!-- 3. Issued + Paid → Final Handover -->
                                            <div class="bg-success bg-opacity-10 border border-success border-3 rounded-pill p-4 shadow-sm mb-3">
                                                <div class="text-success fw-bold fs-5">
                                                    Payment Received
                                                </div>
                                                <div class="text-dark">Ready for collection</div>
                                            </div>

                                            <form action="{{ route('pharmacy.handover', $order) }}" method="POST" class="mt-3">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow-lg">
                                                    Give Medicine to Patient
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-check-circle-fill text-success display-1 mb-4"></i>
                                <h3 class="text-success fw-bold">All Clear!</h3>
                                <p class="text-muted fs-5">No pending prescriptions.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- RIGHT: TODAYS GIVEN TO PATIENTS -->
            <div class="col-lg-4">
                <div class="card border-0 shadow rounded-4 h-100">
                    <div class="card-header text-white py-4" style="background: linear-gradient(90deg, #0d9488, #0f766e);">
                        <h5 class="mb-0 fw-bold">
                            Given to Patients Today 
                            <span class="badge bg-light text-success ms-3">{{ $givenToday->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 78vh; overflow-y: auto;">
                        @forelse($givenToday as $order)
                            <div class="p-4 border-bottom hover-bg-light">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold text-success">{{ $order->medicine->medicine_name }}</div>
                                        <small class="text-muted">
                                            {{ $order->visit->patient->name }} • {{ $order->paid_at->format('h:i A') }}
                                        </small>
                                    </div>
                                    <i class="bi bi-person-check-fill text-success fs-3"></i>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-people-fill display-4 opacity-50"></i>
                                <p class="mt-3">No medicines handed over yet today</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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