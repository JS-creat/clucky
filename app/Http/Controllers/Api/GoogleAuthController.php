<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Google_Client;

class GoogleAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $clientId = config('services.google.client_id'); 
            
            $client = new Google_Client(['client_id' => $clientId]);
            $payload = $client->verifyIdToken($request->id_token);

            if (!$payload) {
                return response()->json([
                    'message' => 'Token de Google inválido o expirado.',
                ], 401);
            }

            $email = $payload['email'] ?? null;

            if (!$email) {
                return response()->json([
                    'message' => 'Google no proporcionó un correo electrónico.',
                ], 422);
            }

            $googleId = $payload['sub'] ?? null;
            $avatar = $payload['picture'] ?? null;
            $givenName = $payload['given_name'] ?? '';
            $familyName = $payload['family_name'] ?? '';

            $user = User::where('correo', $email)->first();

            if ($user) {
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleId,
                        'avatar' => $avatar,
                    ]);
                }
            } else {
                $user = User::create([
                    'nombres' => $givenName,
                    'apellidos' => $familyName,
                    'correo' => $email,
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                    'contrasena' => Hash::make(str()->random(40)),
                    'email_verified_at' => now(),
                    'id_rol' => User::ROL_CLIENTE,
                ]);
            }

            $token = $user->createToken('flutter')->plainTextToken;

            return response()->json([
                'message' => 'Inicio de sesión con Google exitoso.',
                'token' => $token,
                'user' => $user,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Google Mobile Login Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'No se pudo iniciar sesión con Google.',
            ], 401);
        }
    }
}