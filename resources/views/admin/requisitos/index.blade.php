@extends('layouts.admin')

@section('title', 'Requisitos de adopción')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Requisitos de adopción</h1>
            <p class="texto-secundario mb-0">Condiciones requeridas para cada tipo de mascota.</p>
        </div>
        <a class="btn btn-huellitas" href="{{ route('admin.requisitos.create') }}">Nuevo requisito</a>
    </div>

    <form class="card card-huellitas mb-3" method="GET" action="{{ route('admin.requisitos.index') }}">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" for="buscar">Buscar</label>
                    <input class="form-control" id="buscar" type="search" name="buscar"
                           value="{{ request('buscar') }}" placeholder="Nombre o descripción">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="filtro_tipo">Tipo de mascota</label>
                    <select class="form-select" id="filtro_tipo" name="tipo_mascota_id">
                        <option value="">Todos los tipos</option>
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id }}"
                                @selected((int) request('tipo_mascota_id') === $tipo->id)>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="filtro_estado">Estado</label>
                    <select class="form-select" id="filtro_estado" name="estado">
                        <option value="">Todos</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activo</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-huellitas flex-grow-1" type="submit">Filtrar</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.requisitos.index') }}">Limpiar</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card card-huellitas">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Requisito</th>
                        <th>Tipo</th>
                        <th>Obligatorio</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requisitos as $requisito)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $requisito->nombre }}</div>
                                <small class="texto-secundario">{{ $requisito->descripcion }}</small>
                            </td>
                            <td>{{ $requisito->tipoMascota->nombre }}</td>
                            <td>{{ $requisito->obligatorio ? 'Sí' : 'No' }}</td>
                            <td>
                                <span class="badge badge-{{ $requisito->estado }}">{{ ucfirst($requisito->estado) }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('admin.requisitos.edit', $requisito) }}">Editar</a>
                                <form class="d-inline" method="POST"
                                      action="{{ route('admin.requisitos.destroy', $requisito) }}"
                                      onsubmit="return confirm('¿Eliminar este requisito?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-center py-4" colspan="5">No hay requisitos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($requisitos->hasPages())
            <div class="card-footer">{{ $requisitos->links() }}</div>
        @endif
    </div>
@endsection
