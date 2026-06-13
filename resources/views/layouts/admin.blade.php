<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administración') | Huellitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/huellitas.css') }}" rel="stylesheet">
</head>
<body class="admin-body">
    <nav class="navbar navbar-dark navbar-huellitas fixed-top shadow-sm">
        <div class="container-fluid">
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#adminSidebar" aria-controls="adminSidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">Huellitas Admin</a>
            <div class="d-flex align-items-center gap-3 text-white">
                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-light btn-sm" type="submit">Salir</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="offcanvas-lg offcanvas-start sidebar-huellitas admin-sidebar" tabindex="-1"
         id="adminSidebar" aria-labelledby="adminSidebarLabel">
        <div class="offcanvas-header border-bottom border-light">
            <h5 class="offcanvas-title" id="adminSidebarLabel">Menú administrativo</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    data-bs-target="#adminSidebar" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body p-3">
            <nav class="nav flex-column gap-1">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                   href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('admin.tipos-mascotas.*') ? 'active' : '' }}"
                   href="{{ route('admin.tipos-mascotas.index') }}">
                    <i class="bi bi-tags me-2"></i>Tipos de mascotas
                </a>
                <a class="nav-link {{ request()->routeIs('admin.razas.*') ? 'active' : '' }}"
                   href="{{ route('admin.razas.index') }}">
                    <i class="bi bi-diagram-3 me-2"></i>Razas
                </a>
                <a class="nav-link {{ request()->routeIs('admin.mascotas.*') ? 'active' : '' }}"
                   href="{{ route('admin.mascotas.index') }}">
                    <i class="bi bi-heart me-2"></i>Mascotas
                </a>
                <a class="nav-link {{ request()->routeIs('admin.requisitos.*') ? 'active' : '' }}"
                   href="{{ route('admin.requisitos.index') }}">
                    <i class="bi bi-card-checklist me-2"></i>Requisitos
                </a>
                <a class="nav-link {{ request()->routeIs('admin.adoptantes.*') ? 'active' : '' }}"
                   href="{{ route('admin.adoptantes.create') }}">
                    <i class="bi bi-person-plus me-2"></i>Registrar adoptante
                </a>
                <hr class="border-light opacity-50">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="bi bi-house me-2"></i>Ir al inicio
                </a>
            </nav>
        </div>
    </div>

    <main class="admin-content">
        <div class="container-fluid py-4">
            <x-alerts />
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
