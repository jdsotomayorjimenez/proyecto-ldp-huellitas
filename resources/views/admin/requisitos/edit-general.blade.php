@extends('layouts.admin')

@section('title', 'Editar requisito general')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-huellitas">
                <div class="card-body p-4">
                    <h1 class="h3 mb-1">Editar requisito general</h1>
                    <p class="texto-secundario">
                        <i class="bi bi-info-circle me-1"></i>
                        Los cambios se aplicarán a <strong>los {{ $tiposAfectados }} tipos de mascota</strong>
                        que comparten este requisito.
                    </p>

                    <form method="POST" action="{{ route('admin.requisitos.update-general', $requisito) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label" for="nombre">Nombre</label>
                            <input class="form-control @error('nombre') is-invalid @enderror" id="nombre"
                                   name="nombre" value="{{ old('nombre', $requisito->nombre) }}"
                                   maxlength="100" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" maxlength="255"
                                      rows="3">{{ old('descripcion', $requisito->descripcion) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="estado">Estado</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="activo" @selected(old('estado', $requisito->estado) === 'activo')>Activo</option>
                                    <option value="inactivo" @selected(old('estado', $requisito->estado) === 'inactivo')>Inactivo</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <input type="hidden" name="obligatorio" value="0">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" id="obligatorio" type="checkbox"
                                           name="obligatorio" value="1"
                                           @checked((bool) old('obligatorio', $requisito->obligatorio))>
                                    <label class="form-check-label" for="obligatorio">Requisito obligatorio</label>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-huellitas" type="submit">Actualizar en todos los tipos</button>
                        <a class="btn btn-outline-secondary"
                           href="{{ route('admin.requisitos.index') }}">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
