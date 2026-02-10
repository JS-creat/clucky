<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    protected $fillable = [
    'sku', 'nombre_producto', 'slug', 'descripcion',
    'precio', 'precio_oferta', 'talla', 'color',
    'stock', 'imagen', 'marca', 'id_genero',
    'id_categoria', 'id_promocion'
];

    // Relación para traer el descuento
    public function promocion()
    {
        return $this->belongsTo(Promocion::class, 'id_promocion');
    }
    protected $casts = [
    'galeria' => 'array',
];
}
