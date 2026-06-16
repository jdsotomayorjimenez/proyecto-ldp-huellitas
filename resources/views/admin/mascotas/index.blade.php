@extends('layouts.admin')

@section('title', 'Mascotas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Mascotas</h1>
            <p class="texto-secundario mb-0">Registro base de mascotas e imágenes.</p>
        </div>
        <a class="btn btn-huellitas" href="{{ route('admin.mascotas.create') }}">Nueva mascota</a>
    </div>

    <div class="card card-huellitas mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.mascotas.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4 col-xl-3">
                        <label class="form-label small fw-semibold mb-1" for="buscar">
                            <i class="bi bi-search me-1"></i>Buscar
                        </label>
                        <input class="form-control" type="search" id="buscar" name="buscar"
                               value="{{ request('buscar') }}" placeholder="Nombre de la mascota">
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <label class="form-label small fw-semibold mb-1" for="tipo_mascota_id">Tipo</label>
                        <select class="form-select" id="tipo_mascota_id" name="tipo_mascota_id">
                            <option value="">Todos</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->id }}" @selected(request('tipo_mascota_id') == $tipo->id)>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <label class="form-label small fw-semibold mb-1" for="raza_id">Raza</label>
                        <select class="form-select" id="raza_id" name="raza_id">
                            <option value="">Todas</option>
                            @foreach ($razas as $raza)
                                <option value="{{ $raza->id }}" data-tipo="{{ $raza->tipo_mascota_id }}"
                                    @selected(request('raza_id') == $raza->id)>
                                    {{ $raza->tipoMascota->nombre }} · {{ $raza->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <label class="form-label small fw-semibold mb-1" for="estado">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="">Todos</option>
                            @foreach ([
                                'disponible' => 'Disponible',
                                'en_proceso' => 'En proceso',
                                'adoptada' => 'Adoptada',
                                'no_disponible' => 'No disponible',
                            ] as $valor => $texto)
                                <option value="{{ $valor }}" @selected(request('estado') === $valor)>{{ $texto }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-xl-1">
                        <label class="form-label small fw-semibold mb-1" for="genero">Género</label>
                        <select class="form-select" id="genero" name="genero">
                            <option value="">Todos</option>
                            <option value="macho" @selected(request('genero') === 'macho')>Macho</option>
                            <option value="hembra" @selected(request('genero') === 'hembra')>Hembra</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <label class="form-label small fw-semibold mb-1" for="tamanio">Tamaño</label>
                        <select class="form-select" id="tamanio" name="tamanio">
                            <option value="">Todos</option>
                            <option value="pequeno" @selected(request('tamanio') === 'pequeno')>Pequeño</option>
                            <option value="mediano" @selected(request('tamanio') === 'mediano')>Mediano</option>
                            <option value="grande" @selected(request('tamanio') === 'grande')>Grande</option>
                        </select>
                    </div>
                    <div class="col-12 col-xl-12 d-flex gap-2 mt-2">
                        <button class="btn btn-huellitas" type="submit">
                            <i class="bi bi-funnel me-1"></i>Filtrar
                        </button>
                        <a class="btn btn-outline-cafe" href="{{ route('admin.mascotas.index') }}">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-huellitas">
        <div class="table-responsive">
            <table class="table table-hover table-divided align-middle mb-0">
                <thead>
                    <tr>
                        <th>Mascota</th>
                        <th>Tipo / raza</th>
                        <th>Edad</th>
                        <th>Género</th>
                        <th>Tamaño</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mascotas as $mascota)
                        @php
                            $imagen = $mascota->imagenes->firstWhere('es_principal', true)
                                ?? $mascota->imagenes->first();
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if ($imagen)
                                        <img class="pet-thumbnail" src="{{ asset($imagen->ruta) }}"
                                             alt="Foto de {{ $mascota->nombre }}">
                                    @else
                                        <div class="pet-thumbnail pet-thumbnail-empty">Sin foto</div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $mascota->nombre }}</div>
                                        <small class="texto-secundario">{{ $mascota->descripcion }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $mascota->raza->tipoMascota->nombre }} / {{ $mascota->raza->nombre }}</td>
                            <td>{{ $mascota->edad_legible }}</td>
                            <td>{{ ucfirst($mascota->genero) }}</td>
                            <td>{{ ucfirst(str_replace('pequeno', 'pequeño', $mascota->tamanio)) }}</td>
                            <td>
                                <span class="badge badge-{{ str_replace('_', '-', $mascota->estado) }}">
                                    {{ ucfirst(str_replace('_', ' ', $mascota->estado)) }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-cafe"
                                   href="{{ route('admin.mascotas.edit', $mascota) }}">Editar</a>
                                <form class="d-inline" method="POST"
                                      action="{{ route('admin.mascotas.destroy', $mascota) }}"
                                      onsubmit="return confirm('¿Eliminar esta mascota?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-terracota" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-4" colspan="7">
                                @if (request()->hasAny(['buscar', 'tipo_mascota_id', 'raza_id', 'estado', 'genero', 'tamanio']))
                                    No hay mascotas que coincidan con los filtros.
                                @else
                                    No hay mascotas registradas.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($mascotas->hasPages())
            <div class="card-footer">{{ $mascotas->links() }}</div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const tipo = document.getElementById('tipo_mascota_id');
            const raza = document.getElementById('raza_id');

            if (!tipo || !raza) {
                return;
            }

            const acotarRazas = () => {
                const tipoId = tipo.value;
                let reiniciar = false;

                Array.from(raza.options).forEach((opcion) => {
                    if (!opcion.value) {
                        opcion.hidden = false; // "Todas" siempre visible

                        return;
                    }

                    const coincide = !tipoId || opcion.dataset.tipo === tipoId;
                    opcion.hidden = !coincide;

                    if (!coincide && opcion.selected) {
                        reiniciar = true;
                    }
                });

                if (reiniciar) {
                    raza.value = '';
                }
            };

            tipo.addEventListener('change', acotarRazas);
            acotarRazas();
        })();
    </script>
@endpush
