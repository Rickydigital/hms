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

    .opd-page .bg-gradient-success {
        background: linear-gradient(135deg, var(--success), #27ae60) !important;
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
        cursor: pointer;
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

                <!-- Live Search Patient (Like Pharmacy) -->
                <div class="card mb-4">
                    <div class="card-header bg-gradient-primary text-white rounded-top">
                        <h5 class="mb-0">Search Patient in Queue</h5>
                    </div>
                    <div class="card-body p-4">
                        <input type="text" id="queueSearchInput" class="form-control form-control-lg" 
                               placeholder="Search by Name or ID..." autocomplete="off">
                        <small class="text-muted d-block mt-2">Filters today's queue instantly</small>
                    </div>
                </div>

                <!-- Todays Queue -->
                <div class="card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center rounded-top">
                        <h5 class="mb-0">Today's Queue <span class="badge bg-light text-success ms-2">{{ $todayVisits->count() }}</span></h5>
                        <small>{{ now()->format('d M Y') }}</small>
                    </div>
                    <div class="list-group list-group-flush" style="max-height: 70vh; overflow-y: auto;" id="queueList">
                        @forelse($todayVisits as $v)
                            <a href="{{ route('doctor.opd.show', $v) }}" 
                               class="list-group-item list-group-item-action px-4 py-3 queue-item {{ $visit && $visit->id == $v->id ? 'bg-primary text-white' : '' }}"
                               data-patient-name="{{ strtolower($v->patient->name) }}"
                               data-patient-id="{{ strtolower($v->patient->patient_id) }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $v->patient->patient_id }}</strong> • {{ $v->visit_time->format('h:i A') }}<br>
                                        <b>{{ $v->patient->name }}</b> • 
                                        @if($v->patient->age_months || $v->patient->age_days)
                                            {{ $v->patient->age ?? '—' }} yrs 
                                            {{ $v->patient->age_months ? $v->patient->age_months . 'm' : '' }}
                                            {{ $v->patient->age_days ? $v->patient->age_days . 'd' : '' }} 
                                        @else
                                            {{ $v->patient->age ?? '—' }} yrs
                                        @endif
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

                        <!-- PATIENT + VITALS -->
                        <div class="row g-4 mb-5">
                            <div class="col-xl-7">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h5 class="text-primary mb-0">Patient Details</h5>

                                            {{-- View Past History button --}}
                                            <a href="{{ route('patients.history.index') }}?open={{ $visit->patient->id }}"
                                            class="btn btn-outline-light bg-primary border-0 shadow-sm rounded-pill px-4">
                                                <i class="bi bi-clock-history me-2"></i> View Past History
                                            </a>
                                        </div>
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

                            <div class="col-xl-5">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="text-primary mb-4">Vitals</h5>
                                        <div class="row row-cols-3 g-3">
                                            <div><div class="vital-badge bg-danger">BP<br><small>{{ $visit->vitals?->bp ?: '—' }}</small></div></div>
                                            <div><div class="vital-badge bg-warning">Pulse<br><small>{{ $visit->vitals?->pulse ?: '—' }}</small></div></div>
                                            <div><div class="vital-badge bg-info">Temp<br><small>{{ $visit->vitals?->temperature ?: '—' }}°F</small></div></div>
                                            <div><div class="vital-badge bg-success">Weight<br><small>{{ $visit->vitals?->weight ?: '—' }} kg</small></div></div>
                                            <div><div class="vital-badge bg-purple">Height<br><small>{{ $visit->vitals?->height ?: '—' }} cm</small></div></div>
                                            <div><div class="vital-badge bg-primary">RR<br><small>{{ $visit->vitals?->respiration ?: '—' }}</small></div></div>
                                        </div>
                                        <p class="text-muted small mt-3">Vitals recorded at registration</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- COMPLETED LAB RESULTS --}}
                        @if($visit->labOrders->where('is_completed', true)->count() > 0)
                            <div class="card mb-5 border-0 shadow-lg rounded-4">
                                <div class="card-header bg-gradient-success text-white py-4 rounded-top-4">
                                    <h5 class="mb-0">Lab Results • {{ $visit->labOrders->where('is_completed', true)->count() }} Completed</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        @foreach($visit->labOrders->where('is_completed', true) as $order)
                                            @if($order->result)
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="border rounded-4 p-4 shadow-sm h-100 {{ $order->result->is_abnormal ? 'border-danger bg-danger-subtle' : 'border-success bg-light' }}">
                                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                                            <h6 class="fw-bold text-primary mb-0">{{ $order->test->test_name }}</h6>
                                                            @if($order->result->is_abnormal)
                                                                <span class="badge bg-danger fs-6">ABNORMAL</span>
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
                                                                <div class="alert alert-info py-2 px-3 mb-0">{{ $order->result->result_text }}</div>
                                                            @endif
                                                            @if($order->result->remarks)
                                                                <small class="text-muted d-block mt-2"><strong>Remark:</strong> {{ $order->result->remarks }}</small>
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

                        {{-- PENDING LAB TESTS WITH PAYMENT STATUS --}}
                        @if($visit->labOrders->where('is_completed', false)->count() > 0)
                            <div class="card mb-5 border-0 shadow-sm {{ $visit->labOrders->where('is_completed', false)->where('is_paid', false)->count() > 0 ? 'border-danger' : 'border-warning' }}">
                                <div class="card-header {{ $visit->labOrders->where('is_completed', false)->where('is_paid', false)->count() > 0 ? 'bg-danger' : 'bg-warning' }} text-white py-3">
                                    <h6 class="mb-0 d-flex justify-content-between align-items-center">
                                        Pending Lab Tests ({{ $visit->labOrders->where('is_completed', false)->count() }})
                                        @if($visit->labOrders->where('is_completed', false)->where('is_paid', false)->count() > 0)
                                            <span class="badge bg-white text-danger fs-6">Payment Required</span>
                                        @else
                                            <span class="badge bg-white text-dark fs-6">Paid • Awaiting Result</span>
                                        @endif
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        @foreach($visit->labOrders->where('is_completed', false) as $order)
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-3 rounded-3 {{ $order->is_paid ? 'bg-light' : 'bg-danger bg-opacity-10' }} border-start {{ $order->is_paid ? 'border-success' : 'border-danger' }} border-5">
                                                    <div class="flex-grow-1">
                                                        <strong>{{ $order->test->test_name }}</strong>
                                                        @if($order->extra_instruction)
                                                            <br><small class="text-info">{{ $order->extra_instruction }}</small>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        @if($order->is_paid)
                                                            <span class="badge bg-success">PAID</span>
                                                        @else
                                                            <span class="badge bg-danger">NOT PAID</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($visit->labOrders->where('is_completed', false)->where('is_paid', false)->count() > 0)
                                        <div class="alert alert-danger mt-4 mb-0">
                                            <strong>Important:</strong> Patient must pay at the billing counter before lab can process these tests.
                                        </div>
                                    @endif
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

                                <!-- LAB TESTS -->
