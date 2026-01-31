<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        //Verificamos si el ususario esta logeado
        if (!Auth::check()) {
            return redirect('login');
        }
        //comparamos el id_rol con el rol que pide la rura
        //convertimos en entero para estar seguros
        if ((int) Auth::user()->id_rol !== (int) $role) {
            //Si el rol no es correcto lo manadamso al dashboard de usuarios
            return redirect('/dashboard')->with('error','No tienes permisos de administador');
        }
        return $next($request);
    }
}
