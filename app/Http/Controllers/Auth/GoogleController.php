<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('correo', $googleUser->email)->first();

            if ($user) {
                // Usuario existente: vincular Google si no lo tiene
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                    ]);
                }
            } else {
                // Usuario nuevo: igual que registro normal
                $nameParts = explode(' ', $googleUser->name, 2);

                $user = User::create([
                    'nombres' => $nameParts[0],
                    'apellidos' => $nameParts[1] ?? null,
                    'correo' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'contrasena' => Hash::make(uniqid()),
                    'email_verified_at' => now(),
                    'id_rol' => User::ROL_CLIENTE,
                ]);
            }

            Auth::login($user);

            return redirect()->intended('/');

        } catch (\Exception $e) {
            \Log::error('Google login error: ' . $e->getMessage());

            return redirect()->route('login')
                ->with('error', 'No se pudo iniciar sesión con Google. Inténtalo de nuevo.');
        }
    }
}
