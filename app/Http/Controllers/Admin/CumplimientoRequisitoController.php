<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CumplimientoRequisito;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CumplimientoRequisitoController extends Controller
{
    public function update(Request $request, CumplimientoRequisito $cumplimiento): RedirectResponse
    {
        $datos = $request->validate([
            'estado' => ['required', 'in:pendiente,cumplido,no_cumplido,no_aplica'],
            'observacion' => ['nullable', 'string', 'max:255'],
        ]);

        $cumplimiento->update([
            'estado' => $datos['estado'],
            'observacion' => $datos['observacion'] ?? null,
            'fecha_revision' => now(),
        ]);

        return back()->with('success', 'Requisito actualizado.');
    }
}
