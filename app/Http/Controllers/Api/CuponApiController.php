<?php

namespace App\Http\Controllers\Api;

use App\Models\Cupon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CuponApiController extends Controller
{
    /**
     * Listar cupones disponibles para el usuario autenticado
     * GET /api/cupones/disponibles
     */
    public function disponibles(Request $request)
    {
        /** @var User $usuario */
        $usuario = Auth::user();

        $cupones = Cupon::disponiblesPara($usuario);

        return response()->json([
            'success' => true,
            'data' => $cupones->map(function ($cupon) {
                return [
                    'id' => $cupon->id_cupon,
                    'codigo' => $cupon->codigo_cupon,
                    'descripcion' => $cupon->descripcion,
                    'tipo_descuento' => $cupon->tipo_descuento,
                    'valor_descuento' => (float) $cupon->valor_descuento,
                    'descuento_formateado' => $cupon->descuento_formateado,
                    'monto_compra_minima' => (float) $cupon->monto_compra_minima,
                    'fecha_vencimiento' => $cupon->fecha_vencimiento->format('Y-m-d'),
                    'dias_restantes' => $cupon->dias_restantes,
                    'es_privado' => $cupon->es_privado,
                ];
            }),
            'total' => $cupones->count(),
        ]);
    }

    /**
     * Validar un cupón antes de aplicarlo
     * POST /api/cupones/validar
     */
    public function validar(Request $request)
    {
        $request->validate([
            'codigo_cupon' => 'required|string|max:50',
            'monto_carrito' => 'required|numeric|min:0',
        ]);

        /** @var User $usuario */
        $usuario = Auth::user();
        $codigo = strtoupper(trim($request->codigo_cupon));
        $montoCarrito = (float) $request->monto_carrito;

        $cupon = Cupon::vigentes()->porCodigo($codigo)->first();

        if (!$cupon) {
            return response()->json([
                'success' => false,
                'message' => 'Cupón no encontrado o no vigente.',
            ], 404);
        }

        $resultado = $cupon->puedeUsar($usuario);
        if (!$resultado['valido']) {
            return response()->json([
                'success' => false,
                'message' => $resultado['errores'][0],
                'errores' => $resultado['errores'],
            ], 400);
        }

        if ($montoCarrito < $cupon->monto_compra_minima) {
            return response()->json([
                'success' => false,
                'message' => 'El monto mínimo de compra es S/ ' . number_format($cupon->monto_compra_minima, 2),
                'monto_minimo_requerido' => (float) $cupon->monto_compra_minima,
                'monto_carrito' => $montoCarrito,
            ], 400);
        }

        $descuento = $cupon->calcularDescuento($montoCarrito);

        return response()->json([
            'success' => true,
            'message' => 'Cupón válido',
            'data' => [
                'cupon_id' => $cupon->id_cupon,
                'codigo' => $cupon->codigo_cupon,
                'tipo_descuento' => $cupon->tipo_descuento,
                'valor_descuento' => (float) $cupon->valor_descuento,
                'descuento_formateado' => $cupon->descuento_formateado,
                'monto_compra_minima' => (float) $cupon->monto_compra_minima,
                'monto_carrito' => $montoCarrito,
                'descuento_aplicado' => $descuento,
                'total_con_descuento' => round($montoCarrito - $descuento, 2),
                'ahorro' => $descuento,
            ],
        ]);
    }

    /**
     * Aplicar cupón (registrar uso)
     * POST /api/cupones/aplicar
     */
    public function aplicar(Request $request)
    {
        $request->validate([
            'codigo_cupon' => 'required|string|max:50',
            'monto_carrito' => 'required|numeric|min:0',
        ]);

        /** @var User $usuario */
        $usuario = Auth::user();
        $codigo = strtoupper(trim($request->codigo_cupon));
        $montoCarrito = (float) $request->monto_carrito;

        $cupon = Cupon::vigentes()->porCodigo($codigo)->first();

        if (!$cupon) {
            return response()->json([
                'success' => false,
                'message' => 'Cupón no encontrado.',
            ], 404);
        }

        $descuento = $cupon->calcularDescuento($montoCarrito);
        $registrado = $cupon->registrarUso($usuario, $montoCarrito, $descuento);

        if (!$registrado) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo aplicar el cupón.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cupón aplicado exitosamente.',
            'data' => [
                'cupon_id' => $cupon->id_cupon,
                'codigo' => $cupon->codigo_cupon,
                'descuento_aplicado' => $descuento,
                'total_con_descuento' => round($montoCarrito - $descuento, 2),
                'usos_restantes_globales' => $cupon->uso_maximo_global
                    ? ($cupon->uso_maximo_global - $cupon->usos_actuales)
                    : null,
            ],
        ]);
    }
}
