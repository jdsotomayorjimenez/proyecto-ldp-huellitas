<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-huellitas.png') }}">
    <title>@yield('title', 'Adopta una mascota') | Huellitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/huellitas.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg public-navbar sticky-top d-print-none">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <img src="{{ asset('img/logo-huellitas-sf.png') }}" alt="Huellitas" width="32" height="32">
                Huellitas
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#publicNav" aria-controls="publicNav" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="publicNav">
                <ul class="navbar-nav mx-auto">
                    @auth
                        @unless (auth()->user()->esAdministrador())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('solicitudes.mis') ? 'active' : '' }}"
                                   href="{{ route('solicitudes.mis') }}">Mis solicitudes</a>
                            </li>
                        @endunless
                    @endauth
                </ul>
                <div class="d-flex align-items-center gap-2">
                    @auth
                        <span class="d-none d-lg-inline texto-secundario small">
                            Hola, {{ explode(' ', auth()->user()->name)[0] }}
                        </span>
                        @if (auth()->user()->esAdministrador())
                            <a class="btn-cta-ghost btn-sm" href="{{ route('admin.dashboard') }}">Panel admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn-salir-cafe btn-sm" type="submit">Cerrar sesión</button>
                        </form>
                    @else
                        <a class="btn-cta-ghost btn-sm" href="{{ route('login') }}">Ingresar</a>
                        <a class="btn-cta btn-sm" href="{{ route('register') }}">Crear cuenta</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        <div class="container py-4 d-print-none">
            <x-alerts />
        </div>
        @yield('content')
    </main>

    <footer class="public-footer mt-5 pt-5 pb-4 d-print-none">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="footer-brand h4 mb-2 d-flex align-items-center gap-2">
                        <img src="{{ asset('img/logo-huellitas-sf.png') }}" alt="Huellitas" width="34" height="34">
                        Huellitas
                    </div>
                    <p class="mb-0" style="max-width: 24rem;">
                        En Huellitas creemos que todos merecen una familia que los quiera.
                        Gracias por ayudarnos a que cada animalito encuentre su hogar y su
                        montón de mimos. 🧡
                    </p>
                </div>
                <div class="col-lg-5 footer-contact">
                    <h6 class="text-white mb-3">Contacto</h6>
                    <ul class="list-unstyled d-grid gap-2 mb-0">
                        <li><i class="bi bi-geo-alt me-2"></i>Guayaquil, Ecuador</li>
                        <li><i class="bi bi-envelope me-2"></i>contacto@huellitas.com</li>
                        <li><i class="bi bi-telephone me-2"></i>+593 99 999 9999</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <p class="small mb-0 text-center">
                © {{ date('Y') }} Huellitas · Sistema de adopción de mascotas.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
