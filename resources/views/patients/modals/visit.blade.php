<div class="modal fade" id="createVisitModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form id="createVisitForm" class="modal-content border-0 rounded-4">
            @csrf
            <input type="hidden" name="patient_id" id="visit-patient-id">
            <div class="modal-header bg-success text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-file-medical me-2"></i> 
                    Start OPD Visit - <span id="visit-patient-name"></span>
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            Patient ID: <strong id="visit-patient-code"></strong>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-bold">Chief Complaint <span class="text-danger">*</span></label>
                        <textarea name="chief_complaint" rows="3" class="form-control rounded-3" 
                                  placeholder="Fever, cough, headache, etc." required></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">History</label>
                        <textarea name="history" rows="3" class="form-control rounded-3" 
                                  placeholder="Past illness, allergies, etc."></textarea>
                    </div>

                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-heart-pulse"></i> Vitals</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label>Height (cm)</label>
                                <input type="number" name="height" step="0.1" class="form-control rounded-3">
                            </div>
                            <div class="col-md-3">
                                <label>Weight (kg)</label>
                                <input type="number" name="weight" step="0.1" class="form-control rounded-3">
                            </div>
                            <div class="col-md-3">
                                <label>Temperature (°C)</label>
                                <input type="number" name="temperature" step="0.1" class="form-control rounded-3">
                            </div>
                            <div class="col-md-3">
                                <label>Pulse (/min)</label>
                                <input type="number" name="pulse" class="form-control rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label>BP (mmHg)</label>
                                <input type="text" name="bp" placeholder="120/80" class="form-control rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label>Respiration (/min)</label>
                                <input type="number" name="respiration" class="form-control rounded-3">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success rounded-pill px-5">
                    <i class="bi bi-check2-all"></i> Send to Doctor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openVisitModal(id, name, code) {
    document.getElementById('visit-patient-id').value = id;
    document.getElementById('visit-patient-name').textContent = name;
    document.getElementById('visit-patient-code').textContent = code;
}

// Safe DOM ready
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('createVisitForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const patientId = document.getElementById('visit-patient-id').value;

        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sending...';

        fetch(`/patients/${patientId}/visit`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Patient sent to OPD queue!');
                location.reload();
            } else {
                alert(data.message || 'Error occurred');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Connection error. Please try again.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});
</script>