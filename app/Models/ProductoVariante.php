<?php
// app/Models/ProductoVariante.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoVariante extends Model
{
    use HasFactory;

    protected $table = 'producto_variante';
    protected $primaryKey = 'id_variante';
    public $timestamps = true;

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
