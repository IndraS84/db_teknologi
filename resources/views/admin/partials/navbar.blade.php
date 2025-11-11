<header class="topbar">
    <nav class="navbar top-navbar navbar-toggleable-sm navbar-light">
        <!-- ============================================================== -->
        <!-- Logo -->
        <!-- ============================================================== -->
        <div class="navbar-header">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <!-- Logo icon -->
                <b>
                    <!-- Dark Logo icon -->
                    <img src="{{ asset('monster/assets/images/logo-icon.png') }}" alt="homepage" class="dark-logo" />
                </b>
                <!--End Logo icon -->
                <!-- Logo text -->
                <span>
                    <!-- dark Logo text -->
                    <img src="{{ asset('monster/assets/images/logo-text.png') }}" alt="homepage" class="dark-logo" />
                </span>
            </a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <div class="navbar-collapse">
            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->
            <ul class="navbar-nav mr-auto mt-md-0">
                <!-- This is  -->
                <li class="nav-item">
                    <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)">
                        <i class="ti-menu"></i>
                    </a>
                </li>
                <li class="nav-item hidden-sm-down">
                    <form class="app-search p-l-20">
                        <input type="text" class="form-control" placeholder="Search for...">
                        <a class="srh-btn"><i class="ti-search"></i></a>
                    </form>
                </li>
            </ul>
            <!-- ============================================================== -->
            <!-- User profile and search -->
            <!-- ============================================================== -->
            <ul class="navbar-nav my-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ asset('monster/assets/images/users/1.jpg') }}" alt="user" class="profile-pic m-r-5" />
                        {{ Auth::guard('admin')->user()->nama_admin ?? 'Admin' }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-user m-r-5"></i> Profile</a>
                        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-wallet m-r-5"></i> My Balance</a>
                        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-email m-r-5"></i> Inbox</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-settings m-r-5"></i> Account Setting</a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline-block w-100 m-0 p-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-left" style="border:none; background:none; width:100%;">
                                <i class="fa fa-power-off m-r-5"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>

