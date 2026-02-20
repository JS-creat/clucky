<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoVariante extends Model
{

    protected $table = 'producto_variante';

    protected $primaryKey = 'id_variante';

    protected $fillable = [
        'id_producto',
        'talla',
        'color',
        'stock',
        'sku'
    ];


    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

}
