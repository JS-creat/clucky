<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agencia extends Model
{
    protected $table      = 'agencia';
    protected $primaryKey = 'id_agencia';
    public    $timestamps = false;

    protected $fillable = [
        'nombre_agencia',
        'direccion',
        'costo_envio',
        'id_distrito',
        'estado',
    ];

    // Relaciones
    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'id_distrito', 'id_distrito');
    }

    // nombre completo de ubicación
    public function getUbicacionCompletaAttribute(): string
    {
        $d = $this->distrito;
        return "{$d->nombre_distrito} › {$d->provincia->nombre_provincia} › {$d->provincia->departamento->nombre_departamento}";
    }
}
