<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $table = 'carrito';
    protected $primaryKey = 'id_carrito';
    public $timestamps = true;
    protected $fillable = [
        'id_usuario',
        'estado'
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleCarrito::class, 'id_carrito');
    }
}
