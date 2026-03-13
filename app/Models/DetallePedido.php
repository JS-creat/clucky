<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedido';
    protected $primaryKey = 'id_detalle_pedido';

    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'id_variante',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    // ── Relaciones

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function variante()
    {
        return $this->belongsTo(ProductoVariante::class, 'id_variante');
    }
}
