<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Huellitas')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/huellitas.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-huellitas">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">Huellitas</a>
            <div class="d-flex gap-2 align-items-center">
                @auth
                    @if (auth()->user()->esAdministrador())
                        <a class="btn btn-light btn-sm" href="{{ route('admin.dashboard') }}">Panel admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-light btn-sm" type="submit">Cerrar sesión</button>
                    </form>
                @else
                    <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Ingresar</a>
                    <a class="btn btn-light btn-sm" href="{{ route('register') }}">Registrarse</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <x-alerts />
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
