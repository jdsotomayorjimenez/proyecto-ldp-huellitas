@extends('layouts.admin')

@section('title', 'Cita de '.$solicitud->mascota->nombre)

@section('content')
    @php
        $mascota = $solicitud->mascota;
        $tieneAdopcion = $solicitud->adopcion !== null;
        $citaCerrada = $cita->estado === 'cancelada' || $tieneAdopcion;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <a class="text-decoration-none small" href="{{ route('admin.citas.index') }}">
                <i class="bi bi-arrow-left"></i> Volver a citas
            </a>
            <h1 class="h3 mb-0 mt-1">Cita de adopción · {{ $mascota->nombre }}</h1>
        </div>
        <span class="badge estado-badge badge-{{ str_replace('_', '-', $cita->estado) }}">
            {{ ucfirst($cita->estado) }}
        </span>
    </div>

    <div class="row g-4">
        {{-- Columna izquierda: datos de la cita --}}
        <div class="col-lg-5">
            <div class="card card-huellitas mb-4">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-calendar-event me-1"></i> Datos de la cita</h2>
                    <p class="mb-1">
                        <i class="bi bi-clock me-1"></i>
                        {{ \Illuminate\Support\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                        a las {{ \Illuminate\Support\Carbon::parse($cita->hora)->format('H:i') }}
                    </p>
                    <p class="mb-1"><i class="bi bi-geo-alt me-1"></i> {{ $cita->lugar }}</p>
                    @if ($cita->indicaciones)
                        <p class="texto-secundario small mb-0">{{ $cita->indicaciones }}</p>
                    @endif
                    @if ($cita->estado === 'programada')
                        <div class="d-flex gap-2 mt-3">
                            <form method="POST" action="{{ route('admin.citas.update', $cita) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="estado" value="completada">
                                <button class="btn btn-sm btn-outline-success" type="submit">Marcar completada</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card card-huellitas mb-4">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-person me-1"></i> Adoptante y mascota</h2>
                    <dl class="row mb-0 small">
                        <dt class="col-5 texto-secundario">Adoptante</dt>
                        <dd class="col-7">{{ $solicitud->usuario->name }}</dd>
                        <dt class="col-5 texto-secundario">Mascota</dt>
                        <dd class="col-7">{{ $mascota->nombre }}</dd>
                        <dt class="col-5 texto-secundario">Tipo / raza</dt>
                        <dd class="col-7">{{ $mascota->raza->tipoMascota->nombre }} / {{ $mascota->raza->nombre }}</dd>
                    </dl>
                </div>
            </div>

            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.solicitudes.show', $solicitud) }}">
                <i class="bi bi-file-text me-1"></i> Ver solicitud
            </a>
        </div>

        {{-- Columna derecha: requisitos y adopción final --}}
        <div class="col-lg-7">
            {{-- Cumplimiento de requisitos --}}
            <div class="card card-huellitas mb-4">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-card-checklist me-1"></i> Requisitos verificados en cita</h2>
                    @forelse ($solicitud->cumplimientosRequisitos as $cumplimiento)
                        @if ($citaCerrada)
                            {{-- Cita cerrada o adopción registrada: solo lectura, sin botón Guardar --}}
                            <div class="border rounded p-2 mb-2 d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                <div>
                                    <span class="fw-semibold">{{ $cumplimiento->requisitoAdopcion->nombre }}</span>
                                    @if ($cumplimiento->requisitoAdopcion->obligatorio)
                                        <span class="badge text-bg-warning">Obligatorio</span>
                                    @endif
                                </div>
                                <span class="badge badge-{{ str_replace('_', '-', $cumplimiento->estado) }}">
                                    {{ ucfirst(str_replace('_', ' ', $cumplimiento->estado)) }}
                                </span>
                            </div>
                        @else
                            <form method="POST" action="{{ route('admin.cumplimientos.update', $cumplimiento) }}"
                                  class="border rounded p-2 mb-2">
                                @csrf @method('PATCH')
                                <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                    <div>
                                        <span class="fw-semibold">{{ $cumplimiento->requisitoAdopcion->nombre }}</span>
                                        @if ($cumplimiento->requisitoAdopcion->obligatorio)
                                            <span class="badge text-bg-warning">Obligatorio</span>
                                        @endif
                                        <span class="badge badge-{{ str_replace('_', '-', $cumplimiento->estado) }} ms-1">
                                            {{ ucfirst(str_replace('_', ' ', $cumplimiento->estado)) }}
                                        </span>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap justify-content-end">
                                        <select class="form-select form-select-sm" name="estado" style="width:auto;">
                                            @foreach (['pendiente', 'cumplido', 'no_cumplido', 'no_aplica'] as $opcion)
                                                <option value="{{ $opcion }}" @selected($cumplimiento->estado === $opcion)>
                                                    {{ ucfirst(str_replace('_', ' ', $opcion)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input class="form-control form-control-sm" name="observacion"
                                               value="{{ old('observacion', $cumplimiento->observacion) }}"
                                               maxlength="255" placeholder="Observacion" style="max-width: 15rem;">
                                        <button class="btn btn-sm btn-huellitas" type="submit">Guardar</button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    @empty
                        <p class="texto-secundario mb-0">Este tipo de mascota no tiene requisitos activos.</p>
                    @endforelse
                </div>
            </div>

            {{-- Adopción final --}}
            <div class="card card-huellitas">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-house-heart me-1"></i> Registrar adopción final</h2>
                    @if ($tieneAdopcion)
                        <div class="alert alert-success mb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span>
                                <i class="bi bi-patch-check-fill me-1"></i>
                                Adopción registrada · Acta {{ $solicitud->adopcion->numero_acta }}
                            </span>
                            <a class="btn btn-sm btn-outline-success" href="{{ route('admin.adopciones.show', $solicitud->adopcion) }}">
                                Ver adopción
                            </a>
                        </div>
                    @elseif ($cita->estado === 'cancelada')
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle me-1"></i>
                            La adopción fue rechazada. La solicitud se anuló y la mascota volvió al catálogo.
                        </div>
                    @else
                        @if ($cita->estado !== 'completada')
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Verifica los requisitos y marca la cita como completada antes de registrar el acta de adopcion.
                            </div>
                        @else
                        <form method="POST" action="{{ route('admin.adopciones.store', $solicitud) }}">
                            @csrf
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small">Fecha de adopción</label>
                                    <input class="form-control form-control-sm" type="date" name="fecha_adopcion"
                                           value="{{ old('fecha_adopcion', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Número de acta</label>
                                    <input class="form-control form-control-sm" type="text" name="numero_acta"
                                           value="{{ old('numero_acta') }}" maxlength="30" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Observaciones (opcional)</label>
                                    <input class="form-control form-control-sm" type="text" name="observaciones"
                                           value="{{ old('observaciones') }}" maxlength="255">
                                </div>
                            </div>
                            <button class="btn btn-aprobar w-100 mt-3" type="submit">
                                <i class="bi bi-check2-all me-1"></i> Confirmar adopción
                            </button>
                        </form>
                        @endif

                        {{-- Rechazar la adopción si no se presentan / no cumplen el día de la cita --}}
                        <div class="text-center mt-3 pt-3 border-top">
                            <p class="texto-secundario small mb-2">
                                ¿El adoptante no se presentó o no cumplió el día de la cita?
                            </p>
                            <form method="POST" action="{{ route('admin.citas.update', $cita) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="estado" value="cancelada">
                                <button class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('¿Rechazar la adopción? La solicitud se anulará y la mascota volverá al catálogo.')"
                                        type="submit">
                                    <i class="bi bi-x-circle me-1"></i> Rechazar adopción
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
