<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"
                ><i class="fas fa-bars"></i
            ></a>
        </li>

    </ul>


    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <li class="nav-item dropdown user user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                @if (Auth::user()->employee && Auth::user()->employee->foto)
                    <img src="{{ asset('storage/photos/' . Auth::user()->employee->foto) }}" class="user-image img-circle elevation-2" alt="User Image">
                @else
                    <img src="{{ asset('images/blank_profile.png') }}" class="user-image img-circle elevation-2" alt="User Image">
                @endif
                <span class="hidden-xs">{{ Auth::user()->nama }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <li class="user-header bg-primary">
                    @if (Auth::user()->employee && Auth::user()->employee->foto)
                    <img src="{{ asset('storage/photos/' . Auth::user()->employee->foto) }}" class="img-circle elevation-2" alt="User Image">
                @else
                    <img src="{{ asset('images/blank_profile.png') }}" class="img-circle elevation-2" alt="User Image">
                @endif
                <p>
                    {{ Auth::user()->nama }}
                    @if ( Auth::user()->employee )
                    <br><small>Divisi {{ Auth::user()->employee->division->nama }}</small>
                    @else
                    <br><small>Admin</small>
                    @endif
                </p>
                </li>
                <!-- Menu Body -->
                <li class="user-body text-center">
                    @if ( Auth::user()->employee )
                    <small>Berakhir pada {{ \Carbon\Carbon::parse(Auth::user()->employee->tanggal_selesai)->format('d F Y') }}
                    </small>
                    @endif
                <!-- /.row -->
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                <div class="pull-left">
                    @if ( Auth::user()->employee )
                    <a href="{{ route('employee.profile') }}" class="btn btn-default btn-flat">Profile</a>
                    @else
                    <a href="{{ route('admin.reset-password') }}" class="btn btn-default btn-flat">Change Password</a>
                    @endif
                </div>
                <div class="pull-right">
                    <a href="{{ route('logout') }}" 
                    class="btn btn-default btn-flat"
                    onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();"
                    >Sign out</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
                </li>
            </ul>
        </li>
    </ul>
</nav>