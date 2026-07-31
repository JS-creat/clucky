<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforma un modelo Cupon a su representación JSON pública.
 * Reemplaza el ->map() manual que antes vivía en CuponApiController.
 */
class CuponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_cupon,
            'codigo' => $this->codigo_cupon,
            'descripcion' => $this->descripcion,
            'tipo_descuento' => $this->tipo_descuento,
            'valor_descuento' => (float) $this->valor_descuento,
            'descuento_formateado' => $this->descuento_formateado,
            'monto_compra_minima' => (float) $this->monto_compra_minima,
            'fecha_vencimiento' => $this->fecha_vencimiento->format('Y-m-d'),
            'dias_restantes' => $this->dias_restantes,
            'es_privado' => $this->es_privado,
        ];
    }
}