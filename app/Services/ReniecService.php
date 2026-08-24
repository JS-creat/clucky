<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReniecService
{
    /**
     * Consulta un DNI en apisperu.com y devuelve el nombre completo,
     * o null si falla (no debe romper el flujo del usuario).
     */
    public function consultarDni(string $dni): ?array
    {
        if (!preg_match('/^\d{8}$/', $dni)) {
            return null;
        }

        $token = config('services.apisperu.dni_token');

        try {
            $response = Http::timeout(5)
                ->get("https://dniruc.apisperu.com/api/v1/dni/{$dni}", [
                    'token' => $token,
                ]);

            if (!$response->successful()) {
                Log::warning("ReniecService: respuesta no exitosa para DNI {$dni}", [
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();

            if (empty($data['nombres'])) {
                return null;
            }

            return [
                'nombres'          => $data['nombres'],
                'apellido_paterno' => $data['apellidoPaterno'] ?? '',
                'apellido_materno' => $data['apellidoMaterno'] ?? '',
                'nombre_completo'  => trim($data['nombres'] . ' ' . ($data['apellidoPaterno'] ?? '') . ' ' . ($data['apellidoMaterno'] ?? '')),
            ];
        } catch (\Throwable $e) {
            Log::error("ReniecService: error consultando DNI {$dni}: " . $e->getMessage());
            return null;
        }
    }
}