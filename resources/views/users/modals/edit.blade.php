<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="editUserForm" method="POST" class="modal-content border-0 rounded-4">
            @csrf @method('PUT')
            <div class="modal-header bg-warning text-dark rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i> Edit Staff Member
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id" id="edit-id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Full Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control rounded-3" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Email</label>
                        <input type="email" name="email" id="edit-email" class="form-control rounded-3" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Phone</label>
                        <input type="text" name="phone" id="edit-phone" class="form-control rounded-3" required>
                    </div>

                    <!-- DEPARTMENT DROPDOWN -->
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Department</label>
                        <select name="department" id="edit-department" class="form-select rounded-3" required>
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
                    <div class="col-12">
                        <label class="form-label fw-medium">Role</label>
                        <select name="role" id="edit-role" class="form-select rounded-3" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4">Update User</button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(user) {
    const form = document.getElementById('editUserForm');
    form.action = `/users/${user.id}`;
    document.getElementById('edit-id').value = user.id;
    document.getElementById('edit-name').value = user.name;
    document.getElementById('edit-email').value = user.email;
    document.getElementById('edit-phone').value = user.phone;
    document.getElementById('edit-department').value = user.department;
    document.getElementById('edit-role').value = user.roles[0]?.name || '';
}
</script>

<script>
document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const userId = document.getElementById('edit-id').value;

    fetch(`/users/${userId}`, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error updating user');
        }
    });
});
</script>