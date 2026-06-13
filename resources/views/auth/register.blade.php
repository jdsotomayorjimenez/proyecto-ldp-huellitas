@extends('layouts.guest')

@section('title', 'Crear cuenta')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-huellitas">
                <div class="card-body p-4">
                    <h1 class="h3 mb-2">Crear cuenta de adoptante</h1>
                    <p class="texto-secundario">La cuenta se registrará con el rol Adoptante.</p>
                    <x-adoptante-form
                        :action="route('register.store')"
                        :cancel-route="route('login')"
                    />
                </div>
            </div>
        </div>
    </div>
@endsection
