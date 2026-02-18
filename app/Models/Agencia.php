<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agencia extends Model
{
    protected $table = 'agencia';
    protected $primaryKey = 'id_agencia';

    protected $fillable = [
        'nombre_agencia',
        'direccion',
        'id_distrito',
        'estado'
    ];

    public function distrito()
    {
        return $this->belongsTo(Distrito::class,'id_distrito');
    }

}
