<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

/**
 * Versión API (JSON) del flujo de "olvidé mi contraseña" que ya usa la
 * web (App\Http\Controllers\Auth\PasswordResetLinkController). Replica
 * exactamente la misma lógica —mismo campo 'correo', mismo mecanismo de
 * token de Laravel— para no depender de configuraciones distintas entre
 * web y móvil. El usuario recibe el mismo correo con el mismo enlace de
 * restablecimiento que ya funciona en la web; la app solo dispara el
 * envío, no reimplementa el cambio de contraseña en sí.
 */
class PasswordResetApiController extends Controller
{
    public function enviarEnlace(Request $request)
    {
        $request->validate([
            'correo' => ['required', 'email'],
        ]);

        $user = User::where('correo', $request->correo)->first();

        // Por seguridad no confirmamos si el correo existe o no en el
        // mensaje (evita que alguien use este endpoint para averiguar
        // qué correos están registrados), pero seguimos devolviendo
        // success: true en ambos casos con el mismo mensaje genérico.
        if ($user) {
            $token = Password::getRepository()->create($user);
            $user->sendPasswordResetNotification($token);
        }

        return response()->json([
            'success' => true,
            'message' => 'Si el correo está registrado, te enviamos un enlace para restablecer tu contraseña.',
        ]);
    }
}