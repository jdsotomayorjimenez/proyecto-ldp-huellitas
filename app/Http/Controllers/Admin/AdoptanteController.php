<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdoptanteRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdoptanteController extends Controller
{
    public function create(): View
    {
        return view('admin.adoptantes.create');
    }

    public function store(StoreAdoptanteRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['role_id'] = Role::where('nombre', 'Adoptante')->firstOrFail()->id;

        User::create($datos);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Cuenta de adoptante creada correctamente.');
    }
}
