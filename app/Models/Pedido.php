<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    public function agencia()
    {
        return $this->belongsTo(Agencia::class, 'id_agencia');
    }
}
