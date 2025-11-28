<!-- Topbar Start -->
<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-right mb-0">
        <!-- User Profile -->
        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <img src="{{ asset('app-assets/images/users/avatar-1.jpg') }}" alt="user-image" class="rounded-circle">
                    <span class="d-none d-sm-inline-block ml-1 text-white">{{ Auth::user()->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0">{{ Auth::user()->roles[0]['name'] ?? 'User' }}</h6>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item notify-item">
                        <i class="mdi mdi-account-outline"></i>
                        <span>Edit Profile</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item notify-item p-0 bg-transparent border-0 text-left w-100">
                            <i class="mdi mdi-logout-variant"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>

        <!-- TECHNEST LOGO -->
        <div class="logo-box d-flex align-items-center justify-content-center flex-grow-1">
            <a href="{{ route('dashboard') }}" class="logo text-center d-block">
                <!-- Full Logo - Always Visible -->
                <span class="logo-lg">
                    <span class="technest-text technest-lg">Tech<span class="text-accent">nest</span></span>
                </span>
                <!-- Short Logo - Hidden on Mobile -->
                <span class="logo-sm d-none">
                    <span class="technest-text technest-sm">T<span class="text-accent">n</span></span>
                </span>
            </a>
        </div>

        <!-- Mobile Menu Toggle - Hidden on Mobile -->
        <ul class="list-unstyled topnav-menu topnav-menu-left m-0 d-none d-lg-block">
            <li>
                <button class="button-menu-mobile waves-effect" id="mobile-menu-toggle">
                    <i class="mdi mdi-menu"></i>
                </button>
            </li>
        </ul>
    </div>
    <!-- end Topbar -->