@extends('layouts.public')

@section('title', 'Mis solicitudes')

@section('content')
    <section class="container">
        <div class="mb-4">
            <p class="section-eyebrow mb-1">Tu actividad</p>
            <h1 class="section-title mb-0">Mis solicitudes de adopción</h1>
        </div>

        @if ($solicitudes->isEmpty())
            <div class="card-huellitas p-5 text-center">
                <i class="bi bi-clipboard-heart fs-1 texto-secundario"></i>
                <h2 class="h5 mt-3">Aún no tienes solicitudes</h2>
                <p class="texto-secundario mb-3">Explora el catálogo y encuentra a tu nuevo compañero.</p>
                <a class="btn-cta d-inline-block" href="{{ route('mascotas.catalogo') }}">Ver catálogo</a>
            </div>
        @else
            <div class="row g-4">
                @foreach ($solicitudes as $solicitud)
                    @php
                        $mascota = $solicitud->mascota;
                        $imagen = $mascota->imagenes->firstWhere('es_principal', true) ?? $mascota->imagenes->first();
                        $estadoSlug = str_replace('_', '-', $solicitud->estado);
                    @endphp
                    <div class="col-lg-6">
                        <div class="card-huellitas h-100 p-3">
                            <div class="d-flex gap-3">
                                @if ($imagen)
                                    <img class="pet-thumbnail" style="height:80px;width:80px;"
                                         src="{{ asset($imagen->ruta) }}" alt="Foto de {{ $mascota->nombre }}">
                                @else
                                    <div class="pet-thumbnail pet-thumbnail-empty" style="height:80px;width:80px;">Sin foto</div>
                                @endif
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <a class="pet-name h5 text-decoration-none"
                                               href="{{ route('mascotas.show.public', $mascota) }}">{{ $mascota->nombre }}</a>
                                            <p class="pet-meta mb-1">
                                                {{ $mascota->raza->tipoMascota->nombre }} · {{ $mascota->raza->nombre }}
                                            </p>
                                        </div>
                                        <span class="badge rounded-pill badge-{{ $estadoSlug }}">
                                            {{ ucfirst($solicitud->estado) }}
                                        </span>
                                    </div>
                                    <p class="small texto-secundario mb-0">
                                        Solicitada el {{ $solicitud->fecha_solicitud?->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>

                            @if ($solicitud->respuesta)
                                <div class="alert alert-{{ $solicitud->respuesta->resultado === 'aprobada' ? 'success' : 'danger' }} mt-3 mb-0">
                                    <strong>Respuesta del refugio:</strong> {{ $solicitud->respuesta->respuesta }}
                                </div>
                            @elseif ($solicitud->estado === 'cancelada')
                                <div class="alert alert-secondary mt-3 mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Has cancelado esta solicitud. La mascota ha vuelto a estar disponible.
                                </div>
                            @endif

                            @if ($solicitud->cita)
                                <div class="alert alert-info mt-3 mb-0">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    <strong>Cita:</strong>
                                    {{ \Illuminate\Support\Carbon::parse($solicitud->cita->fecha)->format('d/m/Y') }}
                                    a las {{ \Illuminate\Support\Carbon::parse($solicitud->cita->hora)->format('H:i') }}
                                    · {{ $solicitud->cita->lugar }}
                                </div>
                            @endif

                            <div class="mt-3 d-flex gap-2">
                                @if ($solicitud->estado === 'pendiente')
                                    <form action="{{ route('solicitudes.cancelar', $solicitud) }}" method="POST"
                                        onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta solicitud?')">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            <i class="bi bi-x-circle me-1"></i> Cancelar solicitud
                                        </button>
                                    </form>
                                @endif

                                @if ($solicitud->adopcion)
                                    <a class="btn btn-sm btn-huellitas"
                                        href="{{ route('adopciones.certificado', $solicitud->adopcion) }}">
                                        <i class="bi bi-printer me-1"></i> Imprimir certificado
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
