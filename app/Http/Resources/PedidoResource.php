<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforma un Pedido (con sus detalles.variante.producto ya cargados
 * vía with()) al JSON que consume la app móvil.
 *
 * IMPORTANTE: la forma de este JSON replica intencionalmente los mismos
 * nombres de campo que ya existían antes (numero_pedido, total_pedido,
 * fecha_entrega_estimada, direccion_agencia, detalles->variante->producto
 * con nombre_producto/precio_oferta/imagen, etc.), porque Flutter
 * (pedido_service.dart, mis_pedidos.dart, detalles_pedido_screen.dart)
 * ya sabe leer exactamente esa estructura. Cambiar un nombre acá
 * rompería la app sin necesidad.
 */
class PedidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pedido'               => $this->id_pedido,
            'numero_pedido'           => $this->numero_pedido,
            'fecha_pedido'            => $this->fecha_pedido,
            'total_pedido'            => (float) $this->total_pedido,
            'costo_envio'             => (float) ($this->costo_envio ?? 0),
            'estado_pedido'           => $this->estado_pedido,
            'motivo_anulacion'        => $this->motivo_anulacion,
            'payment_id'              => $this->payment_id,
            'nombre_agencia'          => $this->nombre_agencia,
            // 🟢 Cubrimos ambas columnas por si la dirección de la
            // agencia terminó guardada en 'direccion' en vez de
            // 'direccion_agencia' (ver nota sobre $fillable del modelo).
            'direccion_agencia'       => $this->direccion_agencia ?? $this->direccion,
            'fecha_envio'             => $this->fecha_envio,
            'fecha_entrega_estimada'  => $this->fecha_entrega_estimada,
            'fecha_entrega_real'      => $this->fecha_entrega_real,
            'id_distrito'             => $this->id_distrito,
            'id_usuario'              => $this->id_usuario,
            'id_cupon'                => $this->id_cupon,
            'id_tipo_entrega'         => $this->id_tipo_entrega,
            'id_agencia'              => $this->id_agencia,
            'created_at'              => $this->created_at,
            'updated_at'              => $this->updated_at,
            'direccion'               => $this->direccion,
            'detalles'                => $this->detalles->map(function ($detalle) {
                $variante = $detalle->variante;
                $producto = $variante?->producto;

                return [
                    'id_detalle_pedido' => $detalle->id_detalle_pedido,
                    'id_pedido'         => $detalle->id_pedido,
                    'id_variante'       => $detalle->id_variante,
                    'cantidad'          => $detalle->cantidad,
                    'precio_unitario'   => (float) $detalle->precio_unitario,
                    'subtotal'          => (float) $detalle->subtotal,
                    'variante'          => $variante ? [
                        'id_variante' => $variante->id_variante,
                        'id_producto' => $variante->id_producto,
                        'talla'       => $variante->talla,
                        'color'       => $variante->color,
                        'stock'       => $variante->stock,
                        'sku'         => $variante->sku,
                        'created_at'  => $variante->created_at,
                        'updated_at'  => $variante->updated_at,
                        'producto'    => $producto ? [
                            'id_producto'     => $producto->id_producto,
                            'nombre_producto' => $producto->nombre_producto,
                            'descripcion'     => $producto->descripcion,
                            'precio'          => (float) $producto->precio,
                            'precio_oferta'   => $producto->precio_oferta !== null
                                ? (float) $producto->precio_oferta
                                : null,
                            'imagen'          => $producto->imagen,
                            'galeria'         => $producto->galeria,
                            'marca'           => $producto->marca,
                            'estado_producto' => $producto->estado_producto,
                            'id_genero'       => $producto->id_genero,
                            'id_categoria'    => $producto->id_categoria,
                            'created_at'      => $producto->created_at,
                            'updated_at'      => $producto->updated_at,
                        ] : null,
                    ] : null,
                ];
            }),
        ];
    }
}