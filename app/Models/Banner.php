<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';
    protected $primaryKey = 'id_banner';

    protected $fillable = [
        'titulo',
        'subtitulo',
        'descripcion',
        'etiqueta',
        'texto_boton',
        'url_boton',
        'imagen',
        'orden',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];
}