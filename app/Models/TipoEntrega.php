<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEntrega extends Model
{
    protected $table = 'tipo_entrega';
    protected $primaryKey = 'id_tipo_entrega';
    public $timestamps = false;

    protected $fillable = [
        'nombre_tipo_entrega',
        'estado'
    ];
}
