<div class="modal fade" id="registerPatientModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="registerPatientForm" class="modal-content border-0 rounded-4">
            @csrf
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold">Register New Patient</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" required autofocus>
                    </div>

                    <!-- Age Section -->
                    <div class="col-12">
                        <label class="form-label fw-medium">Age <span class="text-danger">*</span></label>
                        <small class="text-muted d-block mb-2">
                            Enter either Years OR Months & Days (for infants). At least one field is required.
                        </small>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="number" name="age" min="0" max="120" 
                                       class="form-control rounded-3" 
                                       placeholder="Years (e.g. 35)" 
                                       id="ageYears">
                                <small class="text-muted">For adults & children</small>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="age_months" min="0" max="11" 
                                       class="form-control rounded-3" 
                                       placeholder="Months (0-11)" 
                                       id="ageMonths">
                                <small class="text-muted">For infants</small>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="age_days" min="0" max="31" 
                                       class="form-control rounded-3" 
                                       placeholder="Days (0-31)" 
                                       id="ageDays">
                                <small class="text-muted">For newborns</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select rounded-3" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control rounded-3" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">Address (Optional)</label>
                        <textarea name="address" rows="2" class="form-control rounded-3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Register Patient</button>
            </div>
        </form>
    </div>
</div>

<script>
// =====================================================
// SMART AGE VALIDATION + PREVENT MIXED INPUT
// =====================================================
document.getElementById('registerPatientForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Clear previous errors
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

    const ageYears = document.getElementById('ageYears');
    const ageMonths = document.getElementById('ageMonths');
    const ageDays = document.getElementById('ageDays');

    const hasYears = ageYears.value.trim() !== '';
    const hasMonths = ageMonths.value.trim() !== '';
    const hasDays = ageDays.value.trim() !== '';

    // At least one age field must be filled
    if (!hasYears && !hasMonths && !hasDays) {
        ageYears.classList.add('is-invalid');
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = 'Please enter age in years OR months/days.';
        ageYears.parentNode.appendChild(feedback);
        ageYears.focus();
        return;
    }

    // Prevent mixing years with months/days (optional but recommended for clarity)
    if (hasYears && (hasMonths || hasDays)) {
        ageYears.classList.add('is-invalid');
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = 'Enter either years OR months/days — not both.';
        ageYears.parentNode.appendChild(feedback);
        return;
    }

    // Submit the form
    const formData = new FormData(this);
    const plainData = Object.fromEntries(formData);

    fetch('{{ route("patients.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(plainData)
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 422) {
                return response.json().then(err => { throw err; });
            }
            return response.text().then(text => { throw new Error(text); });
        }
        return response.json();
    })
    .then(result => {
        if (result.success) {
            alert(result.message || 'Patient registered successfully!');
            location.reload();
        }
    })
    .catch(error => {
        if (error.errors) {
            Object.keys(error.errors).forEach(field => {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = error.errors[field][0];
                    input.parentNode.appendChild(feedback);
                }
            });

            const firstError = document.querySelector('.is-invalid');
            if (firstError) firstError.focus();
        } else {
            console.error('Unexpected error:', error);
            alert('Something went wrong. Check console.');
        }
    });
});
</script>