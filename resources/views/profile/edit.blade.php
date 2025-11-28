@extends('components.main-layout')

@section('title', 'My Profile')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">

            <!-- Profile Header -->
            <div class="text-center mb-5">
                <div class="avatar-xl mx-auto mb-3 bg-gradient-primary text-white d-flex align-items-center justify-content-center rounded-circle shadow-lg">
                    <span class="fs-2 fw-bold">{{ Str::substr(Auth::user()->name, 0, 2) }}</span>
                </div>
                <h3 class="mb-1 fw-bold text-dark">{{ Auth::user()->name }}</h3>
                <p class="text-muted small">{{ Auth::user()->email }}</p>
            </div>

            <!-- Success Alert -->
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn" 
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    <i class="bi bi-check-circle-fill me-2"></i> Profile updated successfully!
                </div>
            @endif

            @if (session('status') === 'password-updated')
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn"
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    <i class="bi bi-shield-check me-2"></i> Password changed successfully!
                </div>
            @endif

            <!-- PROFILE FORM -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-person-circle me-2"></i> Profile Information
                    </h5>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                        @csrf @method('patch')

                        <!-- Name -->
                        <div class="form-group">
                            <label class="form-label fw-semibold text-dark">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="bi bi-person text-primary"></i>
                                </span>
                                <x-text-input id="name" name="name" type="text" class="form-control rounded-end ps-0"
                                              :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-2 small text-danger" />
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label class="form-label fw-semibold text-dark">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="bi bi-envelope text-primary"></i>
                                </span>
                                <x-text-input id="email" name="email" type="email" class="form-control rounded-end ps-0"
                                              :value="old('email', $user->email)" required autocomplete="username" />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 small text-danger" />

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-3 p-3 bg-warning bg-opacity-10 border border-warning rounded-3">
                                    <p class="small mb-2">
                                        <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                                        Your email is not verified.
                                    </p>
                                    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                            Resend Verification Email
                                        </button>
                                    </form>
                                    @if (session('status') === 'verification-link-sent')
                                        <span class="text-success small ms-2">
                                            <i class="bi bi-check"></i> Link sent!
                                        </span>
                                    @endif
                                </div>
                            @else
                                <small class="text-success d-block mt-1">
                                    <i class="bi bi-shield-check text-success me-1"></i> Email verified
                                </small>
                            @endif
                        </div>


                        <!-- Phone Number -->
<div class="form-group">
    <label class="form-label fw-semibold text-dark">Phone Number <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text bg-light border-0">
            <i class="bi bi-phone text-primary"></i>
        </span>
        <div class="form-control rounded-end ps-0 d-flex align-items-center border-0 bg-transparent p-0">
            <span class="text-muted px-2">+255</span>
            <input 
                type="text" 
                id="phone-input" 
                name="phone" 
                value="{{ substr(old('phone', $user->phone ?? ''), 3) }}" 
                class="border-0 flex-fill outline-0" 
                placeholder="712 345 678" 
                maxlength="9" 
                required 
                oninput="formatPhone(this)"
            />
        </div>
    </div>
    <small class="text-muted d-block mt-1">Enter 9 digits after +255</small>
    <x-input-error :messages="$errors->get('phone')" class="mt-2 small text-danger" />
</div>

                        <!-- Save Button -->
                        <div class="d-flex align-items-center gap-3 pt-3">
                            <x-primary-button class="btn btn-primary rounded-pill px-5 shadow-sm">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- PASSWORD FORM -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-danger text-white py-3 px-4">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-lock-fill me-2"></i> Update Password
                    </h5>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                        @csrf @method('put')

                        <!-- Current Password -->
                        <div class="form-group">
                            <label class="form-label fw-semibold text-dark">Current Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="bi bi-key text-danger"></i>
                                </span>
                                <x-text-input id="update_password_current_password" name="current_password" type="password"
                                              class="form-control rounded-end ps-0" autocomplete="current-password" />
                            </div>
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 small text-danger" />
                        </div>

                        <!-- New Password -->
                        <div class="form-group">
                            <label class="form-label fw-semibold text-dark">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="bi bi-shield-lock text-danger"></i>
                                </span>
                                <x-text-input id="update_password_password" name="password" type="password"
                                              class="form-control rounded-end ps-0" autocomplete="new-password" />
                            </div>
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 small text-danger" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label class="form-label fw-semibold text-dark">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="bi bi-shield-check text-danger"></i>
                                </span>
                                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                                              class="form-control rounded-end ps-0" autocomplete="new-password" />
                            </div>
                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 small text-danger" />
                        </div>

                        <!-- Save Button -->
                        <div class="d-flex align-items-center gap-3 pt-3">
                            <x-primary-button class="btn btn-danger rounded-pill px-5 shadow-sm">
                                <i class="bi bi-arrow-repeat me-1"></i> Update Password
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ANIMATIONS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>


<script>
function formatPhone(input) {
    let value = input.value.replace(/\D/g, '');

    // Remove leading 255 if present
    if (value.startsWith('255')) value = value.slice(3);
    if (value.length > 9) value = value.slice(0, 9);

    // Format: 712 345 678
    if (value.length > 6) {
        value = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6);
    } else if (value.length > 3) {
        value = value.slice(0, 3) + ' ' + value.slice(3);
    }

    input.value = value;
}

// Format on load
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('phone-input');
    if (input && input.value) formatPhone(input);
});
</script>
<style>
    :root {
        --bs-primary: #667eea;
        --bs-primary-rgb: 102, 126, 234;
        --bs-danger: #f5365c;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .bg-gradient-danger {
        background: linear-gradient(135deg, #f5365c 0%, #f56036 100%);
    }

    .avatar-xl {
        width: 100px; height: 100px; font-size: 2.2rem;
    }

    .card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,.05) !important;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
    }

    .form-control, .form-select {
        border-radius: 0.5rem;
        padding: 0.65rem 1rem;
        font-size: 0.95rem;
    }

    .input-group-text {
        border-radius: 0.5rem 0 0 0.5rem;
    }

    .btn {
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .form-group {
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
        transform: translateY(10px);
    }
    .form-group:nth-child(1) { animation-delay: 0.1s; }
    .form-group:nth-child(2) { animation-delay: 0.2s; }
    .form-group:nth-child(3) { animation-delay: 0.3s; }

    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 576px) {
        .card-body { padding: 1.5rem !important; }
        .avatar-xl { width: 80px; height: 80px; font-size: 1.8rem; }
    }
</style>

<script>
    // Auto-hide alerts after 3s
    document.addEventListener('DOMContentLoaded', () => {
        const alerts = document.querySelectorAll('.alert[x-data]');
        alerts.forEach(alert => {
            const data = Alpine.store('alert') || { show: true };
            setTimeout(() => {
                alert.__x.$data.show = false;
            }, 3000);
        });
    });
</script>
@endsection