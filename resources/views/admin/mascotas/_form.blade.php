@php
    $razasParaSelector = $razas->map(fn ($raza) => [
        'id' => (string) $raza->id,
        'tipoId' => (string) $raza->tipo_mascota_id,
        'nombre' => $raza->nombre,
    ])->values();
    $razaSeleccionadaId = (string) old('raza_id', $mascota->raza_id ?? '');
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="nombre">Nombre</label>
        <input class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre"
               value="{{ old('nombre', $mascota->nombre ?? '') }}" maxlength="100" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="filtro_tipo_raza">Tipo de mascota</label>
        <select class="form-select" id="filtro_tipo_raza" required>
            <option value="">Selecciona un tipo</option>
            @foreach ($tipos as $tipo)
                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="selector_raza_button">Raza</label>
        <input id="raza_id" type="hidden" name="raza_id" value="{{ $razaSeleccionadaId }}">
        <div class="searchable-select" id="selector_raza">
            <button class="form-select searchable-select-toggle @error('raza_id') is-invalid @enderror"
                    id="selector_raza_button" type="button" aria-expanded="false"
                    aria-controls="selector_raza_panel" disabled>
                <span id="selector_raza_text">Selecciona primero un tipo</span>
            </button>
            <div class="searchable-select-menu" id="selector_raza_panel" hidden>
                <div class="searchable-select-search">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input class="form-control" id="buscar_raza" type="search"
                               placeholder="Escribe para filtrar razas" autocomplete="off"
                               aria-label="Buscar raza">
                    </div>
                </div>
                <div class="searchable-select-options" id="opciones_raza" role="listbox"
                     aria-label="Razas disponibles"></div>
            </div>
            @error('raza_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @else
                <div class="invalid-feedback">Selecciona una raza.</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="fecha_nacimiento">Fecha de nacimiento</label>
        <input class="form-control" id="fecha_nacimiento" type="date" name="fecha_nacimiento"
               value="{{ old('fecha_nacimiento', isset($mascota) && $mascota->fecha_nacimiento ? $mascota->fecha_nacimiento->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="genero">Género</label>
        <select class="form-select" id="genero" name="genero" required>
            <option value="macho" @selected(old('genero', $mascota->genero ?? '') === 'macho')>Macho</option>
            <option value="hembra" @selected(old('genero', $mascota->genero ?? '') === 'hembra')>Hembra</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="tamanio">Tamaño</label>
        <select class="form-select" id="tamanio" name="tamanio" required>
            @foreach (['pequeno' => 'Pequeño', 'mediano' => 'Mediano', 'grande' => 'Grande'] as $valor => $texto)
                <option value="{{ $valor }}" @selected(old('tamanio', $mascota->tamanio ?? '') === $valor)>
                    {{ $texto }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="estado">Estado</label>
        <select class="form-select" id="estado" name="estado" required>
            @foreach ([
                'disponible' => 'Disponible',
                'en_proceso' => 'En proceso',
                'adoptada' => 'Adoptada',
                'no_disponible' => 'No disponible',
            ] as $valor => $texto)
                <option value="{{ $valor }}"
                    @selected(old('estado', $mascota->estado ?? 'disponible') === $valor)>
                    {{ $texto }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label" for="descripcion">Descripción</label>
        <textarea class="form-control" id="descripcion" name="descripcion" maxlength="255"
                  rows="3">{{ old('descripcion', $mascota->descripcion ?? '') }}</textarea>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            const selector = document.getElementById('selector_raza');
            const razaId = document.getElementById('raza_id');
            const filtroTipo = document.getElementById('filtro_tipo_raza');
            const buscarRaza = document.getElementById('buscar_raza');
            const botonRaza = document.getElementById('selector_raza_button');
            const textoRaza = document.getElementById('selector_raza_text');
            const panelRaza = document.getElementById('selector_raza_panel');
            const opcionesRaza = document.getElementById('opciones_raza');

            if (!selector || !razaId || !filtroTipo || !buscarRaza || !botonRaza
                || !textoRaza || !panelRaza || !opcionesRaza) {
                return;
            }

            const razas = @json($razasParaSelector);

            const normalizar = (texto) => texto
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim();

            const obtenerRazaSeleccionada = () => razas.find((raza) => raza.id === razaId.value);

            const mostrarPanel = (mostrar) => {
                if (mostrar && botonRaza.disabled) {
                    return;
                }

                panelRaza.hidden = !mostrar;
                botonRaza.setAttribute('aria-expanded', mostrar ? 'true' : 'false');

                if (mostrar) {
                    buscarRaza.focus();
                    buscarRaza.select();
                }
            };

            const seleccionarRaza = (raza) => {
                razaId.value = raza.id;
                textoRaza.textContent = raza.nombre;
                botonRaza.classList.remove('is-invalid');
                buscarRaza.value = '';
                mostrarPanel(false);
            };

            const renderizarOpciones = () => {
                const tipoId = filtroTipo.value;
                const busqueda = normalizar(buscarRaza.value);
                const filtradas = razas.filter((raza) => raza.tipoId === tipoId
                    && (!busqueda || normalizar(raza.nombre).includes(busqueda)));

                opcionesRaza.replaceChildren();

                if (filtradas.length === 0) {
                    const mensaje = document.createElement('div');
                    mensaje.className = 'searchable-select-empty';
                    mensaje.textContent = busqueda
                        ? 'No hay razas que coincidan con la búsqueda.'
                        : 'Este tipo todavía no tiene razas registradas.';
                    opcionesRaza.append(mensaje);

                    return;
                }

                filtradas.forEach((raza) => {
                    const opcion = document.createElement('button');
                    opcion.type = 'button';
                    opcion.className = 'searchable-select-option';
                    opcion.setAttribute('role', 'option');
                    opcion.setAttribute('aria-selected', raza.id === razaId.value ? 'true' : 'false');
                    opcion.textContent = raza.nombre;

                    if (raza.id === razaId.value) {
                        opcion.classList.add('active');
                    }

                    opcion.addEventListener('click', () => seleccionarRaza(raza));
                    opcionesRaza.append(opcion);
                });
            };

            const actualizarTipo = () => {
                const seleccionada = obtenerRazaSeleccionada();
                const tipoId = filtroTipo.value;

                if (seleccionada && seleccionada.tipoId !== tipoId) {
                    razaId.value = '';
                }

                botonRaza.disabled = !tipoId;
                textoRaza.textContent = obtenerRazaSeleccionada()?.nombre
                    ?? (tipoId ? 'Selecciona una raza' : 'Selecciona primero un tipo');
                buscarRaza.value = '';
                mostrarPanel(false);
                renderizarOpciones();
            };

            const seleccionInicial = obtenerRazaSeleccionada();

            if (seleccionInicial) {
                filtroTipo.value = seleccionInicial.tipoId;
            }

            filtroTipo.addEventListener('change', actualizarTipo);
            botonRaza.addEventListener('click', () => {
                renderizarOpciones();
                mostrarPanel(panelRaza.hidden);
            });
            buscarRaza.addEventListener('input', renderizarOpciones);
            buscarRaza.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }

                if (event.key === 'Escape') {
                    mostrarPanel(false);
                    botonRaza.focus();
                }
            });

            document.addEventListener('click', (event) => {
                if (!selector.contains(event.target)) {
                    mostrarPanel(false);
                }
            });

            razaId.closest('form')?.addEventListener('submit', (event) => {
                if (razaId.value) {
                    return;
                }

                event.preventDefault();
                botonRaza.classList.add('is-invalid');

                if (filtroTipo.value) {
                    renderizarOpciones();
                    mostrarPanel(true);
                } else {
                    filtroTipo.focus();
                }
            });

            actualizarTipo();
        })();
    </script>
@endpush
