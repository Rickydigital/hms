<div class="modal fade" id="registerPatientModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="registerPatientForm" class="modal-content border-0 rounded-4">
            @csrf
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i> Register New Patient
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Age <span class="text-danger">*</span></label>
                        <input type="number" name="age" min="0" max="120" class="form-control rounded-3" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select rounded-3" required>
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
                        <label class="form-label fw-medium">Address</label>
                        <textarea name="address" rows="2" class="form-control rounded-3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-check2"></i> Register Patient
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('registerPatientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('/patients', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Please check all fields'));
        }
    });
});
</script>