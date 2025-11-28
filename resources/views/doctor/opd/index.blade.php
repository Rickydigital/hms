@extends('components.main-layout')
@section('title', 'OPD • Mana Dispensary')

@section('content')

<style>
    .opd-page {
        --primary: #4361ee;
        --success: #2ecc71;
        --danger: #e74c3c;
        --warning: #f39c12;
        --info: #3498db;
        --purple: #9b59b6;
        --light: #f8f9fa;
        --dark: #2c3e50;
        --radius: 1rem;
        --shadow: 0 10px 30px rgba(67,97,238,0.12);
    }

    .opd-page .card {
        border: none !important;
        border-radius: var(--radius) !important;
        box-shadow: var(--shadow) !important;
        transition: all 0.3s ease;
    }

    .opd-page .card:hover { transform: translateY(-6px); }

    .opd-page .bg-gradient-primary {
        background: linear-gradient(135deg, var(--primary), #3f37c9) !important;
    }

    .opd-page .vital-badge {
        width: 70px; height: 70px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .opd-page .vital-badge:hover {
        transform: scale(1.1);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }

    .opd-page .item-card {
        background: linear-gradient(135deg, #f8faff, #f0f4ff);
        border: 2px solid #e3e8ff;
        border-radius: 18px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        position: relative;
        transition: all 0.3s ease;
    }

    .opd-page .item-card:hover {
        background: linear-gradient(135deg, #eef1ff, #e6eafc);
        border-color: var(--primary);
        transform: translateY(-4px);
    }

    .opd-page .remove-item {
        position: absolute;
        top: 12px; right: 12px;
        width: 36px; height: 36px;
        border-radius: 50%;
        background: var(--danger);
        color: white;
        border: none;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .opd-page .section-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 1.2rem;
        padding-bottom: 0.6rem;
        border-bottom: 3px solid #eee;
    }
</style>

<div class="opd-page container-fluid py-4 py-md-5">
    <div class="row g-4 g-xl-5">

        <!-- LEFT SIDEBAR -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 20px;">

                <!-- Search Patient -->
                <div class="card mb-4">
                    <div class="card-header bg-gradient-primary text-white rounded-top">
                        <h5 class="mb-0">Search Patient</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('doctor.opd') }}" method="GET">
                            <div class="input-group input-group-lg">
                                <input type="text" name="patient_id" class="form-control" 
                                       placeholder="CWH2025-000001" value="{{ request('patient_id') }}" required autofocus>
                                <button class="btn btn-primary btn-lg">Go</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Today's Queue -->
                <div class="card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center rounded-top">
                        <h5 class="mb-0">Today's Queue <span class="badge bg-light text-success ms-2">{{ $todayVisits->count() }}</span></h5>
                        <small>{{ now()->format('d M Y') }}</small>
                    </div>
                    <div class="list-group list-group-flush" style="max-height: 70vh; overflow-y: auto;">
                        @forelse($todayVisits as $v)
                            <a href="{{ route('doctor.opd.show', $v) }}" 
                               class="list-group-item list-group-item-action px-4 py-3 {{ $visit && $visit->id == $v->id ? 'bg-primary text-white' : '' }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $v->patient->patient_id }}</strong> • {{ $v->visit_time->format('h:i A') }}<br>
                                        <b>{{ $v->patient->name }}</b> • {{ $v->patient->age }} yrs
                                    </div>
                                    <span class="badge bg-{{ $v->status == 'consulting' ? 'warning' : ($v->status == 'sent_to_lab' ? 'info' : 'primary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $v->status)) }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5 text-muted">No patients yet<br><small>Enjoy your coffee, Doctor!</small></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN CONSULTATION -->
        <div class="col-lg-8">
            @if($visit ?? false)

                <div class="card mb-4">
                    <div class="card-header bg-gradient-primary text-white py-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1">OPD Consultation</h3>
                                <p class="mb-0 opacity-90">Token #{{ $visit->id }} • {{ $visit->created_at->format('d M Y • h:i A') }}</p>
                            </div>
                            <span class="badge bg-white text-primary fs-5 px-4 py-2">{{ ucfirst(str_replace('_', ' ', $visit->status)) }}</span>
                        </div>
                    </div>

                    <div class="card-body p-4 p-xl-5">

                        <!-- PATIENT + VITALS (Compact + Modal) -->
                        <div class="row g-4 mb-5">
                            <div class="col-xl-7">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary mb-4">Patient Details</h5>
                                        <h4 class="mb-1">{{ $visit->patient->name }}</h4>
                                        <p class="text-muted mb-3">{{ $visit->patient->age }} years • {{ $visit->patient->gender }}</p>
                                        <div class="row g-3">
                                            <div class="col-sm-6"><strong>Phone:</strong> {{ $visit->patient->phone }}</div>
                                            <div class="col-sm-6"><strong>Valid Till:</strong> {{ $visit->patient->expiry_date->format('d M Y') }}</div>
                                            <div class="col-12"><strong>Address:</strong> {{ $visit->patient->address ?: '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- VITALS – Compact Icons + Modal -->
                            <div class="col-xl-5">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="text-primary mb-4">Vitals</h5>
                                        <div class="row row-cols-3 g-3">
                                            <div><div class="vital-badge bg-danger" data-bs-toggle="modal" data-bs-target="#vitalsModal">BP<br><small>{{ $visit->vitals?->bp ?: '—' }}</small></div></div>
                                            <div><div class="vital-badge bg-warning" data-bs-toggle="modal" data-bs-target="#vitalsModal">Pulse<br><small>{{ $visit->vitals?->pulse ?: '—' }}</small></div></div>
                                            <div><div class="vital-badge bg-info" data-bs-toggle="modal" data-bs-target="#vitalsModal">Temp<br><small>{{ $visit->vitals?->temperature ?: '—' }}°F</small></div></div>
                                            <div><div class="vital-badge bg-success" data-bs-toggle="modal" data-bs-target="#vitalsModal">Weight<br><small>{{ $visit->vitals?->weight ?: '—' }} kg</small></div></div>
                                            <div><div class="vital-badge bg-purple" data-bs-toggle="modal" data-bs-target="#vitalsModal">Height<br><small>{{ $visit->vitals?->height ?: '—' }} cm</small></div></div>
                                            <div><div class="vital-badge bg-primary" data-bs-toggle="modal" data-bs-target="#vitalsModal">RR<br><small>{{ $visit->vitals?->respiration ?: '—' }}</small></div></div>
                                        </div>
                                        <button class="btn btn-outline-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#vitalsModal">
                                            Edit Vitals
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- VITALS MODAL -->
                        <div class="modal fade" id="vitalsModal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Record Vitals</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('doctor.vitals.store', $visit) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="row g-4">
                                                <div class="col-md-4"><label>BP (mmHg)</label><input name="bp" class="form-control" value="{{ $visit->vitals?->bp }}"></div>
                                                <div class="col-md-4"><label>Pulse (bpm)</label><input name="pulse" type="number" class="form-control" value="{{ $visit->vitals?->pulse }}"></div>
                                                <div class="col-md-4"><label>Temperature (°F)</label><input name="temperature" type="number" step="0.1" class="form-control" value="{{ $visit->vitals?->temperature }}"></div>
                                                <div class="col-md-4"><label>Weight (kg)</label><input name="weight" type="number" step="0.1" class="form-control" value="{{ $visit->vitals?->weight }}"></div>
                                                <div class="col-md-4"><label>Height (cm)</label><input name="height" type="number" step="0.1" class="form-control" value="{{ $visit->vitals?->height }}"></div>
                                                <div class="col-md-4"><label>Respiration (/min)</label><input name="respiration" class="form-control" value="{{ $visit->vitals?->respiration }}"></div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success btn-lg">Save Vitals</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                             {{-- LAB RESULTS – ONLY SHOW IF ANY COMPLETED --}}
@if($visit->labOrders->where('is_completed', true)->count() > 0)
    <div class="card mb-5 border-0 shadow-lg rounded-4">
        <div class="card-header bg-gradient-success text-white py-4 rounded-top-4">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="bi bi-file-medical me-3 fs-4"></i>
                Lab Results • {{ $visit->labOrders->where('is_completed', true)->count() }} Completed
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                @foreach($visit->labOrders->where('is_completed', true) as $order)
                    @if($order->result)
                        <div class="col-md-6 col-lg-4">
                            <div class="border rounded-4 p-4 shadow-sm h-100 
                                       {{ $order->result->is_abnormal ? 'border-danger bg-danger-subtle' : 'border-success bg-light' }}">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="fw-bold text-primary mb-0">{{ $order->test->test_name }}</h6>
                                    @if($order->result->is_abnormal)
                                        <span class="badge bg-danger fs-6 animate__animated animate__pulse animate__infinite">
                                            ABNORMAL
                                        </span>
                                    @else
                                        <span class="badge bg-success">Normal</span>
                                    @endif
                                </div>

                                <div class="mt-3">
                                    @if($order->result->result_value)
                                        <div class="fs-4 fw-bold text-dark">
                                            {{ $order->result->result_value }}
                                            <small class="text-muted fs-6">({{ $order->result->normal_range ?? '—' }})</small>
                                        </div>
                                    @elseif($order->result->result_text)
                                        <div class="alert alert-info py-2 px-3 mb-0">
                                            {{ $order->result->result_text }}
                                        </div>
                                    @endif

                                    @if($order->result->remarks)
                                        <small class="text-muted d-block mt-2">
                                            <strong>Remark:</strong> {{ $order->result->remarks }}
                                        </small>
                                    @endif

                                    <small class="text-muted d-block mt-3">
                                        Reported by: {{ $order->result->technician?->name ?? 'Lab' }}
                                        • {{ $order->result->reported_at?->format('d M Y • h:i A') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- PENDING LAB TESTS – SHOW IF ANY NOT COMPLETED --}}
@if($visit->labOrders->where('is_completed', false)->count() > 0)
    <div class="card mb-5 border-0 shadow-sm border-warning">
        <div class="card-header bg-warning text-dark py-3">
            <h6 class="mb-0">
                Pending Lab Tests ({{ $visit->labOrders->where('is_completed', false)->count() }})
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($visit->labOrders->where('is_completed', false) as $order)
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-warning border-5">
                            <div class="flex-grow-1">
                                <strong>{{ $order->test->test_name }}</strong>
                                @if($order->extra_instruction)
                                    <br><small class="text-info">{{ $order->extra_instruction }}</small>
                                @endif
                            </div>
                            <span class="badge bg-warning text-dark">Pending</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

                        <form action="{{ route('doctor.prescription.store', $visit) }}" method="POST" id="prescriptionForm">
                            @csrf

                            <!-- Clinical Notes -->
                            <div class="card mb-5">
                                <div class="card-body">
                                    <h5 class="text-primary mb-4">Clinical Notes</h5>
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <label class="form-label text-danger fw-bold">Chief Complaint</label>
                                            <textarea name="chief_complaint" rows="4" class="form-control">{{ $visit->vitals?->chief_complaint }}</textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-info fw-bold">Examination</label>
                                            <textarea name="examination" rows="4" class="form-control">{{ $visit->vitals?->examination }}</textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-success fw-bold">Diagnosis</label>
                                            <textarea name="diagnosis" rows="4" class="form-control">{{ $visit->vitals?->diagnosis }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                         <h5 class="text-primary mb-4">Send Patient To</h5>
                            <div class="row g-4">

                                <!-- LAB TESTS – CLEAN CARD STYLE -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="section-title text-info">Lab Tests (Optional)</h6>
                                            <div id="labContainer">
                                                <template id="labTemplate">
                                                    <div class="prescription-item">
                                                        <button type="button" class="remove-prescription">×</button>
                                                        <select name="lab_tests[]" class="form-select form-select-lg">
                                                            <option value="">Choose Lab Test</option>
                                                            @foreach(\App\Models\LabTestMaster::active()->orderBy('test_name')->get() as $test)
                                                                <option value="{{ $test->id }}">{{ $test->test_name }} • ₹{{ $test->price }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </template>
                                            </div>
                                            <button type="button" id="addLabTest" class="btn btn-info rounded-pill px-5 py-3 mt-3 shadow">Add Lab Test</button>
                                            <textarea name="lab_instruction" class="form-control mt-3" rows="2" placeholder="Special instruction for lab..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- MEDICINES – ULTRA BEAUTIFUL & SPACIOUS -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="section-title text-success">Medicines (Optional)</h6>
                                            <div id="medicineContainer">
                                                <template id="medicineTemplate">
    <div class="item-card position-relative mb-4 p-5 rounded-4 border border-primary border-opacity-10">
        <!-- Remove Button -->
        <button type="button" class="remove-item btn btn-danger rounded-circle shadow-sm">
            ×
        </button>

        <!-- Medicine Name with Select2 -->
        <div class="mb-4">
            <label class="form-label fw-bold text-primary fs-5">Medicine Name</label>
            <select name="medicines[][medicine_id]" class="form-select form-select-lg select2-medicine" required style="width:100%;">
                <option value="">Search medicine...</option>
                @foreach(\App\Models\MedicineMaster::active()->get() as $m)
                    <option value="{{ $m->id }}">
                        {{ $m->medicine_name }} @if($m->generic_name) • {{ $m->generic_name }} @endif
                        @if($m->strength) • {{ $m->strength }} @endif
                    </option>
                @endforeach
            </select>
        </div>

        <!-- 1. DOSAGE – Top Row -->
        <div class="mb-4">
            <label class="form-label fw-bold text-info">Dosage</label>
            <input type="text" name="medicines[][dosage]" class="form-control form-control-lg text-center fw-bold fs-4" 
                   placeholder="e.g. 1-0-1 or 1 tab twice daily" required>
        </div>

        <!-- 2. DURATION (DAYS) – Second Row -->
        <div class="mb-4">
            <label class="form-label fw-bold text-warning">Duration (Days)</label>
            <input type="number" name="medicines[][duration_days]" min="1" class="form-control form-control-lg text-center fw-bold fs-4" 
                   placeholder="5" required>
        </div>

        <!-- 3. FREQUENCY / TIMING – Last Row -->
        <div class="mb-4">
            <label class="form-label fw-bold text-purple">Frequency / Timing</label>
            <input type="text" name="medicines[][instruction]" class="form-control form-control-lg" 
                   placeholder="e.g. After food, Before sleep, SOS, With water...">
        </div>

        <!-- Optional Extra Instruction -->
        <div class="mt-4">
            <label class="form-label text-success fw-bold">Additional Instruction (Optional)</label>
            <textarea name="medicines[][extra_instruction]" class="form-control" rows="3" 
                      placeholder="Take with plenty of water, Avoid dairy, etc..."></textarea>
        </div>
    </div>
</template>
                                            </div>

                                            <button type="button" id="addMedicineRow" class="btn btn-success rounded-pill px-5 py-3 mt-3 shadow">
                                                Add Medicine
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- INJECTION – Fixed with readonly -->
<div class="col-md-6">
    <div class="card">
        <div class="card-body">
            <h6 class="section-title">Injection (Optional)</h6>
            <select name="injection_medicine_id" id="injection_medicine_id" class="form-select form-select-lg mb-3">
                <option value="">No Injection</option>
                @foreach(\App\Models\MedicineMaster::injectable()->active()->get() as $inj)
                    <option value="{{ $inj->id }}">{{ $inj->medicine_name }} ({{ $inj->packing }})</option>
                @endforeach
            </select>
            <input type="text" 
                   name="injection_route" 
                   id="injection_route" 
                   class="form-control form-control-lg" 
                   placeholder="Route: IV / IM / SC / IV Push" 
                   readonly 
                   value="{{ old('injection_route') }}">
        </div>
    </div>
</div>

<!-- ADMISSION – Fixed with readonly -->
<div class="col-md-6">
    <div class="card">
        <div class="card-body">
            <h6 class="section-title">Bed Admission (Optional)</h6>
            <select name="ward_id" id="ward_id" class="form-select form-select-lg mb-3">
                <option value="">No Admission</option>
                @foreach(\App\Models\Ward::active()->get() as $ward)
                    <option value="{{ $ward->id }}">{{ $ward->ward_name }} • ₹{{ $ward->price_per_day }}/day • {{ $ward->available_beds }} free</option>
                @endforeach
            </select>
            <textarea name="admission_reason" 
                      id="admission_reason" 
                      class="form-control" 
                      rows="3" 
                      placeholder="Reason for admission..." 
                      readonly>{{ old('admission_reason') }}</textarea>
        </div>
    </div>
</div>
                            </div>

                            <div class="text-center mt-5">
                                <button type="submit" name="follow_up_only" value="1" class="btn btn-warning btn-lg px-5 py-3 rounded-pill me-4 shadow">
                                    Follow-up Only
                                </button>
                                <button type="submit" class="btn btn-success btn-lg px-5 py-3 rounded-pill shadow-lg">
                                    Complete & Send Patient
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            @else
                <div class="text-center py-5 my-5">
                    <h1 class="display-5 text-primary fw-bold">Welcome, Dr. {{ auth()->user()->name }}!</h1>
                    <p class="lead text-muted mt-3">Ready to heal with love and care</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('prescriptionForm');
    if (!form) return;

    const injMedSelect = document.getElementById('injection_medicine_id');
    const injRouteInput = document.getElementById('injection_route');
    const wardSelect = document.getElementById('ward_id');
    const admissionReason = document.getElementById('admission_reason');

    function toggleInjectionField() {
        if (injMedSelect.value) {
            injRouteInput.removeAttribute('readonly');
            injRouteInput.focus();
        } else {
            injRouteInput.setAttribute('readonly', 'readonly');
            injRouteInput.value = '';
        }
    }

    function toggleAdmissionField() {
        if (wardSelect.value) {
            admissionReason.removeAttribute('readonly');
            admissionReason.focus();
        } else {
            admissionReason.setAttribute('readonly', 'readonly');
            admissionReason.value = '';
        }
    }

    toggleInjectionField();
    toggleAdmissionField();
    injMedSelect?.addEventListener('change', toggleInjectionField);
    wardSelect?.addEventListener('change', toggleAdmissionField);

    // ADD MEDICINE ROW + Initialize Select2
    document.getElementById('addMedicineRow')?.addEventListener('click', function () {
        const template = document.getElementById('medicineTemplate').content.cloneNode(true);
        const row = template.querySelector('.item-card');
        
        document.getElementById('medicineContainer').appendChild(row);

        // Initialize Select2 on the new medicine dropdown
        $(row).find('.select2-medicine').select2({
            placeholder: "Search medicine...",
            allowClear: true,
            width: '100%'
        });
    });

    // ADD LAB TEST ROW
    document.getElementById('addLabTest')?.addEventListener('click', function () {
        const template = document.getElementById('labTemplate').content.cloneNode(true);
        document.getElementById('labContainer').appendChild(template);
    });

    // REMOVE MEDICINE ROW — FIXED: Now targets .remove-item
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            const card = e.target.closest('.item-card');
            if (card) {
                card.remove();
            }
        }
    });

    // REMOVE LAB TEST ROW (old class still used in lab)
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-prescription')) {
            e.target.closest('.prescription-item')?.remove();
        }
    });

    // FORM SUBMIT — Clean empty rows
    form.addEventListener('submit', function () {
        // Clean empty medicine rows
        document.querySelectorAll('#medicineContainer .item-card').forEach(card => {
            const select = card.querySelector('select[name$="[medicine_id]"]');
            if (!select || !select.value) {
                card.remove();
            }
        });

        // Clean empty lab rows
        document.querySelectorAll('#labContainer .prescription-item').forEach(item => {
            if (!item.querySelector('select')?.value) {
                item.remove();
            }
        });

        // Reset optional fields
        if (!injMedSelect.value) injRouteInput.value = '';
        if (!wardSelect.value) admissionReason.value = '';
    });

    // Auto-add one medicine row on load (optional)
    document.getElementById('addMedicineRow')?.click();
});
</script>
@endsection