<div class="col-md-6">
    <div class="card">
        <div class="card-body">
            <h6 class="section-title text-info">Lab Tests (Optional)</h6>
            <div id="labContainer">
                <template id="labTemplate">
                    <div class="prescription-item position-relative mb-3 p-3 bg-light rounded-3 border">
                        <button type="button" class="remove-prescription position-absolute top-0 end-0 btn btn-sm btn-danger rounded-circle m-2">×</button>
                        
                        <select name="lab_tests[]" class="form-select form-select-lg select2-lab" multiple="multiple" style="width: 100%;">
                            <option value="">Search lab tests...</option>
                            @foreach(\App\Models\LabTestMaster::active()->orderBy('test_name')->get() as $test)
                                <option value="{{ $test->id }}" data-price="{{ $test->price }}">
                                    {{ $test->test_name }} — {{ number_format($test->price) }} Tsh
                                </option>
                            @endforeach
                        </select>
                    </div>
                </template>
            </div>
            
            <button type="button" id="addLabTest" class="btn btn-info rounded-pill px-5 py-3 mt-3 shadow">
                Add Lab Test Group
            </button>
            
            <textarea name="lab_instruction" class="form-control mt-3" rows="2" placeholder="Special instruction for lab..."></textarea>
        </div>
    </div>
