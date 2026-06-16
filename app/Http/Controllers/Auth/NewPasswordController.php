<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['token' => $request->route('token')]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'correo'   => ['required', 'email'],
            'contrasena' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = \App\Models\User::where('correo', $request->correo)->first();

        if (!$user) {
            return back()->withErrors(['correo' => 'No encontramos un usuario con ese correo.']);
        }

        $repository = Password::getRepository();

        if (!$repository->exists($user, $request->token)) {
            return back()->withErrors(['correo' => 'El enlace de recuperación es inválido o ha expirado.']);
        }

        $user->forceFill([
            'contrasena'     => \Illuminate\Support\Facades\Hash::make($request->contrasena),
            'remember_token' => \Illuminate\Support\Str::random(60),
        ])->save();

        $repository->delete($user);

        event(new \Illuminate\Auth\Events\PasswordReset($user));

        return redirect()->route('login')->with('status', 'Tu contraseña ha sido restablecida con éxito.');
    }
}
