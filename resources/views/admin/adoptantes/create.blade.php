@extends('layouts.admin')

@section('title', 'Registrar adoptante')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="card card-huellitas">
                <div class="card-body p-4">
                    <h1 class="h3 mb-2">Registrar adoptante</h1>
                    <p class="texto-secundario">
                        Esta operación crea una cuenta Adoptante sin cerrar tu sesión administrativa.
                    </p>
                    <x-adoptante-form
                        :action="route('admin.adoptantes.store')"
                        :cancel-route="route('admin.dashboard')"
                    />
                </div>
            </div>
        </div>
    </div>
@endsection
