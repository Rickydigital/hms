<div class="modal fade" id="editPatientModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="editPatientForm" class="modal-content border-0 rounded-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="editPatientId" name="patient_id">

            <div class="modal-header bg-warning text-dark rounded-top-4">
                <h5 class="modal-title fw-bold">Edit Patient Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="edit-name" name="name" class="form-control rounded-3" required>
                    </div>

                    <!-- Age Section - Now Fully Optional & No Restrictions -->
                    <div class="col-12">
                        <label class="form-label fw-medium">Age</label>
                        <small class="text-muted d-block mb-2">
                            Optional. You may enter years, months, days, or any combination.
                        </small>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="number" id="edit-age" name="age" min="0" max="120" 
                                       class="form-control rounded-3" placeholder="Years (e.g. 35)">
                                <small class="text-muted">Years</small>
                            </div>
                            <div class="col-md-4">
                                <input type="number" id="edit-age_months" name="age_months" min="0" max="11" 
                                       class="form-control rounded-3" placeholder="Months (0-11)">
                                <small class="text-muted">Months</small>
                            </div>
                            <div class="col-md-4">
                                <input type="number" id="edit-age_days" name="age_days" min="0" max="31" 
                                       class="form-control rounded-3" placeholder="Days (0-31)">
                                <small class="text-muted">Days</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Gender <span class="text-danger">*</span></label>
                        <select id="edit-gender" name="gender" class="form-select rounded-3" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Phone Number</label>
                        <input type="text" id="edit-phone" name="phone" class="form-control rounded-3">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">Address (Optional)</label>
                        <textarea id="edit-address" name="address" rows="2" class="form-control rounded-3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4">Update Patient</button>
            </div>
        </form>
    </div>
</div>

<script>
// Edit Patient Form Submission - No Age Validation
document.getElementById('editPatientForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Clear previous errors
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

    const patientId = document.getElementById('editPatientId').value;
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    // === NO AGE VALIDATION AT ALL ===

    fetch(`/patients/${patientId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(result => {
        alert(result.message || 'Patient updated successfully!');
        location.reload();
    })
    .catch(error => {
        if (error.errors) {
            // Display validation errors
            Object.keys(error.errors).forEach(field => {
                const input = document.querySelector(`#edit-${field}`);
                if (input) {
                    input.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = error.errors[field][0];
                    input.parentNode.appendChild(feedback);
                }
            });

            const firstError = document.querySelector('.is-invalid');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            console.error('Update error:', error);
            alert(error.message || 'Failed to update patient. Please try again.');
        }
    });
});
</script>