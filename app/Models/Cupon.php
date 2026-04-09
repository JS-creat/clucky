<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    protected $table = 'cupones';
    protected $primaryKey = 'id_cupon';

    public $timestamps = false; 

    protected $fillable = [
        'codigo_cupon',
        'monto_cupon',
        'monto_compra_minima',
        'fecha_vencimiento',
        'estado_cupon'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'estado_cupon' => 'boolean',
    ];

    public function esValido($total)
    {
        return $this->estado_cupon
            && $this->fecha_vencimiento >= now()
            && $total >= $this->monto_compra_minima;
    }
}