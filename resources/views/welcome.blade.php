@extends('layouts.guest')

@section('title', 'Inicio')

@section('content')
    <section class="hero-huellitas card-huellitas p-4 p-md-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge text-bg-warning mb-3">Sistema de adopciones</span>
                <h1 class="display-5 fw-bold">Una base segura para encontrar nuevos hogares</h1>
                <p class="lead texto-secundario">
                    Huellitas administra mascotas, razas y requisitos de adopción.
                    El catálogo público y el flujo de solicitudes se incorporarán en la Parte 2.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    @guest
                        <a class="btn btn-huellitas btn-lg" href="{{ route('login') }}">Iniciar sesión</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('register') }}">Crear cuenta</a>
                    @else
                        @if (auth()->user()->esAdministrador())
                            <a class="btn btn-huellitas btn-lg" href="{{ route('admin.dashboard') }}">
                                Abrir panel administrativo
                            </a>
                        @else
                            <span class="alert alert-info mb-0">
                                Sesión iniciada como Adoptante. El catálogo estará disponible en la Parte 2.
                            </span>
                        @endif
                    @endguest
                </div>
            </div>
            <div class="col-lg-5">
                <div class="paw-placeholder text-center" aria-hidden="true">H</div>
            </div>
        </div>
    </section>
@endsection
