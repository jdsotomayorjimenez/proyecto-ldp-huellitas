@php
    $esEdicion = isset($requisito);
    $alcance = old('alcance', 'tipo');
@endphp

@unless ($esEdicion)
    <div class="mb-3">
        <label class="form-label d-block">Alcance</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" id="alcance_tipo" type="radio" name="alcance"
                   value="tipo" @checked($alcance === 'tipo')>
            <label class="form-check-label" for="alcance_tipo">Un tipo de mascota</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" id="alcance_general" type="radio" name="alcance"
                   value="general" @checked($alcance === 'general')>
            <label class="form-check-label" for="alcance_general">Todos los tipos</label>
        </div>
        <div class="form-text">
            El modo general crea una fila por tipo para mantener el modelo relacional obligatorio.
        </div>
    </div>
@endunless

<div class="mb-3" id="contenedor_tipo">
    <label class="form-label" for="tipo_mascota_id">Tipo de mascota</label>
    <select class="form-select @error('tipo_mascota_id') is-invalid @enderror" id="tipo_mascota_id"
            name="tipo_mascota_id">
        <option value="">Selecciona un tipo</option>
        @foreach ($tipos as $tipo)
            <option value="{{ $tipo->id }}"
                @selected((int) old('tipo_mascota_id', $requisito->tipo_mascota_id ?? 0) === $tipo->id)>
                {{ $tipo->nombre }}
            </option>
        @endforeach
    </select>
    @error('tipo_mascota_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@unless ($esEdicion)
    <div class="mb-3 d-none" id="contenedor_general">
        <label class="form-label">Obligatoriedad por tipo</label>
        <div class="card bg-light border-0">
            <div class="card-body">
                <p class="small texto-secundario">
                    Todos recibirán el requisito. Desmarca “Obligatorio” cuando solo sea recomendado,
                    por ejemplo para Conejo.
                </p>
                <div class="row g-2">
                    @foreach ($tipos as $tipo)
                        <input type="hidden" name="tipos[]" value="{{ $tipo->id }}">
                        <div class="col-md-6">
                            <div class="form-check border rounded bg-white p-3 ps-5">
                                <input type="hidden" name="obligatorios[{{ $tipo->id }}]" value="0">
                                <input class="form-check-input" id="obligatorio_{{ $tipo->id }}"
                                       type="checkbox" name="obligatorios[{{ $tipo->id }}]" value="1"
                                       @checked((bool) old("obligatorios.{$tipo->id}", true))>
                                <label class="form-check-label" for="obligatorio_{{ $tipo->id }}">
                                    <span class="fw-semibold">{{ $tipo->nombre }}</span>
                                    <span class="d-block small texto-secundario">Obligatorio</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @error('tipos')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
@endunless

<div class="mb-3">
    <label class="form-label" for="nombre">Nombre</label>
    <input class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre"
           value="{{ old('nombre', $requisito->nombre ?? '') }}" maxlength="100" required>
    @error('nombre')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="descripcion">Descripción</label>
    <textarea class="form-control" id="descripcion" name="descripcion" maxlength="255"
              rows="3">{{ old('descripcion', $requisito->descripcion ?? '') }}</textarea>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="estado">Estado</label>
        <select class="form-select" id="estado" name="estado" required>
            <option value="activo" @selected(old('estado', $requisito->estado ?? 'activo') === 'activo')>Activo</option>
            <option value="inactivo" @selected(old('estado', $requisito->estado ?? '') === 'inactivo')>Inactivo</option>
        </select>
    </div>
    <div class="col-md-6 mb-3 d-flex align-items-end" id="contenedor_obligatorio">
        <input type="hidden" name="obligatorio" value="0">
        <div class="form-check mb-2">
            <input class="form-check-input" id="obligatorio" type="checkbox" name="obligatorio" value="1"
                   @checked((bool) old('obligatorio', $requisito->obligatorio ?? true))>
            <label class="form-check-label" for="obligatorio">Requisito obligatorio</label>
        </div>
    </div>
</div>

@unless ($esEdicion)
    @push('scripts')
        <script>
            (() => {
                const alcanceTipo = document.getElementById('alcance_tipo');
                const alcanceGeneral = document.getElementById('alcance_general');
                const contenedorTipo = document.getElementById('contenedor_tipo');
                const contenedorGeneral = document.getElementById('contenedor_general');
                const contenedorObligatorio = document.getElementById('contenedor_obligatorio');
                const tipoMascota = document.getElementById('tipo_mascota_id');

                const actualizarAlcance = () => {
                    const esGeneral = alcanceGeneral.checked;
                    contenedorTipo.classList.toggle('d-none', esGeneral);
                    contenedorGeneral.classList.toggle('d-none', !esGeneral);
                    contenedorObligatorio.classList.toggle('d-none', esGeneral);
                    tipoMascota.required = !esGeneral;
                };

                alcanceTipo.addEventListener('change', actualizarAlcance);
                alcanceGeneral.addEventListener('change', actualizarAlcance);
                actualizarAlcance();
            })();
        </script>
    @endpush
@endunless
