@props(['action', 'cancelRoute'])

<form method="POST" action="{{ $action }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="name">Nombre completo</label>
            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                   value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="email">Correo electrónico</label>
            <input class="form-control @error('email') is-invalid @enderror" id="email" type="email"
                   name="email" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="cedula">Cédula</label>
            <input class="form-control @error('cedula') is-invalid @enderror" id="cedula" name="cedula"
                   maxlength="10" value="{{ old('cedula') }}" inputmode="numeric">
            @error('cedula')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="fecha_nacimiento">Fecha de nacimiento</label>
            <input class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                   id="fecha_nacimiento" type="date" name="fecha_nacimiento"
                   value="{{ old('fecha_nacimiento') }}">
            @error('fecha_nacimiento')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="telefono">Teléfono</label>
            <input class="form-control @error('telefono') is-invalid @enderror" id="telefono"
                   name="telefono" maxlength="10" value="{{ old('telefono') }}" inputmode="numeric">
            @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="direccion">Dirección</label>
            <input class="form-control @error('direccion') is-invalid @enderror" id="direccion"
                   name="direccion" value="{{ old('direccion') }}">
            @error('direccion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="password">Contraseña</label>
            <input class="form-control @error('password') is-invalid @enderror" id="password"
                   type="password" name="password" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
            <input class="form-control" id="password_confirmation" type="password"
                   name="password_confirmation" required>
        </div>
    </div>
    <div class="mt-4">
        <button class="btn btn-huellitas" type="submit">Crear cuenta</button>
        <a class="btn btn-outline-secondary" href="{{ $cancelRoute }}">Cancelar</a>
    </div>
</form>
