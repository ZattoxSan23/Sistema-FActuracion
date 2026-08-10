<?php

namespace App\Http\Middleware;

use App\Models\Caja;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CajaAbierta
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Solo aplica a cajeras y administradores
        if (!$user || $user->isContador()) {
            return $next($request);
        }

        $caja = Caja::cajaAbierta();

        // Si no hay caja abierta y la ruta requiere caja abierta
        if (!$caja && !$request->routeIs('caja.*')) {
            return redirect()->route('caja.index')
                ->with('warning', 'Debes aperturar una caja antes de realizar ventas.');
        }

        // Si la ruta es de apertura y ya hay caja abierta, redirigir al POS
        if ($caja && $request->routeIs('caja.apertura')) {
            return redirect()->route('pos.index')
                ->with('info', 'Ya tienes una caja abierta.');
        }

        return $next($request);
    }
}
