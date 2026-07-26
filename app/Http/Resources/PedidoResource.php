<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PedidoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_pedido'      => $this->id_pedido,
            'numero_pedido'  => $this->numero_pedido,
            'estado_pedido'  => $this->estado_pedido,
            'total_pedido'   => $this->total_pedido,
            'costo_envio'    => $this->costo_envio,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'detalles'       => $this->whenLoaded('detalles', function () {
                return $this->detalles->map(function ($detalle) {
                    return [
                        'id_variante'     => $detalle->id_variante,
                        'cantidad'        => $detalle->cantidad,
                        'precio_unitario' => $detalle->precio_unitario,
                        'subtotal'        => $detalle->subtotal,
                        'producto'        => $detalle->variante->producto->nombre_producto ?? null,
                        'talla'           => $detalle->variante->talla ?? null,
                        'color'           => $detalle->variante->color ?? null,
                    ];
                });
            }),
        ];
    }
}
