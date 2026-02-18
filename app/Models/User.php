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

    public $timestamps = true;

    protected $fillable = [

        'nombres',

        'apellidos',

        'correo',

        'contrasena',

        'telefono',

        'numero_documento',

        'id_tipo_documento',

        'id_rol'

    ];



    protected $hidden = [

        'contrasena',

        'remember_token',

    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function getAuthPassword()
    {

        return $this->contrasena;

    }



    public function getEmailForVerification()
    {

        return $this->correo;

    }



    public function routeNotificationForMail($notification)
    {

        return $this->correo;

    }

    public function getAuthIdentifierName()
    {

        return 'id_usuario';

    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }


}
