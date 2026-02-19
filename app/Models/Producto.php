<?php
// app/Models/Producto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'id_producto';
    public $timestamps = true;

    protected $fillable = [
        'codigo',
        'nombre_producto',
        'descripcion',
        'precio',
        'precio_oferta',
        'imagen',
        'galeria',
        'marca',
        'estado_producto',
        'id_genero',
        'id_categoria',
        'id_promocion'
    ];

    protected $casts = [
        'galeria' => 'array',
        'precio' => 'decimal:2',
        'precio_oferta' => 'decimal:2',
    ];

    // Relaciones
    public function promocion()
    {
        return $this->belongsTo(Promocion::class, 'id_promocion');
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'id_genero');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function variantes()
    {
        return $this->hasMany(ProductoVariante::class, 'id_producto', 'id_producto');
    }

    // Accessors
    public function getPrecioFormateadoAttribute()
    {
        return 'S/ ' . number_format($this->precio, 2);
    }

    public function getPrecioOfertaFormateadoAttribute()
    {
        return $this->precio_oferta ? 'S/ ' . number_format($this->precio_oferta, 2) : null;
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado_producto', 1);
    }
}
