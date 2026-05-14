<?php

namespace App\Services;

use Pusher\PushNotifications\PushNotifications;
use Illuminate\Support\Facades\Log;

class PusherBeamsService
{
    protected $beams;

    public function __construct()
    {
        $instanceId = env('PUSHER_BEAMS_INSTANCE_ID');
        $secretKey = env('PUSHER_BEAMS_SECRET_KEY');

        // Validamos que existan las credenciales antes de instanciar
        if (!empty($instanceId) && !empty($secretKey)) {
            try {
                $this->beams = new PushNotifications([
                    'instanceId' => $instanceId,
                    'secretKey' => $secretKey,
                ]);
            } catch (\Exception $e) {
                Log::error("Error al inicializar Pusher Beams: " . $e->getMessage());
                $this->beams = null;
            }
        } else {
            // Si no hay credenciales, asignamos null para evitar el error 500
            $this->beams = null;
        }
    }

    public function enviarOferta($nombreProducto, $precioOferta, $categoria = '')
    {
        // Si no se pudo inicializar Beams, salimos silenciosamente
        if (!$this->beams) return null;

        $iconUrl = url('/images/logo_notificacion.jpg');

        $titulo = "OFERTA ESPECIAL";
        $mensaje = $categoria
            ? "$nombreProducto en $categoria ahora a solo S/ " . number_format($precioOferta, 2)
            : "$nombreProducto ahora a solo S/ " . number_format($precioOferta, 2);

        return $this->beams->publishToInterests(
            ['ofertas'],
            [
                "fcm" => [
                    "notification" => [
                        "title" => $titulo,
                        "body" => $mensaje,
                    ]
                ],
                "web" => [
                    "notification" => [
                        "title" => $titulo,
                        "body" => $mensaje,
                        "icon" => $iconUrl
                    ]
                ]
            ]
        );
    }

    public function enviarLanzamiento($nombreProducto, $categoria = '')
    {
        if (!$this->beams) return null;

        $iconUrl = url('/images/logo_notificacion.jpg');

        $titulo = "NUEVO PRODUCTO!";
        $mensaje = $categoria
            ? "Ya disponible: $nombreProducto en $categoria"
            : "Ya disponible: $nombreProducto";

        return $this->beams->publishToInterests(
            ['lanzamientos'],
            [
                "fcm" => [
                    "notification" => [
                        "title" => $titulo,
                        "body" => $mensaje,
                    ]
                ],
                "web" => [
                    "notification" => [
                        "title" => $titulo,
                        "body" => $mensaje,
                        "icon" => $iconUrl
                    ]
                ]
            ]
        );
    }

    public function enviarCarrito($userId, $titulo, $mensaje)
    {
        if (!$this->beams) return null;

        $iconUrl = url('/images/logo_notificacion.jpg');

        return $this->beams->publishToUsers(
            ["carrito-$userId"],
            [
                "fcm" => [
                    "notification" => [
                        "title" => $titulo,
                        "body" => $mensaje,
                    ]
                ],
                "web" => [
                    "notification" => [
                        "title" => $titulo,
                        "body" => $mensaje,
                        "icon" => $iconUrl
                    ]
                ]
            ]
        );
    }
}
