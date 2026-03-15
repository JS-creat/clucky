<?php

namespace App\Services;

use Pusher\PushNotifications\PushNotifications;

class PusherBeamsService
{
    protected $beams;

    public function __construct()
    {
        $this->beams = new PushNotifications([
            'instanceId' => env('PUSHER_BEAMS_INSTANCE_ID'),
            'secretKey' => env('PUSHER_BEAMS_SECRET_KEY'),
        ]);
    }

    public function enviarOferta($nombreProducto, $precioOferta, $categoria = '')
    {
        $iconUrl = url('/images/logo_notificacion.jpg');
        
        $titulo = "OFERTA ESPECIAL";
        $mensaje = $categoria 
            ? "$nombreProducto en $categoria ahora a solo S/ " . number_format($precioOferta, 2)
            : "$nombreProducto ahora a solo S/ " . number_format($precioOferta, 2);
        
        $resultado = $this->beams->publishToInterests(
            ['ofertas'],
            [
                "web" => [
                    "notification" => [
                        "title" => $titulo,
                        "body" => $mensaje,
                        "icon" => $iconUrl
                    ]
                ]
            ]
        );
        
        return $resultado;
    }

    public function enviarLanzamiento($nombreProducto, $categoria = '')
    {
        $iconUrl = url('/images/logo_notificacion.jpg');
        
        $titulo = "NUEVO PRODUCTO!";
        $mensaje = $categoria 
            ? "Ya disponible: $nombreProducto en $categoria"
            : "Ya disponible: $nombreProducto";
        
        $resultado = $this->beams->publishToInterests(
            ['lanzamientos'],  
            [
                "web" => [
                    "notification" => [
                        "title" => $titulo,
                        "body" => $mensaje,
                        "icon" => $iconUrl
                    ]
                ]
            ]
        );
        
        return $resultado;
    }

    public function enviarCarrito($userId, $titulo, $mensaje)
    {
        $iconUrl = url('/images/logo_notificacion.jpg');
        
        $resultado = $this->beams->publishToUsers(
            ["carrito-$userId"],
            [
                "web" => [
                    "notification" => [
                        "title" => $titulo,
                        "body" => $mensaje,
                        "icon" => $iconUrl
                    ]
                ]
            ]
        );
        
        return $resultado;
    }
}