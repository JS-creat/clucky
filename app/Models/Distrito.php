<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $table = 'distrito';
    protected $primaryKey = 'id_distrito';
    public $timestamps = false;

    public function agencias()
    {
        return $this->hasMany(Agencia::class,'id_distrito');
    }

}
