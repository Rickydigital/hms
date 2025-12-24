<div class="modal fade" id="viewPatientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header bg-info text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-circle me-2"></i> Patient Details
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="avatar-xl mx-auto mb-4 bg-soft-info rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-fill text-info" style="font-size: 4rem;"></i>
                </div>
                <h4 id="view-name" class="fw-bold"></h4>
                <p class="text-muted mb-3">
                    <span id="view-id" class="fw-bold text-primary"></span>
                </p>

                <div class="row g-4 text-start">
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <i class="bi bi-calendar-heart text-success fs-4"></i>
                            <div>
                                <small class="text-muted">Age / Gender</small>
                                <p id="view-age-gender" class="mb-0 fw-medium"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <i class="bi bi-telephone text-primary fs-4"></i>
                            <div>
                                <small class="text-muted">Phone</small>
                                <p id="view-phone" class="mb-0 fw-medium"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <i class="bi bi-geo-alt text-warning fs-4"></i>
                            <div>
                                <small class="text-muted">Address</small>
                                <p id="view-address" class="mb-0 fw-medium"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <i class="bi bi-calendar-check text-success fs-4"></i>
                            <div>
                                <small class="text-muted">Registered On</small>
                                <p id="view-reg-date" class="mb-0 fw-medium"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <i class="bi bi-credit-card-2-front fs-4"></i>
                            <div>
                                <small class="text-muted">Card Validity</small>
                                <p id="view-expiry" class="mb-0 fw-medium"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success rounded-pill px-4" data-id="">
                    <i class="bi bi-printer me-2"></i> Print Card
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showPatient(patient) {
    document.getElementById('view-name').textContent = patient.name;
    document.getElementById('view-id').textContent = patient.patient_id;

    // === Smart & Complete Age Display ===
    const parts = [];

    if (patient.age) {
        parts.push(patient.age + ' yr' + (patient.age > 1 ? 's' : ''));
    }
    if (patient.age_months) {
        parts.push(patient.age_months + ' month' + (patient.age_months > 1 ? 's' : ''));
    }
    if (patient.age_days) {
        parts.push(patient.age_days + ' day' + (patient.age_days > 1 ? 's' : ''));
    }

    const ageText = parts.length > 0 ? parts.join(' ') : '—';

    document.getElementById('view-age-gender').textContent = ageText + ' • ' + patient.gender;

    document.getElementById('view-phone').textContent = patient.phone || '—';
    document.getElementById('view-address').textContent = patient.address || '—';
    document.getElementById('view-reg-date').textContent = new Date(patient.registration_date).toLocaleDateString('en-GB');
    document.getElementById('view-expiry').textContent = new Date(patient.expiry_date).toLocaleDateString('en-GB');

    // Set patient ID for print button
    document.querySelector('#viewPatientModal .btn-success').dataset.id = patient.id;
}
</script>