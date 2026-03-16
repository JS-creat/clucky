<?php
// app/Http/Resources/DetalleCarritoResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleCarritoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_detalle' => $this->id_detalle_carrito,
            'cantidad' => $this->cantidad,
            'variante' => new CarritoVarianteResource($this->whenLoaded('variante')),
        ];
    }
}