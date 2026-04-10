<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedido';
    protected $primaryKey = 'id_pedido';

    protected $casts = [
        'fecha_pedido' => 'datetime',
        'fecha_envio' => 'datetime',
        'fecha_entrega_estimada' => 'date',
        'fecha_entrega_real' => 'datetime',
    ];

    protected $fillable = [
        'numero_pedido',
        'fecha_pedido',
        'total_pedido',
        'estado_pedido',
        'fecha_envio',
        'fecha_entrega_estimada',
        'fecha_entrega_real',
        'id_distrito',
        'id_usuario',
        'id_cupon',
        'id_tipo_entrega',
        'id_agencia',
        'costo_envio',
        'nombre_agencia',
        'direccion',
    ];

    // ── Relaciones ──────────────────────────────────

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido');
    }

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'id_distrito');
    }

    public function tipoEntrega()
    {
        return $this->belongsTo(TipoEntrega::class, 'id_tipo_entrega');
    }

    public function agencia()
    {
        return $this->belongsTo(Agencia::class, 'id_agencia');
    }
}
