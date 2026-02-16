<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCarrito extends Model
{
    protected $table = 'detalle_carrito';
    protected $primaryKey = 'id_detalle_carrito';
    public $timestamps = false;

    protected $fillable = [
        'cantidad',
        'id_carrito',
        'id_producto'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
