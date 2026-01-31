<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    protected $fillable = [
        'nombres',
        'apellidos',
        'correo',
        'contrasena',
        'id_rol'
    ];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function getAuthIdentifierName()
    {
        return 'correo';
    }

    // Laravel usará tu columna correo para verificación
    public function getEmailForVerification()
    {
        return $this->correo;
    }

    public function routeNotificationForMail($notification)
    {
        return $this->correo;
    }

}
