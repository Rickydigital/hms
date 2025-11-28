<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | TechNest</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0d9488',
                        'primary-dark': '#0f766e',
                    }
                }
            }
        }
    </script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .btn-primary {
            background: linear-gradient(135deg, #0d9488, #0f766e);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13,148,136,0.3);
        }
        .input-group {
            position: relative;
        }
        .input-group .bi {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            z-index: 10;
        }
        .input-group input {
            padding-left: 40px !important;
        }
    </style>
</head>

<body class="h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">

        <!-- Header with Lottie + Title -->
        <div class="bg-gradient-to-r from-primary to-primary-dark p-6 text-white text-center">
            <div class="mx-auto w-32 h-32 mb-4">
                <lottie-player
                    src="https://assets1.lottiefiles.com/packages/lf20_2gj2n2b6.json"
                    background="transparent"
                    speed="1"
                    loop
                    autoplay
                ></lottie-player>
            </div>
            <h1 class="text-2xl font-bold">Set New Password</h1>
            <p class="text-sm opacity-90 mt-1">Choose a strong, unique password</p>
        </div>

        <!-- Form Body -->
        <div class="p-6 space-y-5">

            <!-- Success Message -->
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Hidden Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-envelope me-1"></i> Email Address
                    </label>
                    <div class="input-group">
                        <i class="bi bi-envelope"></i>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $request->email) }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                            placeholder="you@example.com"
                        />
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-shield-lock me-1"></i> New Password
                    </label>
                    <div class="input-group">
                        <i class="bi bi-shield-lock"></i>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                            placeholder="••••••••"
                        />
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-shield-check me-1"></i> Confirm Password
                    </label>
                    <div class="input-group">
                        <i class="bi bi-shield-check"></i>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                            placeholder="••••••••"
                        />
                    </div>
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-3">
                    <button type="submit" class="w-full btn-primary text-white font-semibold py-3 rounded-lg flex items-center justify-center space-x-2">
                        <i class="bi bi-check-circle"></i>
                        <span>Reset Password</span>
                    </button>
                </div>

                <!-- Back to Login -->
                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="text-sm text-primary hover:underline flex items-center justify-center space-x-1">
                        <i class="bi bi-arrow-left"></i>
                        <span>Back to Login</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 text-center text-xs text-gray-500 border-t">
            © {{ date('Y') }} <strong class="text-primary">TechNest</strong>. Secured & Powered by O3Plus.
        </div>
    </div>
</div>

<!-- Lottie Player -->
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

</body>
</html>