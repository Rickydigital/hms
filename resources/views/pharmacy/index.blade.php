@extends('components.main-layout')
@section('title', 'Pharmacy • Mana Dispensary')

@section('content')
<div class="min-vh-100 bg-gradient-to-b from-green-50 to-white">

    <!-- HEADER -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white py-6 shadow-xl">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="display-5 fw-bold mb-1">Pharmacy Module</h1>
                    <p class="lead mb-0 opacity-90">
                        Welcome, <strong>{{ Auth::user()->name }}</strong> • {{ now()->format('d M Y') }}
                    </p>
                </div>
                <div class="col-auto">
                    <div class="text-end">
                        <h3 class="mb-0">Pending: <span class="badge bg-white text-danger fs-4 px-4">{{ $pending->count() }}</span></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-5">
        <div class="row g-5">

            <!-- PENDING PRESCRIPTIONS -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-2xl rounded-4 overflow-hidden">
                    <div class="card-header bg-danger text-white py-4">
                        <h4 class="mb-0 fw-bold">Pending Medicines to Issue</h4>
                    </div>
                    <div class="card-body p-0" style="max-height: 78vh; overflow-y: auto;">
                        @forelse($pending as $order)
                            @php
                                $medicine = $order->medicine;
                                $totalStock = $medicine->currentStock(); // Sum of all available batches
                                $canIssue = $totalStock >= 1;
                            @endphp

                            <div class="p-5 border-bottom hover:bg-gray-50 transition-all">
                                <div class="row align-items-center">
                                    <div class="col-md-7">
                                        <h5 class="fw-bold text-danger text-xl">{{ $medicine->medicine_name }}</h5>
                                        <p class="mb-2">
                                            <strong>Patient:</strong> {{ $order->visit->patient->name }}
                                            <span class="text-muted">({{ $order->visit->patient->patient_id }})</span>
                                        </p>
                                        <small class="text-muted">
                                            Dosage: {{ $order->dosage }} × {{ $order->duration_days }} days
                                        </small>
                                    </div>

                                    <div class="col-md-5">
                                        @if($canIssue)
                                            <form action="{{ route('pharmacy.issue', $order) }}" method="POST" class="row g-3 align-items-center">
                                                @csrf
                                                <div class="col-5">
                                                    <div class="bg-success bg-opacity-10 text-success fw-bold text-center py-3 rounded-3 border border-success">
                                                        Stock: {{ $totalStock }}
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <input type="number" name="quantity_issued" class="form-control form-control-lg text-center fw-bold" 
                                                           value="1" min="1" max="{{ $totalStock }}" required>
                                                </div>
                                                <div class="col-4">
                                                    <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow-lg fw-bold">
                                                        Issue
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="alert alert-danger py-3 mb-0 text-center fw-bold">
                                                OUT OF STOCK
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16">
                                <i class="bi bi-check-circle text-success display-1 opacity-20"></i>
                                <h3 class="mt-4 text-success">All prescriptions issued!</h3>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- TODAY ISSUED -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-2xl rounded-4 h-100">
                    <div class="card-header bg-success text-white py-4">
                        <h5 class="mb-0 fw-bold">Issued Today ({{ $issuedToday->count() }})</h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 78vh; overflow-y: auto;">
                        @forelse($issuedToday as $order)
                            <div class="p-4 border-bottom hover:bg-gray-50">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="text-success">{{ $order->medicine->medicine_name }}</strong><br>
                                        <small class="text-muted">
                                            {{ $order->visit->patient->name }} • {{ $order->issued_at->format('h:i A') }}
                                        </small>
                                    </div>
                                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-muted">
                                No medicines issued today
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover\:bg-gray-50:hover { background-color: #f9fafb !important; }
</style>
@endsection