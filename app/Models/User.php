<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Iluminate\Eloquent\Factories\HasFactory;

class User extends Authenticatable {
    protected $table = 'usuario';       // Nombre de tu tabla
    protected $primaryKey = 'id_usuario'; // Tu llave primaria

    public $timestamps = false;
    protected $fillable = [
        'nombres', 'apellidos', 'correo', 'contrasena', 'id_rol'
    ];

    // Esto es para que Laravel sepa que "contrasena" es el password
    public function getAuthPassword() {
        return $this->contrasena;
    }
}
