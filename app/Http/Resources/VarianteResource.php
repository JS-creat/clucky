<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VarianteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id_variante,
            'talla' => $this->talla,
            'color' => $this->color,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'disponible' => $this->stock > 0,
        ];
    }
}