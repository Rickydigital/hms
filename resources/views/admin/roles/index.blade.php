@extends('components.main-layout')
@section('title', 'Manage Roles & Permissions')
@section('content')
<div class="container-fluid py-4">
    <h4 class="mb-4 text-primary fw-bold"><i class="bi bi-shield-lock me-2"></i> Roles & Permissions</h4>

    <div class="row g-4">
        <!-- Create Role -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h6 class="mb-0 fw-bold">Create New Role</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Role Name</label>
                            <input type="text" name="name" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Assign Permissions</label>
                            <div class="row row-cols-2 g-2">
                                @foreach($permissions as $id => $name)
                                    <div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $name }}" id="create-{{ $id }}">
                                            <label class="form-check-label small" for="create-{{ $id }}">
                                                {{ ucwords(str_replace(['_', '-'], ' ', $name)) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-plus-circle"></i> Create Role
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Roles List -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Role</th>
                                    <th>Permissions</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                <tr>
                                    <td><strong class="text-primary">{{ $role->name }}</strong></td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($role->permissions->take(5) as $perm)
                                                <span class="badge bg-info text-dark small">{{ str_replace('_', ' ', $perm->name) }}</span>
                                            @endforeach
                                            @if($role->permissions->count() > 5)
                                                <span class="badge bg-secondary small">+{{ $role->permissions->count() - 5 }} more</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editRoleModal{{ $role->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <form action="{{ url('admin/roles/' . $role->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-content border-0 rounded-4 overflow-hidden">
                                                <div class="modal-header bg-gradient-primary text-white">
                                                    <h5 class="modal-title fw-bold">
                                                        <i class="bi bi-shield-lock"></i> Edit Role: {{ $role->name }}
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold">Role Name</label>
                                                        <input type="text" name="name" value="{{ $role->name }}" class="form-control rounded-3" required>
                                                    </div>
                                                    <div>
                                                        <label class="form-label fw-bold">Permissions</label>
                                                        <div class="row row-cols-2 g-3">
                                                            @foreach($permissions as $id => $name)
                                                                <div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $name }}"
                                                                            id="edit-perm-{{ $role->id }}-{{ $id }}"
                                                                            {{ $role->hasPermissionTo($name) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="edit-perm-{{ $role->id }}-{{ $id }}">
                                                                            {{ ucwords(str_replace(['_', '-'], ' ', $name)) }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success rounded-pill px-4">
                                                        <i class="bi bi-check2-all"></i> Update Role
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <i class="bi bi-shield-slash display-4"></i><br>
                                        No roles created yet
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection