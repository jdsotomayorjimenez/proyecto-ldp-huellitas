<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdoptanteRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credenciales, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Las credenciales ingresadas no son correctas.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return $request->user()->esAdministrador()
            ? redirect()->intended(route('admin.dashboard'))
            : redirect()->intended(route('home'));
    }

    public function showRegister(Request $request): View|RedirectResponse
    {
        if ($request->user()?->esAdministrador()) {
            return redirect()->route('admin.adoptantes.create');
        }

        if ($request->user()) {
            return redirect()
                ->route('home')
                ->with('error', 'Cierra sesión para registrar una cuenta diferente.');
        }

        return view('auth.register');
    }

    public function register(StoreAdoptanteRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['role_id'] = Role::where('nombre', 'Adoptante')->firstOrFail()->id;
        $usuario = User::create($datos);

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('success', 'Tu cuenta fue creada correctamente.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
