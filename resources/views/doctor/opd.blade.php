@extends('components.main-layout')
@section('title', 'OPD Doctor Dashboard')
@section('content')
<div class="container-fluid py-4">
    <!-- Header Stats -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="text-primary fw-bold mb-1">
                <i class="bi bi-heart-pulse"></i> OPD Dashboard
            </h3>
            <p class="text-muted">Today: {{ today()->format('d M Y') }} • {{ $todayQueue->count() }} patients waiting</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <button class="btn btn-success rounded-pill px-4 shadow-sm">
                    <i class="bi bi-check2-all"></i> Completed Today: {{ $completedToday }}
                </button>
            </div>
        </div>
    </div>

    <!-- Live Queue -->
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-people-fill"></i> Today’s OPD Queue</h5>
        </div>
        <div class="card-body p-0">
            @forelse($todayQueue as $visit)
            <div class="border-bottom p-4 hover-shadow transition-all" 
                 style="cursor: pointer; background: {{ $loop->first ? '#f8fff8' : '' }}"
                 onclick="openPatientModal({{ $visit->id }})">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg rounded-circle bg-primary text-white fw-bold me-3">
                            {{ strtoupper(substr($visit->patient->name, 0, 2)) }}
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">{{ $visit->patient->name }}</h6>
                            <small class="text-muted">
                                {{ $visit->patient->patient_code }} • 
                                {{ $visit->patient->age }} yrs • 
                                {{ $visit->patient->gender }}
                            </small>
                            <div class="mt-1">
                                <span class="badge bg-warning text-dark small">
                                    <i class="bi bi-clock"></i> {{ $visit->visit_time->diffForHumans() }}
                                </span>
                                @if($visit->vitals)
                                    <span class="badge bg-success small">Vitals Done</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <h5 class="text-primary fw-bold mb-0">
                            #{{ $loop->iteration }}
                        </h5>
                        @if($loop->first)
                            <span class="badge bg-danger animate-pulse">NOW SERVING</span>
                        @endif
                    </div>
                </div>

                @if($visit->vitals?->chief_complaint)
                <div class="mt-3 p-3 bg-light rounded-3">
                    <small class="text-danger fw-bold">Complaint:</small>
                    <span class="text-dark">{{ $visit->vitals->chief_complaint }}</span>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-emoji-smile display-1"></i>
                <h5>No patients in queue</h5>
                <p>Enjoy your coffee, Doctor!</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- PATIENT FULL SCREEN MODAL (THE MAGIC) -->
@foreach($todayQueue as $visit)
<div class="modal fade" id="patientModal{{ $visit->id }}" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white">
                <h4 class="mb-0 fw-bold">
                    <i class="bi bi-person-heart"></i> 
                    {{ $visit->patient->name }} • {{ $visit->patient->patient_code }}
                </h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0" style="min-height: 100vh;">
                    <!-- Left: History & Vitals -->
                    <div class="col-lg-4 bg-light border-end">
                        <!-- Vitals, History, Past Visits -->
                        <div class="p-4">
                            <h5 class="text-primary">Chief Complaint</h5>
                            <p class="bg-white p-3 rounded shadow-sm">
                                {{ $visit->vitals?->chief_complaint ?? 'Not recorded' }}
                            </p>
                        </div>
                    </div>

                    <!-- Center: Prescription -->
                    <div class="col-lg-4">
                        <div class="p-4">
                            <h5 class="text-success fw-bold">Prescription</h5>
                            <form action="{{ route('doctor.opd.prescription', $visit) }}" method="POST">
                                @csrf
                                <textarea name="prescription" rows="15" class="form-control rounded-4" 
                                          placeholder="Type prescription here...&#10;Tab: Paracetamol 650mg 1-1-1 x 5 days&#10;Cap. Amoxiclav 625mg 1-0-1 x 5 days">{{ $visit->prescription?->notes }}</textarea>
                                <button class="btn btn-success rounded-pill mt-3 px-5">Save Prescription</button>
                            </form>
                        </div>
                    </div>

                    <!-- Right: Actions -->
                    <div class="col-lg-4 bg-white">
                        <div class="p-4">
                            <h5 class="text-info">Quick Actions</h5>
                            <div class="d-grid gap-3">
                                <button class="btn btn-outline-primary rounded-pill">Order Lab Tests</button>
                                <button class="btn btn-outline-warning rounded-pill">Refer to Specialist</button>
                                <button class="btn btn-outline-danger rounded-pill">Admit to Ward / IPD</button>
                                <hr>
                                <form action="{{ route('doctor.opd.complete', $visit) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg rounded-pill w-100 shadow">
                                        Complete Consultation & Bill
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
function openPatientModal(id) {
    new bootstrap.Modal(document.getElementById('patientModal' + id)).show();
}

// Auto-refresh queue every 30 seconds
setInterval(() => location.reload(), 30000);
</script>

<style>
.hover-shadow:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important; transform: translateY(-2px); transition: all 0.3s; }
.animate-pulse { animation: pulse 2s infinite; }
</style>
@endsection