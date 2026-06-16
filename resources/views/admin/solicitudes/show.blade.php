@extends('layouts.admin')

@section('title', 'Solicitud #'.$solicitud->id)

@section('content')
    @php
        $mascota = $solicitud->mascota;
        $tieneAdopcion = $solicitud->adopcion !== null;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <a class="text-decoration-none small" href="{{ route('admin.solicitudes.index') }}">
                <i class="bi bi-arrow-left"></i> Volver a solicitudes
            </a>
            <h1 class="h3 mb-0 mt-1">Solicitud para
                <span style="color: var(--color-principal-vivo);">{{ $mascota->nombre }}</span>
            </h1>
        </div>
        <span class="badge estado-badge badge-{{ str_replace('_', '-', $solicitud->estado) }}">
            {{ ucfirst($solicitud->estado) }}
        </span>
    </div>

    <div class="row g-4">
        {{-- Columna izquierda: datos --}}
        <div class="col-lg-5">
            <div class="card card-huellitas mb-4">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-person me-1"></i> Adoptante</h2>
                    <dl class="row mb-0 small">
                        <dt class="col-5 texto-secundario">Nombre</dt>
                        <dd class="col-7">{{ $solicitud->usuario->name }}</dd>
                        <dt class="col-5 texto-secundario">Correo</dt>
                        <dd class="col-7">{{ $solicitud->usuario->email }}</dd>
                        <dt class="col-5 texto-secundario">Cédula</dt>
                        <dd class="col-7">{{ $solicitud->usuario->cedula ?? '—' }}</dd>
                        <dt class="col-5 texto-secundario">Teléfono</dt>
                        <dd class="col-7">{{ $solicitud->usuario->telefono ?? '—' }}</dd>
                        <dt class="col-5 texto-secundario">Dirección</dt>
                        <dd class="col-7">{{ $solicitud->usuario->direccion ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card card-huellitas mb-4">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-heart me-1"></i> Mascota</h2>
                    <dl class="row mb-0 small">
                        <dt class="col-5 texto-secundario">Nombre</dt>
                        <dd class="col-7">{{ $mascota->nombre }}</dd>
                        <dt class="col-5 texto-secundario">Tipo / raza</dt>
                        <dd class="col-7">{{ $mascota->raza->tipoMascota->nombre }} / {{ $mascota->raza->nombre }}</dd>
                        <dt class="col-5 texto-secundario">Estado actual</dt>
                        <dd class="col-7">
                            <span class="badge badge-{{ str_replace('_', '-', $mascota->estado) }}">
                                {{ ucfirst(str_replace('_', ' ', $mascota->estado)) }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card card-huellitas">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-file-text me-1"></i> Datos de la solicitud</h2>
                    <p class="mb-2"><span class="texto-secundario small d-block">Motivo</span>{{ $solicitud->motivo }}</p>
                    <p class="mb-2"><span class="texto-secundario small d-block">Experiencia</span>{{ $solicitud->experiencia ?? '—' }}</p>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <span class="pet-chip"><i class="bi bi-house"></i>{{ ucfirst($solicitud->tipo_vivienda) }}</span>
                        <span class="pet-chip"><i class="bi bi-{{ $solicitud->vivienda_propia ? 'check' : 'x' }}-circle"></i>Vivienda propia</span>
                        <span class="pet-chip"><i class="bi bi-{{ $solicitud->tiene_mascotas ? 'check' : 'x' }}-circle"></i>Otras mascotas</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna derecha: acciones según estado --}}
        <div class="col-lg-7 d-flex flex-column">
            @if ($solicitud->estado === 'pendiente')
                <div class="alert alert-warning d-flex align-items-center gap-2 fs-6 mb-4" role="alert">
                    <i class="bi bi-hourglass-split fs-5"></i>
                    <span>Solicitud <strong>en revisión</strong>: aún no se ha tomado una decisión.
                        Apruébala y agenda la cita, o recházala indicando el motivo.</span>
                </div>
                <div class="row g-4 flex-grow-1">
                    <div class="col-md-6">
                        <div class="card card-huellitas h-100 border-success">
                            <div class="card-body d-flex flex-column">
                                <h2 class="h6 fw-bold text-success mb-3"><i class="bi bi-check-circle"></i> Aprobar y agendar cita</h2>
                                <form method="POST" action="{{ route('admin.solicitudes.responder', $solicitud) }}"
                                      class="d-flex flex-column flex-grow-1">
                                    @csrf
                                    <input type="hidden" name="resultado" value="aprobada">
                                    <div class="mb-2">
                                        <label class="form-label small">Mensaje para el adoptante</label>
                                        <textarea class="form-control form-control-sm" name="respuesta" rows="2"
                                                  maxlength="800" required>{{ old('respuesta') }}</textarea>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">
                                            Requisitos a revisar en la cita
                                        </label>
                                        @forelse ($requisitos as $req)
                                            <div class="small d-flex align-items-start gap-2 mb-1">
                                                <i class="bi bi-clipboard-check text-success"></i>
                                                <span>
                                                    {{ $req->nombre }}
                                                    @if ($req->obligatorio)
                                                        <span class="badge text-bg-warning">Obligatorio</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @empty
                                            <p class="texto-secundario small mb-0">Este tipo de mascota no tiene requisitos activos.</p>
                                        @endforelse
                                        <p class="texto-secundario small mt-2 mb-0">
                                            Aprobar esta solicitud solo agenda la cita; no registra la adopcion.
                                        </p>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small">Fecha</label>
                                            <input class="form-control form-control-sm" type="date" name="fecha"
                                                   value="{{ old('fecha') }}" min="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">Hora</label>
                                            <input class="form-control form-control-sm" type="time" name="hora"
                                                   value="{{ old('hora') }}" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small">Lugar</label>
                                            <input class="form-control form-control-sm" type="text" name="lugar"
                                                   value="{{ old('lugar', 'Refugio Huellitas, Av. de las Américas, Guayaquil') }}" maxlength="150" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small">Indicaciones (opcional)</label>
                                            <input class="form-control form-control-sm" type="text" name="indicaciones"
                                                   value="{{ old('indicaciones') }}" maxlength="255">
                                        </div>
                                    </div>
                                    <button class="btn btn-aprobar w-100 mt-3" type="submit">Aprobar solicitud</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-huellitas h-100 border-danger">
                            <div class="card-body d-flex flex-column">
                                <h2 class="h6 fw-bold text-danger mb-3"><i class="bi bi-x-circle"></i> Rechazar solicitud</h2>
                                <form method="POST" action="{{ route('admin.solicitudes.responder', $solicitud) }}"
                                      class="d-flex flex-column flex-grow-1">
                                    @csrf
                                    <input type="hidden" name="resultado" value="rechazada">
                                    <div class="mb-3 d-flex flex-column flex-grow-1">
                                        <label class="form-label small">Motivo del rechazo</label>
                                        <textarea class="form-control form-control-sm flex-grow-1" name="respuesta"
                                                  style="min-height: 8rem;" maxlength="800" required></textarea>
                                    </div>
                                    <button class="btn btn-rechazar w-100 mt-auto"
                                            onclick="return confirm('¿Rechazar esta solicitud? La mascota volverá a estar disponible.')"
                                            type="submit">Rechazar solicitud</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($solicitud->estado === 'aprobada')
                @if ($solicitud->respuesta)
                    <div class="alert alert-success">
                        <span class="text-uppercase small fw-semibold d-block">Solicitud aprobada</span>
                        <span class="fs-6">{{ $solicitud->respuesta->respuesta }}</span>
                    </div>
                @endif

                @if ($solicitud->cita)
                    {{-- La cita se agenda al aprobar: la gestión continúa en su página --}}
                    <div class="card card-huellitas flex-grow-1 border-success" style="background-color: rgba(25,135,84,0.06);">
                        <div class="card-body d-flex flex-column justify-content-center text-center p-4">
                            <div style="font-size: 3rem; line-height: 1;" class="mb-2">🎉</div>
                            <h2 class="h4 text-success fw-bold mb-2">¡Solicitud aprobada y cita agendada!</h2>
                            <p class="texto-secundario fs-6 mb-4 mx-auto" style="max-width: 32rem;">
                                La preparación (cita y requisitos) y el registro de la adopción se gestionan
                                en la página de la cita.
                            </p>
                            <div class="d-grid gap-3 mx-auto w-100" style="max-width: 30rem;">
                                <a class="btn btn-huellitas btn-lg py-3 fw-semibold" href="{{ route('admin.citas.show', $solicitud->cita) }}">
                                    <i class="bi bi-calendar-event me-2"></i> Ver cita y requisitos
                                </a>
                                @if ($tieneAdopcion)
                                    <a class="btn btn-outline-success btn-lg py-3 fw-semibold" href="{{ route('admin.adopciones.show', $solicitud->adopcion) }}">
                                        <i class="bi bi-house-heart me-2"></i> Ver adopción registrada
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning flex-grow-1 mb-0">
                        Esta solicitud está aprobada pero no tiene una cita agendada.
                    </div>
                @endif
            @else
                {{-- Rechazada (la rechazó el refugio) o cancelada (la canceló el adoptante) --}}
                @php
                    $fueRechazada = $solicitud->estado === 'rechazada';
                @endphp
                <div class="card card-huellitas flex-grow-1 border-secondary" style="background-color: rgba(108,117,125,0.06);">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                        <div style="font-size: 3rem; line-height: 1;" class="mb-2">{{ $fueRechazada ? '🚫' : '⚪' }}</div>
                        <h2 class="h4 {{ $fueRechazada ? 'text-danger' : 'text-secondary' }} fw-bold mb-2">
                            {{ $fueRechazada ? 'Esta solicitud no se aceptó' : 'Solicitud cancelada por el adoptante' }}
                        </h2>
                        <span class="texto-secundario text-uppercase small fw-semibold d-block mb-1">
                            {{ $fueRechazada ? 'Motivo del rechazo' : 'Estado de la solicitud' }}
                        </span>
                        @if ($fueRechazada && $solicitud->respuesta)
                            <p class="mb-2 mx-auto fs-5 lh-base" style="max-width: 34rem;">
                                {{ $solicitud->respuesta->respuesta }}
                            </p>
                            <p class="texto-secundario small mb-4">
                                Respondida el {{ $solicitud->respuesta->fecha_respuesta?->format('d/m/Y') }}
                            </p>
                        @else
                            <p class="mb-4 mx-auto fs-5 lh-base" style="max-width: 34rem;">
                                El adoptante decidió cancelar la solicitud antes de completar el proceso,
                                por lo que la adopción no continuó. No se registró ninguna observación adicional.
                            </p>
                        @endif
                        <p class="texto-secundario mb-0 mx-auto" style="max-width: 30rem;">
                            <i class="bi bi-heart me-1"></i>
                            {{ $mascota->nombre }} volvió a estar disponible para que otra familia pueda adoptarla.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
