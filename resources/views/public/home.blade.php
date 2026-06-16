@extends('layouts.public')

@section('title', 'Encuentra a tu nuevo mejor amigo')

@section('content')
    <section class="container">
        <div class="hero-anipat p-4 p-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <p class="hero-eyebrow mb-2">Refugio Huellitas</p>
                    <h1 class="display-4 mb-3">
                        Encuentra a tu <span class="hero-highlight">nuevo mejor amigo</span>
                    </h1>
                    <p class="lead texto-secundario mb-4" style="max-width: 34rem;">
                        Aquí cada animalito tiene su propia historia y un montón de amor
                        para dar. Date una vuelta por el catálogo… tu nuevo mejor amigo
                        podría estar esperándote.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a class="btn-cta btn-lg" href="{{ route('mascotas.catalogo') }}">
                            <i class="bi bi-search me-1"></i> Ver catálogo
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-pet-wrapper">
                        <img class="hero-pet" src="{{ asset('img/hero-mascotas-v3.png') }}"
                             alt="Perro y gato del refugio Huellitas">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container mt-4 mt-lg-5">
        <div class="row g-3 g-lg-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number">{{ $totalDisponibles }}</div>
                    <p class="texto-secundario mb-0">Mascotas disponibles</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number">{{ $totalAdoptadas }}</div>
                    <p class="texto-secundario mb-0">Adopciones logradas</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number">{{ $totalTipos }}</div>
                    <p class="texto-secundario mb-0">Tipos de mascota</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container mt-5">
        <div class="text-center mb-4">
            <p class="section-eyebrow mb-1">Así de fácil</p>
            <h2 class="section-title">¿Cómo funciona la adopción?</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-icon mb-3"><i class="bi bi-search-heart"></i></div>
                    <h3 class="h5">1. Explora</h3>
                    <p class="texto-secundario mb-0">
                        Recorre el catálogo y conoce a las mascotas que buscan un hogar.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-icon mb-3"><i class="bi bi-file-earmark-text"></i></div>
                    <h3 class="h5">2. Solicita</h3>
                    <p class="texto-secundario mb-0">
                        Crea tu cuenta y envía una solicitud de adopción para tu favorita.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-icon mb-3"><i class="bi bi-house-heart"></i></div>
                    <h3 class="h5">3. Adopta</h3>
                    <p class="texto-secundario mb-0">
                        El refugio revisa tu solicitud, agenda una cita y completas la adopción.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="container mt-5">
        <div class="text-center mb-4">
            <p class="section-eyebrow mb-1">Los más jóvenes</p>
            <h2 class="section-title mb-0">Conoce a los más peques 🐾</h2>
        </div>

        @if ($destacadas->isEmpty())
            <div class="card-huellitas p-5 text-center">
                <i class="bi bi-emoji-smile fs-1 texto-secundario"></i>
                <p class="texto-secundario mb-0 mt-2">Pronto tendremos nuevas mascotas disponibles.</p>
            </div>
        @else
            <div class="row g-4">
                @foreach ($destacadas as $mascota)
                    <div class="col-sm-6 col-lg-4">
                        <x-pet-card :mascota="$mascota" />
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
