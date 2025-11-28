<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'Mana') }} | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Mana Management System" name="description" />
    <meta content="Your Name" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('app-assets/images/logo-sm.png') }}">

    <!-- LOTTIE PLAYER (ONLY ADDITION) -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- YOUR EXISTING CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="{{ asset('app-assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <link href="{{ asset('app-assets/libs/switchery/switchery.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/libs/multiselect/multi-select.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/libs/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/libs/custombox/custombox.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link href="{{ asset('app-assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- LOTTIE TRANSITION OVERLAY + TEAL THEME -->
    <style>
        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
        }

        #wrapper { display: flex; flex-direction: column; min-height: 100vh; }
        .content-page { margin-left: 250px; margin-top: 70px; padding-bottom: 60px; transition: margin-left .3s; }
        @media (max-width: 991px) { .content-page { margin-left: 0; margin-top: 60px; } }

        /* LOTTIE PAGE TRANSITION OVERLAY */
        #page-transition {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }

        #page-transition.active {
            opacity: 1;
            pointer-events: all;
        }

        #page-transition .lottie-container {
            width: 100px;
            height: 100px;
        }

        /* Keep your chat button */
        .chat-button {
            position: fixed; bottom: 20px; right: 20px;
            background: #0077B6; color: #fff; border: none; border-radius: 50%;
            width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,.3); z-index: 1000; cursor: pointer;
            animation: bounce 2s infinite;
        }
        .chat-button:hover { background: #0096C7; transform: scale(1.1); }
        @keyframes bounce { 0%,20%,50%,80%,100% { transform: translateY(0); } 40% { transform: translateY(-10px); } 60% { transform: translateY(-5px); } }
    </style>
</head>
<body>
    <!-- LOTTIE TRANSITION OVERLAY -->
    <div id="page-transition">
        <div class="lottie-container">
            <lottie-player
                src="https://assets6.lottiefiles.com/packages/lf20_3n3m3jrx.json"
                background="transparent"
                speed="1.8"
                loop
                autoplay>
            </lottie-player>
        </div>
    </div>

    <div id="wrapper">
        @include('components.top-bar')
        @include('components.side-bar')

        <div class="content-page">
            <div class="content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible my-3">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>

            @include('components.footer')
        </div>

        {{-- Uncomment when ready
        @if (Auth::check())
            <a href="{{ route('chat.index') }}" class="chat-button">
                <i class="fas fa-comments"></i>
            </a>
        @endif
        --}}
    </div>

    <!-- YOUR EXISTING JS -->
    <script src="{{ asset('app-assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('app-assets/bootstrap-5.0.2/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/app.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/jquery-steps/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/switchery/switchery.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/multiselect/jquery.multi-select.js') }}"></script>
    <script src="{{ asset('app-assets/libs/jquery-quicksearch/jquery.quicksearch.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/autocomplete/jquery.autocomplete.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/bootstrap-filestyle2/bootstrap-filestyle.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/custombox/custombox.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/rwd-table/rwd-table.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/morris-js/morris.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('app-assets/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/pages/dashboard.init.js') }}"></script>
    <script src="{{ asset('app-assets/js/pages/form-wizard.init.js') }}"></script>
    {{--  <script src="{{ asset('app-assets/js/pages/form-advanced.init.js') }}"></script>  --}}
    <script src="{{ asset('app-assets/js/pages/sweetalerts.init.js') }}"></script>
    <script src="{{ asset('app-assets/select2/js/select2Init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- LOTTIE PAGE TRANSITION SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const overlay = document.getElementById('page-transition');

            // Hide overlay after first load
            setTimeout(() => {
                overlay.classList.remove('active');
            }, 600);

            // Intercept ALL internal links
            document.querySelectorAll('a[href^="/"]:not([href*="mailto"]):not([href*="tel"]):not([target]):not(.no-transition)').forEach(link => {
                link.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (href === '#' || href.startsWith('javascript:')) return;

                    e.preventDefault();

                    overlay.classList.add('active');

                    setTimeout(() => {
                        window.location = href;
                    }, 500);
                });
            });

            // Also handle browser back/forward
            window.addEventListener('pageshow', function (e) {
                if (e.persisted) {
                    overlay.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>