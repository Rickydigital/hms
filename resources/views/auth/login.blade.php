<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mana Login</title>

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Lottie Player -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <style>
        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-600: #6c757d;
            --gray-800: #343a40;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            margin: 0;
            overflow: hidden;
        }

        /* Page Transition Overlay */
        #page-transition {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.6s ease;
            pointer-events: none;
        }

        #page-transition.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .lottie-container {
            width: 120px;
            height: 120px;
        }

        .auth-card {
            background: white;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px rgba(13, 148, 136, 0.15);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
            animation: fadeInUp 0.7s ease-out;
            position: relative;
            z-index: 10;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .auth-header::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 100%; height: 30px;
            background: white;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
            transform: scaleX(1.5);
        }

        .logo-icon {
            width: 80px; height: 80px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.2rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .auth-body {
            padding: 2rem;
            background: white;
        }

        .form-control, .form-select {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            border: 1.8px solid var(--gray-200);
            font-size: 0.95rem;
            transition: all 0.25s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.22rem rgba(13, 148, 136, 0.18);
        }

        .input-group-text {
            background: var(--gray-100);
            border: 1.8px solid var(--gray-200);
            border-right: none;
            border-radius: 0.75rem 0 0 0.75rem;
            color: var(--primary);
            font-weight: 600;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 0.75rem;
            padding: 0.8rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(13, 148, 136, 0.3);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(13, 148, 136, 0.4);
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .text-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .text-link:hover {
            color: var(--primary-dark);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: var(--gray-600);
            font-size: 0.875rem;
            margin: 1.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1.5px solid var(--gray-200);
        }

        .divider::before { margin-right: 1rem; }
        .divider::after { margin-left: 1rem; }

        @media (max-width: 576px) {
            .auth-card { margin: 1rem; border-radius: 1rem; }
            .auth-header { padding: 2rem 1.5rem; }
            .logo-icon { width: 65px; height: 65px; font-size: 1.8rem; }
            .auth-body { padding: 1.5rem; }
        }
    </style>
</head>
<body>

    <!-- LOTTIE TRANSITION OVERLAY -->
    <div id="page-transition">
        <div class="lottie-container">
            <lottie-player
                src="https://assets1.lottiefiles.com/packages/lf20_u8jppxsl.json"
                background="transparent"
                speed="1.5"
                loop
                autoplay>
            </lottie-player>
        </div>
    </div>

    <!-- LOGIN CARD -->
    <div class="auth-card">
        <div class="auth-header">
            <div class="logo-icon">
                <i class="bi bi-building"></i>
            </div>
            <h4 class="mb-0 fw-bold">Mana Portal</h4>
            <p class="mb-0 small opacity-90 mt-1">Secure access to your properties</p>
        </div>

        <div class="auth-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email') }}" required autofocus 
                               placeholder="you@example.com">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-danger small" />
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" 
                               required autocomplete="current-password" placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-danger small" />
                </div>

                <!-- Remember Me -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label text-dark" for="remember">Remember me</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-link small">Forgot?</a>
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Login Now
                </button>
            </form>

            <!-- No Register -->
            <div class="text-center mt-4">
                <p class="text-muted small mb-0">
                    Access restricted to authorized Manas only.
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Page Transition Animation -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const overlay = document.getElementById('page-transition');

            // Hide after animation
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 800);

            // Trigger on all internal link clicks
            document.querySelectorAll('a[href^="/"]:not([href*="mailto"]):not([href*="tel"])').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');

                    overlay.classList.remove('hidden');
                    setTimeout(() => {
                        window.location = href;
                    }, 400);
                });
            });
        });
    </script>
</body>
</html>