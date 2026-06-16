@extends('layouts.admin')

@section('title', 'Requisitos de adopción')

@section('content')
    <div class="admin-page-heading mb-4">
        <div>
            <p class="admin-eyebrow mb-1">Proceso de adopción</p>
            <h1 class="h3 mb-1">Requisitos de adopción</h1>
            <p class="texto-secundario mb-0">
                Por defecto se muestran los requisitos generales. Usa el filtro para ver los de un tipo concreto.
            </p>
        </div>
    </div>

    <div class="card admin-panel-card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="admin-card-label">{{ $esGeneral ? 'Vista actual' : 'Tipo seleccionado' }}</span>
                <h2 class="h4 mb-0">{{ $esGeneral ? 'Requisitos generales' : $tipoSeleccionado->nombre }}</h2>
                <small class="texto-secundario">
                    {{ $esGeneral
                        ? 'Requisitos que aplican a todos los tipos de mascota.'
                        : 'Requisitos específicos del tipo seleccionado.' }}
                </small>
            </div>
            <a class="btn btn-huellitas"
               href="{{ route('admin.requisitos.create', array_filter(['tipo_mascota_id' => $tipoSeleccionado?->id])) }}">
                <i class="bi bi-plus-lg me-1"></i>Añadir requisito
            </a>
        </div>

        <form class="admin-inline-filters" method="GET" action="{{ route('admin.requisitos.index') }}">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1">
                        <i class="bi bi-funnel me-1"></i>Filtrar por tipo de mascota
                    </label>
                    <select class="form-select" name="tipo_mascota_id" onchange="this.form.submit()">
                        <option value="">Generales (todos los tipos)</option>
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id }}" @selected($tipoSeleccionado?->id === $tipo->id)>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1" for="comboBuscarInput">Buscar</label>
                    <div class="searchable-select" id="comboBuscar">
                        <input class="form-control" type="text" name="buscar" id="comboBuscarInput"
                               value="{{ request('buscar') }}" placeholder="Elige o escribe un requisito"
                               autocomplete="off" role="combobox" aria-expanded="false"
                               aria-controls="comboBuscarPanel">
                        <div class="searchable-select-menu" id="comboBuscarPanel" hidden>
                            <div class="searchable-select-options" id="comboBuscarOptions" role="listbox"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="">Todos</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activo</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2 align-items-end">
                    <button class="btn btn-huellitas flex-grow-1" type="submit">Filtrar</button>
                    <a class="btn btn-outline-cafe" href="{{ route('admin.requisitos.index') }}">Limpiar</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-divided align-middle mb-0">
                <thead>
                    <tr>
                        <th>Requisito</th>
                        @unless ($esGeneral)
                            <th>Tipo</th>
                        @endunless
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
                            @unless ($esGeneral)
                                <td>{{ $requisito->tipoMascota?->nombre }}</td>
                            @endunless
                            <td>{{ $requisito->obligatorio ? 'Sí' : 'No' }}</td>
                            <td>
                                <span class="badge badge-{{ $requisito->estado }}">
                                    {{ ucfirst($requisito->estado) }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                @if ($esGeneral)
                                    <a class="btn btn-sm btn-outline-cafe"
                                       href="{{ route('admin.requisitos.editar-general', $requisito) }}">Editar</a>
                                    <form class="d-inline" method="POST"
                                          action="{{ route('admin.requisitos.destroy-general', $requisito) }}"
                                          onsubmit="return confirm('Este requisito se eliminará de TODOS los tipos. ¿Continuar?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-terracota" type="submit">
                                            Eliminar
                                        </button>
                                    </form>
                                @else
                                    <a class="btn btn-sm btn-outline-cafe"
                                       href="{{ route('admin.requisitos.edit', $requisito) }}">Editar</a>
                                    <form class="d-inline" method="POST"
                                          action="{{ route('admin.requisitos.destroy', $requisito) }}"
                                          onsubmit="return confirm('¿Eliminar este requisito?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-terracota" type="submit">
                                            Eliminar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-5" colspan="{{ $esGeneral ? 4 : 5 }}">
                                {{ $esGeneral
                                    ? 'Todavía no hay requisitos generales (presentes en todos los tipos).'
                                    : 'No hay requisitos para este tipo.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const contenedor = document.getElementById('comboBuscar');
            const input = document.getElementById('comboBuscarInput');
            const panel = document.getElementById('comboBuscarPanel');
            const opciones = document.getElementById('comboBuscarOptions');

            if (!contenedor || !input || !panel || !opciones) {
                return;
            }

            const nombres = @json($nombresRequisitos);

            const normalizar = (texto) => texto
                .normalize('NFD')
                .replace(/[̀-ͯ]/g, '')
                .toLowerCase()
                .trim();

            const mostrarPanel = (mostrar) => {
                panel.hidden = !mostrar;
                input.setAttribute('aria-expanded', mostrar ? 'true' : 'false');
            };

            const renderizar = () => {
                const busqueda = normalizar(input.value);
                const filtradas = nombres.filter((nombre) => !busqueda || normalizar(nombre).includes(busqueda));

                opciones.replaceChildren();

                if (filtradas.length === 0) {
                    const vacio = document.createElement('div');
                    vacio.className = 'searchable-select-empty';
                    vacio.textContent = 'Sin coincidencias. Pulsa Enter para buscar igualmente.';
                    opciones.append(vacio);

                    return;
                }

                filtradas.forEach((nombre) => {
                    const opcion = document.createElement('button');
                    opcion.type = 'button';
                    opcion.className = 'searchable-select-option';
                    opcion.setAttribute('role', 'option');
                    opcion.textContent = nombre;
                    opcion.addEventListener('click', () => {
                        input.value = nombre;
                        mostrarPanel(false);
                        input.form.submit();
                    });
                    opciones.append(opcion);
                });
            };

            input.addEventListener('focus', () => { renderizar(); mostrarPanel(true); });
            input.addEventListener('input', () => { renderizar(); mostrarPanel(true); });
            input.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    mostrarPanel(false);
                }
            });

            document.addEventListener('click', (event) => {
                if (!contenedor.contains(event.target)) {
                    mostrarPanel(false);
                }
            });
        })();
    </script>
@endpush
