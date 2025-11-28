<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Responsive bootstrap 4 admin template" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('app-assets/images/logo-sm.png') }}">


    <!-- App css -->
    <link href="{{ asset('app-assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"
        id="bootstrap-stylesheet" />
    <link href="{{ asset('app-assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-stylesheet" />
</head>

<body>
    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="text-center account-logo-box">
                            <div class="mt-2 mb-2">
                                <a href="{{ url('/') }}" class="text-success">
                                    <span><img src="{{ asset('app-assets/images/logo.png') }}" alt=""
                                            height="36"></span>
                                </a>
                            </div>
                        </div>

                        <div class="card-body">

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <!-- Display validation errors -->


                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Yield the form content -->
                            @yield('form')
                        </div>
                        <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <!-- Yield the footer link -->

                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
        {{-- @include('components.footer') --}}
    </div>
    <!-- end page -->

    <!-- Vendor js -->
    <script src="{{ asset('app-assets/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('app-assets/js/app.min.js') }}"></script>
</body>

</html>
