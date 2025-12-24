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

                    <div class="col-12">
                        <label class="form-label fw-medium">Age <span class="text-danger">*</span></label>
                        <small class="text-muted d-block mb-2">Enter either Years OR Months & Days</small>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="number" id="edit-age" name="age" min="0" max="120" 
                                       class="form-control rounded-3" placeholder="Years (e.g. 35)">
                                <small class="text-muted">For adults</small>
                            </div>
                            <div class="col-md-4">
                                <input type="number" id="edit-age_months" name="age_months" min="0" max="11" 
                                       class="form-control rounded-3" placeholder="Months">
                                <small class="text-muted">For infants</small>
                            </div>
                            <div class="col-md-4">
                                <input type="number" id="edit-age_days" name="age_days" min="0" max="31" 
                                       class="form-control rounded-3" placeholder="Days">
                                <small class="text-muted">For newborns</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Gender <span class="text-danger">*</span></label>
                        <select id="edit-gender" name="gender" class="form-select rounded-3" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" id="edit-phone" name="phone" class="form-control rounded-3" required>
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
document.getElementById('editPatientForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Clear previous errors
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

    const patientId = document.getElementById('editPatientId').value;
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    // Client-side age validation
    const hasYears = data.age.trim() !== '';
    const hasMonths = data.age_months.trim() !== '';
    const hasDays = data.age_days.trim() !== '';

    if (!hasYears && !hasMonths && !hasDays) {
        alert('Please enter age in years OR months/days.');
        return;
    }

    if (hasYears && (hasMonths || hasDays)) {
        alert('Enter either years OR months/days — not both.');
        return;
    }

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
        if (!response.ok) throw response;
        return response.json();
    })
    .then(result => {
        alert(result.message || 'Patient updated successfully!');
        location.reload();
    })
    .catch(async (err) => {
        const errorData = await err.json();
        if (errorData.errors) {
            Object.keys(errorData.errors).forEach(field => {
                const input = document.querySelector(`#edit-${field}`);
                if (input) {
                    input.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = errorData.errors[field][0];
                    input.parentNode.appendChild(feedback);
                }
            });
            alert('Please fix the errors.');
        } else {
            alert('Update failed. Check console.');
        }
    });
});
</script>