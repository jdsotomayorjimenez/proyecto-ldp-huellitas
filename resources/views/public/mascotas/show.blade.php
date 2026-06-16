@extends('layouts.public')

@section('title', $mascota->nombre)

@section('content')
    @php
        $imagenPrincipal = $mascota->imagenes->firstWhere('es_principal', true) ?? $mascota->imagenes->first();
        $estadoSlug = str_replace('_', '-', $mascota->estado);
        $estadoLabel = ucfirst(str_replace('_', ' ', $mascota->estado));
        $tamanio = ucfirst(str_replace('pequeno', 'pequeño', $mascota->tamanio));
        $esAdmin = auth()->check() && auth()->user()->esAdministrador();
    @endphp

    <section class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mascotas.catalogo') }}">Catálogo</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $mascota->nombre }}</li>
            </ol>
        </nav>

        <div class="row g-4 g-lg-5">
            {{-- Galería --}}
            <div class="col-lg-6">
                <div class="pet-gallery-main mb-3">
                    @if ($imagenPrincipal)
                        <img id="galleryMain" src="{{ asset($imagenPrincipal->ruta) }}"
                             alt="Foto de {{ $mascota->nombre }}">
                    @else
                        <div class="pet-empty"><i class="bi bi-image" style="font-size:4rem;"></i></div>
                    @endif
                </div>
                @if ($mascota->imagenes->count() > 1)
                    <div class="row g-2">
                        @foreach ($mascota->imagenes as $img)
                            <div class="col-3">
                                <img class="pet-thumb" src="{{ asset($img->ruta) }}"
                                     alt="Foto de {{ $mascota->nombre }}"
                                     onclick="document.getElementById('galleryMain').src = this.src">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Información --}}
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <h1 class="section-title mb-0">{{ $mascota->nombre }}</h1>
                    <span class="badge rounded-pill badge-{{ $estadoSlug }}">{{ $estadoLabel }}</span>
                </div>
                <p class="texto-secundario mb-4">
                    {{ $mascota->raza->tipoMascota->nombre }} · {{ $mascota->raza->nombre }}
                </p>

                <div class="row g-3 mb-4">
                    <div class="col-6 col-sm-3">
                        <div class="spec-item">
                            <div class="spec-label">Género</div>
                            <div class="spec-value">{{ ucfirst($mascota->genero) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="spec-item">
                            <div class="spec-label">Tamaño</div>
                            <div class="spec-value">{{ $tamanio }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="spec-item">
                            <div class="spec-label">Edad</div>
                            <div class="spec-value">{{ $mascota->edad_legible }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="spec-item">
                            <div class="spec-label">Tipo</div>
                            <div class="spec-value">{{ $mascota->raza->tipoMascota->nombre }}</div>
                        </div>
                    </div>
                </div>

                @if ($mascota->descripcion)
                    <h2 class="h6 fw-bold">Sobre {{ $mascota->nombre }}</h2>
                    <p class="texto-secundario">{{ $mascota->descripcion }}</p>
                @endif

                @if ($requisitos->isNotEmpty())
                    <h2 class="h6 fw-bold mt-4">Requisitos para adoptar</h2>
                    <ul class="list-unstyled d-grid gap-2">
                        @foreach ($requisitos as $requisito)
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill" style="color: var(--color-exito);"></i>
                                <span>
                                    {{ $requisito->nombre }}
                                    @unless ($requisito->obligatorio)
                                        <span class="badge text-bg-light">Opcional</span>
                                    @endunless
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- CTA de adopción --}}
                <div class="mt-4 pt-3 border-top">
                    @if ($mascota->estado !== 'disponible')
                        <button class="btn-cta btn-lg w-100" disabled>
                            Esta mascota no está disponible
                        </button>
                    @elseif ($esAdmin)
                        <div class="alert alert-info mb-0">
                            Estás en sesión de administrador. Las solicitudes las envían los adoptantes.
                        </div>
                    @elseif (! auth()->check())
                        <a class="btn-cta btn-lg w-100" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Inicia sesión para adoptar
                        </a>
                    @elseif ($yaSolicitada)
                        <a class="btn-cta-ghost btn-lg w-100 text-center" href="{{ route('solicitudes.mis') }}">
                            Ya enviaste una solicitud · Ver estado
                        </a>
                    @else
                        <a class="btn-cta btn-lg w-100" href="{{ route('solicitudes.create', $mascota) }}">
                            <i class="bi bi-heart-fill me-1"></i> Solicitar adopción
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
