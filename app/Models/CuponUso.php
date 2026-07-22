<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuponUso extends Model
{
    protected $table = 'cupon_usos';
    protected $primaryKey = 'id_cupon_uso';
    public $timestamps = true;

    protected $fillable = [
        'id_cupon',
        'id_usuario',
        'monto_descuento',
        'monto_carrito',
    ];

    protected $casts = [
        'monto_descuento' => 'decimal:2',
        'monto_carrito' => 'decimal:2',
    ];

    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'id_cupon', 'id_cupon');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
