<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray($request)
    {
        // Generar URL completa para la imagen principal
        $imagenPrincipal = null;
        if ($this->imagen) {
            if (filter_var($this->imagen, FILTER_VALIDATE_URL)) {
                $imagenPrincipal = $this->imagen;
            } else {
                $imagenPrincipal = url('storage/' . $this->imagen);
            }
        }

        // Procesar la galería de imágenes si existe
        $galeria = [];
        if ($this->galeria) {
            $imagenesGaleria = json_decode($this->galeria, true) ?? [];
            $galeria = collect($imagenesGaleria)->map(function($img) {
                if (filter_var($img, FILTER_VALIDATE_URL)) {
                    return $img;
                }
                return url('storage/' . $img);
            })->toArray();
        }

        // Obtener variantes del producto
        $variantes = $this->whenLoaded('variantes', function() {
            return $this->variantes;
        }, collect());

        // Calcular stock total
        $stockTotal = $variantes->sum('stock');

        // Obtener tallas únicas
        $tallas = $variantes->pluck('talla')->unique()->filter()->values()->toArray();

        // Obtener colores únicos
        $colores = $variantes->pluck('color')->unique()->filter()->values()->toArray();

        // Calcular SKU principal (usar el de la primera variante o el del producto si tuviera)
        $skuPrincipal = $variantes->isNotEmpty() ? $variantes->first()->sku : null;

        // Calcular precio final considerando oferta y promoción
        $precioOriginal = (float) $this->precio;
        $precioFinal = $precioOriginal;
        $descuento = 0;

        if ($this->precio_oferta) {
            $precioFinal = (float) $this->precio_oferta;
            $descuento = round((($precioOriginal - $precioFinal) / $precioOriginal) * 100, 0);
        } elseif ($this->id_promocion && $this->promocion && $this->promocion->estado_promocion) {
            $precioFinal = $precioOriginal - $this->promocion->descuento;
            $descuento = round(($this->promocion->descuento / $precioOriginal) * 100, 0);
        }

        return [
            'id' => $this->id_producto,
            'titulo' => $this->nombre_producto,
            'descripcion' => $this->descripcion ?? '',
            'precio' => $precioFinal,
            'precio_antes' => $precioFinal < $precioOriginal ? $precioOriginal : null,
            'descuento' => $descuento > 0 ? $descuento : null,
            'imagenes' => $galeria,
            'imagen_principal' => $imagenPrincipal,
            'categoria' => $this->categoria ? $this->categoria->nombre_categoria : null,
            'categoria_id' => $this->id_categoria,
            'genero' => $this->genero ? $this->genero->nombre_genero : null,
            'tallas' => $tallas,
            'colores' => $colores,
            'marca' => $this->marca,
            'stock' => $stockTotal,
            'sku' => $skuPrincipal,
            'disponible' => $stockTotal > 0 && $this->estado_producto == 1,
            'en_oferta' => $this->precio_oferta !== null || $this->id_promocion !== null,
            'variantes' => VarianteResource::collection($variantes),
            'promocion' => $this->when($this->id_promocion, function() {
                return [
                    'id' => $this->promocion->id_promocion,
                    'nombre' => $this->promocion->nombre_promocion,
                    'descuento' => $this->promocion->descuento,
                    'fecha_fin' => $this->promocion->fecha_fin,
                ];
            }),
            'fecha_creacion' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'fecha_actualizacion' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}