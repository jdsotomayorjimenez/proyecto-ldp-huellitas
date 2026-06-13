@extends('layouts.admin')

@section('title', 'Nuevo requisito')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-huellitas">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Nuevo requisito de adopción</h1>
                    <form method="POST" action="{{ route('admin.requisitos.store') }}">
                        @csrf
                        @include('admin.requisitos._form')
                        <button class="btn btn-huellitas" type="submit">Guardar</button>
                        <a class="btn btn-outline-secondary"
                           href="{{ route('admin.requisitos.index') }}">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
