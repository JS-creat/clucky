<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCarrito extends Model
{

    protected $table = 'detalle_carrito';

    protected $primaryKey = 'id_detalle_carrito';

    protected $fillable = [
        'id_carrito',
        'id_variante',
        'cantidad'
    ];


    public function variante()
    {
        return $this->belongsTo(ProductoVariante::class, 'id_variante');
    }

}
