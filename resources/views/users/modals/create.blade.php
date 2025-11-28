<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="createUserForm" method="POST" class="modal-content border-0 rounded-4">
            @csrf
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    Add New Staff Member
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-3" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control rounded-3" required>
                    </div>

                    <!-- DEPARTMENT DROPDOWN -->
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Department <span class="text-danger">*</span></label>
                        <select name="department" class="form-select rounded-3" required>
                            <option value="">-- Select Department --</option>
                            <option value="Admin">Admin</option>
                            <option value="Reception">Reception</option>
                            <option value="Doctor">Doctor</option>
                            <option value="Lab">Lab</option>
                            <option value="Pharmacy">Pharmacy</option>
                            <option value="Cashier">Cashier</option>
                            <option value="Store">Store</option>
                        </select>
                    </div>

                    <!-- ROLE DROPDOWN -->
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select rounded-3" required>
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="alert alert-info mt-4 mb-0" role="alert">
                    A strong random password will be generated and sent directly to the user's email.
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Creating...';

    const formData = new FormData(this);

    fetch('/users', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Staff created successfully! Login credentials have been sent to their email.');
            location.reload();
        } else {
            alert(data.message || 'Error creating user. Please check the details and try again.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Something went wrong. Please try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Create User';
    });
});
</script>