@extends('layouts.public')

@section('title', 'Solicitar adopción de '.$mascota->nombre)

@section('content')
    <section class="container" style="max-width: 760px;">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('mascotas.catalogo') }}">Catálogo</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('mascotas.show.public', $mascota) }}">{{ $mascota->nombre }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Solicitar</li>
            </ol>
        </nav>

        <div class="text-center mb-4">
            <p class="section-eyebrow mb-1">Casi listo</p>
            <h1 class="section-title">Solicitud de adopción</h1>
            <p class="texto-secundario mb-0">
                Estás solicitando adoptar a <strong>{{ $mascota->nombre }}</strong>
                ({{ $mascota->raza->tipoMascota->nombre }} · {{ $mascota->raza->nombre }}).
            </p>
        </div>

        <form method="POST" action="{{ route('solicitudes.store', $mascota) }}" class="card-huellitas p-4 p-lg-5">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold" for="motivo">¿Por qué quieres adoptarla? *</label>
                <textarea class="form-control @error('motivo') is-invalid @enderror" id="motivo" name="motivo"
                          rows="3" maxlength="255" required>{{ old('motivo') }}</textarea>
                @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" for="experiencia">
                    Tu experiencia con mascotas <span class="texto-secundario">(opcional)</span>
                </label>
                <textarea class="form-control @error('experiencia') is-invalid @enderror" id="experiencia"
                          name="experiencia" rows="2" maxlength="255">{{ old('experiencia') }}</textarea>
                @error('experiencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" for="tipo_vivienda">Tipo de vivienda *</label>
                <select class="form-select @error('tipo_vivienda') is-invalid @enderror" id="tipo_vivienda"
                        name="tipo_vivienda" required>
                    <option value="" disabled @selected(! old('tipo_vivienda'))>Selecciona...</option>
                    @foreach (['casa' => 'Casa', 'departamento' => 'Departamento', 'finca' => 'Finca', 'otro' => 'Otro'] as $valor => $label)
                        <option value="{{ $valor }}" @selected(old('tipo_vivienda') === $valor)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('tipo_vivienda') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="vivienda_propia"
                               name="vivienda_propia" value="1" @checked(old('vivienda_propia'))>
                        <label class="form-check-label" for="vivienda_propia">Vivienda propia</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="tiene_mascotas"
                               name="tiene_mascotas" value="1" @checked(old('tiene_mascotas'))>
                        <label class="form-check-label" for="tiene_mascotas">Ya tengo otras mascotas</label>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button class="btn-cta btn-lg" type="submit">
                    <i class="bi bi-send me-1"></i> Enviar solicitud
                </button>
                <a class="btn-cta-ghost btn-lg" href="{{ route('mascotas.show.public', $mascota) }}">Cancelar</a>
            </div>
        </form>
    </section>
@endsection