</div>

                                <!-- MEDICINES -->
<div class="col-md-6">
    <div class="card">
        <div class="card-body">
            <h6 class="section-title text-success">Medicines (Optional)</h6>
            <div id="medicineContainer">
                <template id="medicineTemplate">
                    <div class="item-card position-relative mb-4 p-5 rounded-4 border border-primary border-opacity-10">
                        <button type="button" class="remove-item btn btn-danger rounded-circle shadow-sm">×</button>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary fs-5">Medicine Name</label>
                            <select name="medicines[medicine_id][]" class="form-select form-select-lg select2-medicine" style="width:100%;">
                                <option value="">Search medicine...</option>
                                @foreach(
                                    \App\Models\MedicineMaster::active()
                                        ->withSum('batches as current_stock', 'current_stock')
                                        ->having('current_stock', '>', 0)
                                        ->orderBy('medicine_name')
                                        ->get() as $m
                                )
                                    <option value="{{ $m->id }}"
                                            data-stock="{{ $m->current_stock }}">
                                        {{ $m->medicine_name }}
                                        @if($m->generic_name) • {{ $m->generic_name }} @endif
                                        @if($m->strength) • {{ $m->strength }} @endif
                                        <span class="text-muted small"> — Stock: {{ $m->current_stock }}</span>
                                        @if($m->current_stock <= 10)
                                            <span class="text-danger fw-bold"> (Low Stock!)</span>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-info">Dosage</label>
                            <input type="text" name="medicines[dosage][]" class="form-control form-control-lg text-center fw-bold fs-4" 
                                   placeholder="e.g. 1-0-1 or 1 tab twice daily">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-warning">Duration (Days)</label>
                            <input type="number" name="medicines[duration_days][]" min="1" class="form-control form-control-lg text-center fw-bold fs-4" 
                                   placeholder="5">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-purple">Frequency / Timing</label>
                            <input type="text" name="medicines[instruction][]" class="form-control form-control-lg" 
                                   placeholder="e.g. After food, Before sleep, SOS, With water...">
                        </div>

                        <div class="mt-4">
                            <label class="form-label text-success fw-bold">Additional Instruction (Optional)</label>
                            <textarea name="medicines[extra_instruction][]" class="form-control" rows="3" 
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

                                <!-- INJECTION -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="section-title">Injection (Optional)</h6>
                                            <select name="injection_medicine_id" id="injection_medicine_id" class="form-select form-select-lg mb-3">
                                                <option value="">No Injection</option>
                                                @foreach(\App\Models\MedicineMaster::injectable()->active()->get() as $inj)
                                                    <option value="{{ $inj->id }}">{{ $inj->generic_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="injection_route" id="injection_route" class="form-control form-control-lg" 
                                                   placeholder="Route: IV / IM / SC / IV Push" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- ADMISSION -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="section-title">Bed Admission (Optional)</h6>
                                            <select name="ward_id" id="ward_id" class="form-select form-select-lg mb-3">
                                                <option value="">No Admission</option>
                                                @foreach(\App\Models\Ward::active()->get() as $ward)
                                                    <option value="{{ $ward->id }}">{{ $ward->ward_name }} • {{ $ward->available_beds }} free</option>
                                                @endforeach
                                            </select>
                                            <textarea name="admission_reason" id="admission_reason" class="form-control" rows="3" 
                                                      placeholder="Reason for admission..." readonly></textarea>
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
                    <i class="bi bi-heart-pulse display-1 text-primary mt-4"></i>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const queueSearchInput = document.getElementById('queueSearchInput');
    const queueItems = document.querySelectorAll('.queue-item');

    queueSearchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();

        queueItems.forEach(item => {
            const name = item.dataset.patientName;
            const id = item.dataset.patientId;

            const matches = name.includes(query) || id.includes(query);
            item.style.display = matches ? '' : 'none';
        });
    });

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

    // Add Medicine Row
    document.getElementById('addMedicineRow')?.addEventListener('click', function () {
        const template = document.getElementById('medicineTemplate').content.cloneNode(true);
        const row = template.querySelector('.item-card');
        document.getElementById('medicineContainer').appendChild(row);

        // Initialize Select2 on the newly added select
        $(row).find('.select2-medicine').select2({
            placeholder: "Search medicine...",
            allowClear: true,
            width: '100%'
        });
    });

    // Add Lab Test
    // Add Lab Test Row + Initialize Select2
