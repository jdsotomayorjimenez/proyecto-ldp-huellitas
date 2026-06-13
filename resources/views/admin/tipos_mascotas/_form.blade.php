<div class="mb-3">
    <label class="form-label" for="nombre">Nombre</label>
    <input class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre"
           value="{{ old('nombre', $tipo->nombre ?? '') }}" maxlength="50" required>
    @error('nombre')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="descripcion">Descripción</label>
    <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion"
              name="descripcion" maxlength="255" rows="3">{{ old('descripcion', $tipo->descripcion ?? '') }}</textarea>
    @error('descripcion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
