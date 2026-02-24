<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria';
    protected $primaryKey = 'id_categoria';
    
    // La tabla categoría NO tiene timestamps en tu BD
    public $timestamps = false;

    protected $fillable = [
        'nombre_categoria'
    ];

    /**
     * Relación con productos
     * Una categoría tiene muchos productos
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria', 'id_categoria');
    }

    /**
     * Scope para categorías con productos activos
     */
    public function scopeConProductosActivos($query)
    {
        return $query->whereHas('productos', function($q) {
            $q->activos();
        });
    }

    /**
     * Obtener el conteo de productos activos en esta categoría
     */
    public function getProductosActivosCountAttribute()
    {
        return $this->productos()->activos()->count();
    }

    /**
     * Obtener productos disponibles (con stock) de esta categoría
     */
    public function productosDisponibles()
    {
        return $this->productos()->disponibles();
    }
}