@extends('components.main-layout')
@section('title', 'Users & Staff')
@section('content')

<div class="container-fluid py-3 py-md-4">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        @can('manage users')
            <div>
                <h4 class="mb-1 text-primary fw-bold">
                    <i class="bi bi-people-fill me-2"></i> Users & Staff
                </h4>
                <p class="text-muted mb-0 small">Manage doctors, reception, lab, pharmacy and all staff</p>
            </div>

            <button type="button" class="btn btn-primary d-none d-sm-flex align-items-center shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-plus-circle-fill me-2"></i> Add Staff
            </button>
        @endcan
    </div>

    <!-- Users Grid -->
    <div class="row g-3 g-md-4">
        @forelse($users as $user)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative hover-lift">
                    <!-- Role Badge -->
                    <div class="position-absolute top-0 end-0 p-2 z-3">
                        <span class="badge rounded-pill px-3 py-2 text-white
                            @if($user->hasRole('Admin')) bg-gradient-danger
                            @elseif($user->hasRole('Doctor')) bg-gradient-success
                            @elseif($user->hasRole('Reception')) bg-gradient-info
                            @elseif($user->hasRole('Lab')) bg-gradient-purple
                            @elseif($user->hasRole('Pharmacy')) bg-gradient-warning
                            @else bg-gradient-primary @endif
                            small fw-medium">
                            <i class="bi
                                @if($user->hasRole('Admin')) bi-shield-lock
                                @elseif($user->hasRole('Doctor')) bi-stethoscope
                                @elseif($user->hasRole('Reception')) bi-person-lines-fill
                                @elseif($user->hasRole('Lab')) bi-vial
                                @elseif($user->hasRole('Pharmacy')) bi-capsule
                                @else bi-person @endif
                            "></i>
                            {{ $user->roles->first()->name ?? 'User' }}
                        </span>
                    </div>

                    <div class="card-body p-4 d-flex flex-column text-center">
                        <div class="avatar-lg mx-auto mb-3 bg-soft-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-person-fill text-primary" style="font-size: 2.5rem;"></i>
                        </div>

                        <h5 class="card-title mb-1 text-dark fw-semibold fs-6">
                            {{ Str::limit($user->name, 20) }}
                        </h5>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-person-badge"></i> {{ $user->employee_code }}
                        </p>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-building"></i> {{ $user->department }}
                        </p>

                        <div class="d-flex gap-2 justify-content-center mt-auto">
                            <button type="button" class="btn btn-outline-info btn-sm rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#showUserModal"
                                    onclick='showUser({!! json_encode($user->load('roles')) !!})'>
                                <i class="bi bi-eye"></i>
                            </button>

                            @can('manage users')
                                <button type="button" class="btn btn-outline-warning btn-sm rounded-pill"
                                        data-bs-toggle="modal" data-bs-target="#editUserModal"
                                        onclick='editUser({!! json_encode($user) !!})'>
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"
                                            onclick="return confirm('Delete this user?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 my-4">
                    <i class="bi bi-people text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                    <h5 class="text-muted mb-2">No staff added yet</h5>
                    <p class="text-muted small mb-4">Start adding doctors and staff members</p>
                    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createUserModal">
                        <i class="bi bi-plus-circle me-2"></i> Add First Staff
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Floating Add Button (Mobile) -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <button class="btn btn-primary rounded-circle shadow-lg d-sm-none p-3 lh-1"
            data-bs-toggle="modal" data-bs-target="#createUserModal"
            style="width: 56px; height: 56px;">
        <i class="bi bi-plus fs-4"></i>
    </button>
</div>

@include('users.modals.create')
@include('users.modals.edit')
@include('users.modals.show')

<style>
.bg-gradient-danger { background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%); }
.bg-gradient-success { background: linear-gradient(135deg, #a7f3d0 0%, #6ee7b7 100%); }
.bg-gradient-info { background: linear-gradient(135deg, #a8edea 0%, #5ee7df 100%); }
.bg-gradient-purple { background: linear-gradient(135deg, #d8b4fe 0%, #c084fc 100%); }
.bg-gradient-warning { background: linear-gradient(135deg, #fed6a0 0%, #fbbf24 100%); }

.hover-lift { transition: all .3s cubic-bezier(.34,1.56,.64,1); }
.hover-lift:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0,0,0,.1)!important;
}
.avatar-lg { width:5rem; height:5rem; }
.bg-soft-primary { background-color:rgba(13,110,253,.15)!important; }
.rounded-4 { border-radius:1rem!important; }
</style>

<script>
function editUser(user) {
    const form = document.getElementById('editUserForm');
    form.action = `/users/${user.id}`;
    document.getElementById('edit-name').value = user.name;
    document.getElementById('edit-email').value = user.email;
    document.getElementById('edit-phone').value = user.phone;
    document.getElementById('edit-department').value = user.department;
    document.getElementById('edit-role').value = user.roles[0]?.name || '';
}

function showUser(user) {
    document.getElementById('show-name').textContent = user.name;
    document.getElementById('show-code').textContent = user.employee_code;
    document.getElementById('show-email').textContent = user.email;
    document.getElementById('show-phone').textContent = user.phone;
    document.getElementById('show-department').textContent = user.department;
    document.getElementById('show-role').textContent = user.roles[0]?.name || '—';
    document.getElementById('show-status').innerHTML = user.is_active 
        ? '<span class="badge bg-success">Active</span>' 
        : '<span class="badge bg-danger">Inactive</span>';
}
</script>
@endsection