document.getElementById('addLabTest')?.addEventListener('click', function () {
    const template = document.getElementById('labTemplate').content.cloneNode(true);
    const item = template.querySelector('.prescription-item');
    document.getElementById('labContainer').appendChild(item);

    // Initialize Select2 on the new lab select
    $(item).find('.select2-lab').select2({
        placeholder: "Search and select lab tests...",
        allowClear: true,
        width: '100%',
        templateResult: formatLabOption,
        templateSelection: formatLabSelection
    });
});

// Optional: Custom formatting to show price nicely
function formatLabOption(option) {
    if (!option.id) return option.text;
    
    var $option = $(
        '<span>' + option.text + '</span>'
    );
    return $option;
}

function formatLabSelection(option) {
    return option.text || 'No tests selected';
}



    // Remove items (delegated event)
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            e.target.closest('.item-card')?.remove();
        }
        if (e.target.classList.contains('remove-prescription') || e.target.closest('.remove-prescription')) {
            e.target.closest('.prescription-item')?.remove();
        }
    });

    // Form validation on submit
    form.addEventListener('submit', function (e) {
        let hasError = false;

        // Validate and clean medicine rows
        document.querySelectorAll('#medicineContainer .item-card').forEach(card => {
        // Updated selectors to match new name format
        const medicineSelect = card.querySelector('select[name="medicines[medicine_id][]"]');
        const dosageInput = card.querySelector('input[name="medicines[dosage][]"]');
        const durationInput = card.querySelector('input[name="medicines[duration_days][]"]');

        if (!medicineSelect || !dosageInput || !durationInput) {
            card.remove(); // safety
            return;
        }

        const medicineId = medicineSelect.value.trim();
        const dosage = dosageInput.value.trim();
        const duration = durationInput.value.trim();

        // Remove completely empty rows
        if (!medicineId && !dosage && !duration) {
            card.remove();
            return;
        }

        // If any field filled → require all three
        if (medicineId || dosage || duration) {
            if (!medicineId || !dosage || !duration) {
                alert('Please fill Medicine Name, Dosage, and Duration for all added medicines, or remove the row.');
                hasError = true;
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                card.style.border = '4px solid red';
                setTimeout(() => card.style.border = '', 6000);
            }
        }
    });

        // Clean up empty lab tests
        document.querySelectorAll('#labContainer .prescription-item').forEach(item => {
            const select = item.querySelector('select[name="lab_tests[]"]');
            if (select && !select.value) {
                item.remove();
            }
        });

        // Clear injection/admission if not selected
        if (!injMedSelect.value) {
            injRouteInput.value = '';
        }
        if (!wardSelect.value) {
            admissionReason.value = '';
        }

        if (hasError) {
            e.preventDefault();
        }
    });

    // Optional: Auto-add one blank medicine row when page loads
    setTimeout(() => {
        const addBtn = document.getElementById('addMedicineRow');
        if (addBtn) addBtn.click();
    }, 100);
});
</script>

@endsection