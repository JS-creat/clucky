<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $table      = 'distrito';
    protected $primaryKey = 'id_distrito';
    public    $timestamps = false;

    protected $fillable = ['nombre_distrito', 'id_provincia'];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'id_provincia', 'id_provincia');
    }

    public function agencias()
    {
        return $this->hasMany(Agencia::class, 'id_distrito', 'id_distrito');
    }
}
