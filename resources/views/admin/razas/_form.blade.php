<div class="mb-3">
    <label class="form-label" for="tipo_mascota_id">Tipo de mascota</label>
    <select class="form-select @error('tipo_mascota_id') is-invalid @enderror" id="tipo_mascota_id"
            name="tipo_mascota_id" required>
        <option value="">Selecciona un tipo</option>
        @foreach ($tipos as $tipo)
            <option value="{{ $tipo->id }}"
                @selected((int) old('tipo_mascota_id', $raza->tipo_mascota_id ?? 0) === $tipo->id)>
                {{ $tipo->nombre }}
            </option>
        @endforeach
    </select>
    @error('tipo_mascota_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="nombre">Nombre</label>
    <input class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre"
           value="{{ old('nombre', $raza->nombre ?? '') }}" maxlength="80" required>
    @error('nombre')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="descripcion">Descripción</label>
    <textarea class="form-control" id="descripcion" name="descripcion" maxlength="255"
              rows="3">{{ old('descripcion', $raza->descripcion ?? '') }}</textarea>
</div>
