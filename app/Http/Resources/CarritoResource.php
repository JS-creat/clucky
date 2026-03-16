<?php
// app/Http/Resources/CarritoResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarritoResource extends JsonResource
{
    public function toArray($request)
    {
        $items = DetalleCarritoResource::collection($this->whenLoaded('detalles'));
        
        $total = 0;
        $cantidadTotal = 0;
        
        foreach ($this->detalles as $detalle) {
            if ($detalle->variante && $detalle->variante->producto) {
                $precio = $detalle->variante->producto->precio_oferta ?? $detalle->variante->producto->precio;
                $total += $precio * $detalle->cantidad;
                $cantidadTotal += $detalle->cantidad;
            }
        }

        return [
            'id_carrito' => $this->id_carrito,
            'id_usuario' => $this->id_usuario,
            'items' => $items,
            'total' => $total,
            'cantidad_total' => $cantidadTotal,
            'fecha_creacion' => $this->created_at,
            'fecha_actualizacion' => $this->updated_at,
        ];
    }
}