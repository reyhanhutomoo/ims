<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <i class="nav-icon fa fa-users"></i>
        <p>
            Magang
            <i class="fas fa-angle-left right"></i>
            <span class="badge badge-info right">3</span>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
        </li>
        <li class="nav-item">
            <a
                href="{{ route('admin.employees.index') }}"
                class="nav-link">
                <i class="far fa-user nav-icon"></i>
                <p>Peserta Magang</p>
            </a>
        </li>
        <li class="nav-item">
            <a
                href="{{ route('admin.employees.attendance') }}"
                class="nav-link">
                <i class="far fa-calendar-check-o nav-icon"></i>
                <p>Absensi Magang</p>
            </a>
        </li>
        <li class="nav-item">
            <a
                href="{{ route('admin.employees.weeklyreports') }}"
                class="nav-link">
                <i class="far fa-file nav-icon"></i>
                <p>Laporan Mingguan</p>
            </a>
        </li>
    </ul>
</li>
<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <i class="nav-icon fa fa-calendar-times-o"></i>
        <p>
            Daftar Cuti Magang
            <i class="fas fa-angle-left right"></i>
            <span class="badge badge-info right">1</span>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a
                href="{{ route('admin.leaves.index') }}"
                class="nav-link"
            >
                <i class="far fa-calendar-minus nav-icon"></i>
                <p>Cuti</p>
            </a>
        </li>
    </ul>
</li>
<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <i class="nav-icon fa fa-gear"></i>
        <p>
            Kelola
            <i class="fas fa-angle-left right"></i>
            <span class="badge badge-info right">3</span>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a
            href="{{ route('admin.admin.index') }}"
            class="nav-link">
            <i class="fa fa-user nav-icon"></i>
            <p>Data Admin</p>
        </a>
            <a
                href="{{ route('admin.division.index') }}"
                class="nav-link">
                <i class="fa fa-users nav-icon"></i>
                <p>Divisi</p>
            </a>
            <a
                href="{{ route('admin.campus.index') }}"
                class="nav-link">
                <i class="far fa-building nav-icon"></i>
                <p>Kampus</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ route('admin.moa.dashboard') }}" class="nav-link {{ (request()->is('admin/moa/dashboard')) ? 'active' : '' }}">
        <i class="nav-icon fas fa-chart-bar"></i>
        <p>Dashboard MOA/IA</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.moa.index') }}" class="nav-link {{ (request()->is('admin/moa') || request()->is('admin/moa/*')) ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-signature"></i>
        <p>Review MOA/IA</p>
    </a>
</li>