@extends('components.main-layout')
@section('title', 'Lab • Mana Dispensary')

@section('content')
<div class="min-vh-100 bg-light">
    <!-- TOP HEADER -->
    <div class="bg-primary text-white py-5 shadow-sm">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="display-5 fw-bold mb-1">Laboratory Module</h1>
                    <p class="lead mb-0 opacity-90">Welcome back, {{ Auth::user()->name }}</p>
                </div>
                <div class="col-auto">
                    <div class="text-end">
                        <h5 class="mb-0">{{ now()->format('d M Y') }}</h5>
                        <small>{{ now()->format('l') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="row g-4">

            <!-- LEFT: SEARCH HISTORY + TODAYS COMPLETED -->
            <div class="col-lg-5">
                <div class="sticky-top" style="top: 20px;">

                    <!-- SEARCH PATIENT LAB HISTORY -->
                    <div class="card border-0 shadow-lg rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom-0 py-4">
                            <h5 class="mb-0 text-primary fw-bold">
                                Search Patient Lab History
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('lab.index') }}" method="GET">
                                <div class="input-group input-group-lg shadow-sm">
                                    <span class="input-group-text bg-white border-end-0">Search</span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                           placeholder="Patient ID • Name • Phone" 
                                           value="{{ request('search') }}" autofocus>
                                    <button class="btn btn-primary">Go</button>
                                </div>
                            </form>

                            @if(request('search'))
                                @if($history->count())
                                    <div class="mt-4" style="max-height: 60vh; overflow-y: auto;">
                                        @foreach($history as $item)
                                            <div class="border rounded-3 p-3 mb-3 bg-white shadow-sm">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-0 text-success fw-bold">{{ $item['patient']->name }}</h6>
                                                        <small class="text-muted">{{ $item['patient']->patient_id }} • {{ $item['patient']->phone }}</small>
                                                    </div>
                                                </div>
                                                <div class="small">
                                                    @foreach($item['orders']->take(5) as $order)
                                                        <div class="d-flex justify-content-between py-1 border-bottom border-light">
                                                            <span class="text-dark">
                                                                <strong>{{ $order->test->test_name }}</strong>
                                                                <br><small class="text-muted">{{ $order->visit->created_at->format('d M Y') }}</small>
                                                            </span>
                                                            <span>
                                                                @if($order->is_completed)
                                                                    <span class="badge rounded-pill bg-{{ $order->result?->is_abnormal ? 'danger' : 'success' }}">
                                                                        {{ $order->result?->is_abnormal ? 'Abnormal' : 'Normal' }}
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-{{ $order->is_paid ? 'warning' : 'secondary' }} text-dark">
                                                                        {{ $order->is_paid ? 'Pending' : 'Not Paid' }}
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                    @if($item['orders']->count() > 5)
                                                        <small class="text-muted">+{{ $item['orders']->count() - 5 }} more...</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1"></i>
                                        <p class="mt-3">No lab history found</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- TODAY'S COMPLETED -->
                    <div class="card border-0 shadow-lg rounded-4">
                        <div class="card-header bg-success text-white py-4 rounded-top-4">
                            <h5 class="mb-0">Today's Completed ({{ $completedToday->count() }})</h5>
                        </div>
                        <div class="card-body p-0" style="max-height: 50vh; overflow-y: auto;">
                            @forelse($completedToday as $order)
                                <div class="px-4 py-3 border-bottom hover-bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="flex-grow-1">
                                            <strong class="text-success">{{ $order->test->test_name }}</strong><br>
                                            <small class="text-muted">
                                                {{ $order->visit->patient->name }} • {{ $order->completed_at->format('h:i A') }}
                                            </small>
                                        </div>
                                        <div>
                                            @if($order->result?->is_abnormal)
                                                <span class="badge bg-danger fs-6">Abnormal</span>
                                            @else
                                                <span class="badge bg-success fs-6">Normal</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    No tests completed today
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: PENDING TESTS (MAIN WORK AREA) -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg rounded-4 h-100">
                    <div class="card-header bg-primary text-white py-4 rounded-top-4 d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Pending Tests • {{ $pending->count() }}</h4>
                        <span class="badge bg-white text-primary fs-5">{{ $pending->count() }} Active</span>
                    </div>
                    <div class="card-body p-4" style="max-height: 85vh; overflow-y: auto;">
                        @forelse($pending as $order)
                            <div class="card mb-4 border-0 shadow-sm rounded-4 
                                {{ $order->is_paid ? 'border-start border-success border-5' : 'border-start border-danger border-5 opacity-75' }}">
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-start justify-content-between mb-3">
                                                <h5 class="text-primary mb-0">{{ $order->test->test_name }}</h5>
                                                <div>
                                                    @if($order->is_paid)
                                                        <span class="badge bg-success fs-6">PAID</span>
                                                    @else
                                                        <span class="badge bg-danger fs-6">NOT PAID</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Patient:</strong> {{ $order->visit->patient->name }}
                                                <span class="text-muted">• {{ $order->visit->patient->patient_id }}</span>
                                            </div>
                                            <div class="small text-muted">
                                                Ordered: {{ $order->visit->created_at->format('d M Y • h:i A') }}
                                                @if($order->extra_instruction)
                                                    <br><span class="text-info fw-bold">Note: {{ $order->extra_instruction }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            @if($order->is_paid)
                                                <a href="{{ route('lab.order.show', $order) }}" 
                                                   class="btn btn-success btn-lg rounded-pill px-5 shadow">
                                                    Enter Result
                                                </a>
                                            @else
                                                <button class="btn btn-secondary btn-lg rounded-pill px-5" disabled>
                                                    Payment Required
                                                </button>
                                                <small class="d-block text-danger mt-2">
                                                    Patient must pay at billing first
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="bi bi-cash-coin text-warning" style="font-size: 4rem;"></i>
                                </div>
                                <h3 class="text-warning fw-bold">No Paid Tests Yet</h3>
                                <p class="text-muted">Waiting for patients to complete payment at billing counter.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: all 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
}
.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}
.rounded-4 {
    border-radius: 1rem !important;
}
.opacity-75 {
    opacity: 0.75;
}
</style>
@endsection