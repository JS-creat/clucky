<?php
// app/Http/Resources/CarritoProductoResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarritoProductoResource extends JsonResource
{
    public function toArray($request)
    {
        $imagenPrincipal = null;
        if ($this->imagen) {
            $imagenPrincipal = filter_var($this->imagen, FILTER_VALIDATE_URL)
                ? $this->imagen
                : url('/api/imagen/' . $this->imagen);
        }

        return [
            'id' => $this->id_producto,
            'titulo' => $this->nombre_producto,
            'precio' => $this->precio_oferta ?? $this->precio,
            'precio_original' => $this->precio,
            'imagen_principal' => $imagenPrincipal,
        ];
    }
}