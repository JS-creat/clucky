<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * Modelo Cupon - Gestión de cupones de descuento
 *
 * Los cupones se gestionan desde el panel admin web
 * pero SOLO son aplicables en la aplicación móvil.
 */
class Cupon extends Model
{
    // ============================================
    // CONFIGURACIÓN
    // ============================================

    protected $table = 'cupones';
    protected $primaryKey = 'id_cupon';
    public $timestamps = true;

    protected $fillable = [
        'codigo_cupon',
        'descripcion',
        'tipo_descuento',
        'valor_descuento',
        'monto_cupon',
        'monto_compra_minima',
        'fecha_vencimiento',
        'estado_cupon',
        'uso_maximo_global',
        'uso_maximo_por_usuario',
        'usos_actuales',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date:Y-m-d',
        'estado_cupon' => 'boolean',
        'valor_descuento' => 'decimal:2',
        'monto_cupon' => 'decimal:2',
        'monto_compra_minima' => 'decimal:2',
        'usos_actuales' => 'integer',
        'uso_maximo_global' => 'integer',
        'uso_maximo_por_usuario' => 'integer',
    ];

    // ============================================
    // CONSTANTES
    // ============================================

    public const TIPO_PORCENTAJE = 'porcentaje';
    public const TIPO_MONTO_FIJO = 'monto_fijo';

    // ============================================
    // SCOPES (FILTROS REUTILIZABLES)
    // ============================================

    public function scopeActivos($query)
    {
        return $query->where('estado_cupon', true);
    }

    public function scopeNoVencidos($query)
    {
        return $query->whereDate('fecha_vencimiento', '>=', now()->toDateString());
    }

    public function scopeVigentes($query)
    {
        return $query->activos()
            ->noVencidos()
            ->where(function ($q) {
                $q->whereNull('uso_maximo_global')
                  ->orWhereRaw('usos_actuales < uso_maximo_global');
            });
    }

    public function scopePorCodigo($query, string $codigo)
    {
        return $query->whereRaw('LOWER(codigo_cupon) = ?', [strtolower($codigo)]);
    }

    // ============================================
    // RELACIONES
    // ============================================

    public function usuariosAsignados(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'cupon_usuario',
            'id_cupon',
            'id_usuario',
            'id_cupon',
            'id_usuario'
        )->withPivot('usos_realizados')
         ->withTimestamps();
    }

    public function usosHistorial()
    {
        return $this->hasMany(CuponUso::class, 'id_cupon', 'id_cupon');
    }

    // ============================================
    // ACCESSORS (atributos computados)
    // ============================================

    public function getVencidoAttribute(): bool
    {
        return $this->fecha_vencimiento < now()->startOfDay();
    }

    public function getAgotadoAttribute(): bool
    {
        if (is_null($this->uso_maximo_global)) {
            return false;
        }
        return $this->usos_actuales >= $this->uso_maximo_global;
    }

    public function getVigenteAttribute(): bool
    {
        return $this->estado_cupon && !$this->vencido && !$this->agotado;
    }

    public function getDescuentoFormateadoAttribute(): string
    {
        return $this->tipo_descuento === self::TIPO_PORCENTAJE
            ? $this->valor_descuento . '%'
            : 'S/ ' . number_format($this->valor_descuento, 2);
    }

    public function getDiasRestantesAttribute(): int
    {
        return now()->diffInDays($this->fecha_vencimiento, false);
    }

    public function getEsPrivadoAttribute(): bool
    {
        return $this->usuariosAsignados()->exists();
    }

    // ============================================
    // MÉTODOS DE NEGOCIO
    // ============================================

    public function calcularDescuento(float $montoCompra): float
    {
        if ($montoCompra < $this->monto_compra_minima) {
            return 0.0;
        }

        if ($this->tipo_descuento === self::TIPO_PORCENTAJE) {
            return round($montoCompra * ($this->valor_descuento / 100), 2);
        }

        return min($this->valor_descuento, $montoCompra);
    }

    public function puedeUsar(?User $usuario = null): array
    {
        $errores = [];

        if (!$this->estado_cupon) {
            $errores[] = 'El cupón está inactivo.';
        }

        if ($this->vencido) {
            $errores[] = 'El cupón ha vencido.';
        }

        if ($this->agotado) {
            $errores[] = 'Este cupón ha agotado sus usos disponibles.';
        }

        if ($this->es_privado) {
            if (!$usuario) {
                $errores[] = 'Este cupón requiere inicio de sesión.';
            } else {
                $asignado = $this->usuariosAsignados()
                    ->where('usuarios.id_usuario', $usuario->id_usuario)
                    ->exists();

                if (!$asignado) {
                    $errores[] = 'Este cupón no está disponible para tu cuenta.';
                }

                if ($this->uso_maximo_por_usuario && $asignado) {
                    $usos = $this->usuariosAsignados()
                        ->where('usuarios.id_usuario', $usuario->id_usuario)
                        ->first()?->pivot?->usos_realizados ?? 0;

                    if ($usos >= $this->uso_maximo_por_usuario) {
                        $errores[] = 'Has alcanzado el límite de usos de este cupón.';
                    }
                }
            }
        }

        if (!$this->es_privado && $this->uso_maximo_por_usuario && $usuario) {
            $usosHistorial = $this->usosHistorial()
                ->where('id_usuario', $usuario->id_usuario)
                ->count();

            if ($usosHistorial >= $this->uso_maximo_por_usuario) {
                $errores[] = 'Has alcanzado el límite de usos de este cupón.';
            }
        }

        return [
            'valido' => empty($errores),
            'errores' => $errores,
        ];
    }

    public function registrarUso(User $usuario, float $montoCarrito, float $montoDescuento): bool
    {
        $verificacion = $this->puedeUsar($usuario);
        if (!$verificacion['valido']) {
            return false;
        }

        return DB::transaction(function () use ($usuario, $montoCarrito, $montoDescuento) {
            $this->increment('usos_actuales');

            if ($this->es_privado) {
                $this->usuariosAsignados()->updateExistingPivot(
                    $usuario->id_usuario,
                    ['usos_realizados' => DB::raw('usos_realizados + 1')]
                );
            }

            CuponUso::create([
                'id_cupon' => $this->id_cupon,
                'id_usuario' => $usuario->id_usuario,
                'monto_descuento' => $montoDescuento,
                'monto_carrito' => $montoCarrito,
            ]);

            return true;
        });
    }

    public function asignarUsuarios(array $idsUsuarios): void
    {
        $this->usuariosAsignados()->syncWithPivotValues(
            $idsUsuarios,
            ['usos_realizados' => 0]
        );
    }

    public function desasignarUsuarios(array $idsUsuarios = []): void
    {
        if (empty($idsUsuarios)) {
            $this->usuariosAsignados()->detach();
        } else {
            $this->usuariosAsignados()->detach($idsUsuarios);
        }
    }

    // ============================================
    // MÉTODOS ESTÁTICOS (HELPERS)
    // ============================================

    public static function buscarPorCodigo(string $codigo): ?self
    {
        return self::vigentes()->porCodigo($codigo)->first();
    }

    public static function disponiblesPara(User $usuario)
    {
        return self::vigentes()
            ->where(function ($query) use ($usuario) {
                $query->whereDoesntHave('usuariosAsignados')
                    ->orWhereHas('usuariosAsignados', function ($q) use ($usuario) {
                        $q->where('usuarios.id_usuario', $usuario->id_usuario);
                    });
            })
            ->get()
            ->filter(function ($cupon) use ($usuario) {
                return $cupon->puedeUsar($usuario)['valido'];
            })
            ->values();
    }
}
