<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validamos usando tu campo 'correo'
        $request->validate([
            'correo' => ['required', 'email'],
        ]);

        // 2. Buscamos al usuario de forma manual y explícita
        $user = User::where('correo', $request->correo)->first();

        // Si no existe, devolvemos el error directo a tu input 'correo'
        if (!$user) {
            return back()->withErrors(['correo' => 'No podemos encontrar un usuario con esa dirección de correo electrónico.']);
        }

        // 3. Generamos el token usando el método nativo (ahora que el modelo sabe leer 'correo')
        $token = Password::getRepository()->create($user);

        // 4. Desparamos la notificación por email
        $user->sendPasswordResetNotification($token);

        // 5. Retornamos con éxito total
        return back()->with('status', '¡Hemos enviado por correo el enlace para restablecer tu contraseña!');
    }
}
