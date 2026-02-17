@forelse($patients as $patient)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3 patient-card"
                 data-patient-name="{{ strtolower($patient->name) }}"
                 data-patient-id="{{ strtolower($patient->patient_id) }}">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative hover-lift">
                    <div class="position-absolute top-0 start-0 p-2 z-3">
                        @if($patient->isExpired())
                            <span class="badge bg-danger rounded-pill px-3 py-2 small fw-medium">Expired</span>
                        @elseif($patient->is_active)
                            <span class="badge bg-success rounded-pill px-3 py-2 small fw-medium">Active</span>
                        @else
                            <span class="badge bg-warning rounded-pill px-3 py-2 small fw-medium">Inactive</span>
                        @endif
                    </div>

                    <div class="card-body p-4 d-flex flex-column text-center">
                        <div class="avatar-lg mx-auto mb-3 bg-soft-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi {{ $patient->gender == 'Male' ? 'bi-person' : 'bi-person-fill' }} text-primary" style="font-size: 2.8rem;"></i>
                        </div>

                        <h5 class="card-title mb-1 fw-bold">{{ $patient->name }}</h5>
                        <p class="text-muted small mb-2">
                            Age: 
                            @if($patient->age_months || $patient->age_days)
                                {{ $patient->age_months ? $patient->age_months . ' months' : '' }}
                                {{ $patient->age_days ? $patient->age_days . ' days' : '' }}
                            @else
                                {{ $patient->age ?? '—' }} yrs
                            @endif
                        </p>
                        <p class="text-primary small fw-bold mb-3">{{ $patient->patient_id }}</p>

                        <!-- Buttons Section -->
                        <div class="d-flex gap-2 justify-content-center mt-auto flex-wrap">
                            <button type="button" class="btn btn-outline-info btn-sm rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#viewPatientModal"
                                    onclick='showPatient({!! json_encode($patient->append(['age_display'])) !!})'>
                                View
                            </button>

                            <!-- Edit Button -->
                            <button type="button" class="btn btn-outline-warning btn-sm rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#editPatientModal"
                                    onclick='openEditModal({!! json_encode($patient) !!})'>
                                <i class="bi bi-pencil"></i> Edit
                            </button>

                            @if($patient->is_active && !$patient->isExpired())
                                @if(!$patient->visits()->whereDate('visit_date', today())->exists())
                                    <button type="button" class="btn btn-success btn-sm rounded-pill"
                                            data-bs-toggle="modal" data-bs-target="#createVisitModal"
                                            onclick="openVisitModal({{ $patient->id }}, '{{ $patient->name }}', '{{ $patient->patient_id }}')">
                                        Start Visit
                                    </button>
                                @else
                                    <span class="btn btn-success btn-sm rounded-pill disabled">In OPD</span>
                                @endif
                            @else
                                <button type="button" class="btn btn-outline-success btn-sm rounded-pill"
                                        onclick="reactivatePatient({{ $patient->id }})">
                                    Reactivate
                                </button>
                            @endif
                        </div>

                        <small class="text-muted mt-2">
                            Expires: {{ $patient->expiry_date->format('d M Y') }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <h5 class="text-muted">No patients found</h5>
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#registerPatientModal">
                    Register First Patient
                </button>
            </div>
        @endforelse