<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mana Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-600: #6c757d;
            --gray-800: #343a40;
            --gray-900: #212529;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 1rem;
        }

        .auth-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
            margin: auto;
        }

        .auth-header {
            background: var(--primary);
            color: white;
            padding: 2rem 1.5rem;
            text-align: center;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            backdrop-filter: blur(10px);
        }

        .auth-body {
            padding: 2rem 1.5rem;
        }

        .form-control, .form-select {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--gray-200);
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(13, 148, 136, 0.15);
        }

        .input-group-text {
            background: var(--gray-100);
            border: 1.5px solid var(--gray-200);
            border-right: none;
            border-radius: 0.75rem 0 0 0.75rem;
            color: var(--primary);
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 148, 136, 0.3);
        }

        .text-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .text-link:hover {
            color: var(--primary-dark);
        }

        .phone-input-container {
            display: flex;
            align-items: center;
            border: 1.5px solid var(--gray-200);
            border-radius: 0.75rem;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .phone-input-container:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(13, 148, 136, 0.15);
        }

        .phone-prefix {
            background: var(--gray-100);
            padding: 0.75rem 1rem;
            font-weight: 600;
            color: var(--gray-800);
            border-right: 1.5px solid var(--gray-200);
        }

        .phone-input {
            border: none;
            outline: none;
            padding: 0.75rem 1rem;
            flex: 1;
            font-size: 0.95rem;
        }

        @media (max-width: 576px) {
            .auth-card { margin: 1rem; }
            .auth-header { padding: 1.5rem 1rem; }
            .logo-icon { width: 60px; height: 60px; font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
            <div class="logo-icon">
                <i class="bi bi-house-door-fill"></i>
            </div>
            <h4 class="mb-0 fw-bold">Mana</h4>
            <p class="mb-0 small opacity-90">Create your account</p>
        </div>

        <!-- Body -->
        <div class="auth-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" name="name" class="form-control" 
                               value="{{ old('name') }}" required autofocus 
                               placeholder="John Doe">
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger small" />
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email') }}" required 
                               placeholder="you@example.com">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
                </div>

                <!-- Phone -->
                <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Phone Number</label>
                    <div class="phone-input-container">
                        <span class="phone-prefix">+255</span>
                        <input type="text" name="phone" id="phone" class="phone-input" 
                               value="{{ old('phone') ? substr(old('phone'), 3) : '' }}" 
                               placeholder="712 345 678" maxlength="11" required 
                               oninput="formatPhone(this)">
                    </div>
                    <small class="text-muted d-block mt-1">Enter 9 digits</small>
                    <x-input-error :messages="$errors->get('phone')" class="mt-2 text-danger small" />
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="password" class="form-control" 
                               required autocomplete="new-password" placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger small" />
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label class="form-label fw-medium text-dark">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-shield-check"></i>
                        </span>
                        <input type="password" name="password_confirmation" class="form-control" 
                               required autocomplete="new-password" placeholder="••••••••">
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus me-2"></i> Create Account
                </button>
            </form>

            <p class="text-center mt-4 mb-0">
                Already have an account?
                <a href="{{ route('login') }}" class="text-link fw-semibold">Login</a>
            </p>
        </div>
    </div>

    <script>
        function formatPhone(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.startsWith('255')) value = value.slice(3);
            if (value.length > 9) value = value.slice(0, 9);

            if (value.length > 6) {
                value = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6);
            } else if (value.length > 3) {
                value = value.slice(0, 3) + ' ' + value.slice(3);
            }

            input.value = value;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('phone');
            if (input && input.value) formatPhone(input);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>