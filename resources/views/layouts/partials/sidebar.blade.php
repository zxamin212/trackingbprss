{{-- resources/views/layouts/partials/sidebar.blade.php --}}
<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <div class="logo-header" data-background-color="dark">
            <a href="{{ auth()->check() ? route(auth()->user()->dashboardRoute()) : route('login') }}" class="logo">
                <span class="text-white fw-bold fs-5">Sahabat Sejati</span>
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">

                <li class="nav-item {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                    <a href="{{ auth()->check() ? route(auth()->user()->dashboardRoute()) : route('login') }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                @auth
                    @php $role = auth()->user()->role; @endphp

                    @if($role === 'cs')
                        <li class="nav-section"><h4 class="text-section">Proses Berkas</h4></li>
                        <li class="nav-item {{ request()->routeIs('cs.berkas.create') ? 'active' : '' }}">
                            <a href="{{ route('cs.berkas.create') }}">
                                <i class="fas fa-plus-circle"></i>
                                <p>Input Berkas Baru</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('cs.berkas.index') ? 'active' : '' }}">
                            <a href="{{ route('cs.berkas.index') }}">
                                <i class="fas fa-folder-open"></i>
                                <p>Verifikasi Berkas</p>
                            </a>
                        </li>
                    @elseif($role === 'senior_loan_officer')
                        <li class="nav-section"><h4 class="text-section">Proses Berkas</h4></li>
                        <li class="nav-item {{ request()->routeIs('slo.berkas.index') ? 'active' : '' }}">
                            <a href="{{ route('slo.berkas.index') }}">
                                <i class="fas fa-search-dollar"></i>
                                <p>Survey & Komite</p>
                            </a>
                        </li>
                    @elseif($role === 'admin_legal')
                        <li class="nav-section"><h4 class="text-section">Proses Berkas</h4></li>
                        <li class="nav-item {{ request()->routeIs('legal.berkas.index') ? 'active' : '' }}">
                            <a href="{{ route('legal.berkas.index') }}">
                                <i class="fas fa-file-signature"></i>
                                <p>Akad & Pencairan</p>
                            </a>
                        </li>
                    @elseif($role === 'admin')
                        <li class="nav-section"><h4 class="text-section">Monitoring</h4></li>
                        <li class="nav-item {{ request()->routeIs('admin.berkas.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.berkas.index') }}">
                                <i class="fas fa-th-list"></i>
                                <p>Semua Berkas</p>
                            </a>
                        </li>
                        <li class="nav-section"><h4 class="text-section">Pengaturan</h4></li>
                        <li class="nav-item {{ request()->routeIs('admin.kantor.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.kantor.index') }}">
                                <i class="fas fa-building"></i>
                                <p>Kantor</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.user.index') }}">
                                <i class="fas fa-users"></i>
                                <p>User</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.laporan.index') }}">
                                <i class="fas fa-chart-bar"></i>
                                <p>Laporan</p>
                            </a>
                        </li>
                    @elseif($role === 'direksi')
                        <li class="nav-section"><h4 class="text-section">Monitoring</h4></li>
                        <li class="nav-item {{ request()->routeIs('direksi.berkas.*') ? 'active' : '' }}">
                            <a href="{{ route('direksi.berkas.index') }}">
                                <i class="fas fa-th-list"></i>
                                <p>Semua Berkas</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('direksi.laporan.*') ? 'active' : '' }}">
                            <a href="{{ route('direksi.laporan.index') }}">
                                <i class="fas fa-chart-bar"></i>
                                <p>Laporan</p>
                            </a>
                        </li>
                    @endif
                @endauth

            </ul>
        </div>
    </div>
</div>