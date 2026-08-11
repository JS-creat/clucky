<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PagoService;
use Illuminate\Http\Request;

/**
 * Endpoints PÚBLICOS de retorno de Mercado Pago para la app móvil.
 *
 * El WebView de la app no comparte la sesión web (cookies), por eso
 * necesita rutas propias, sin el middleware `auth`. La seguridad no
 * depende de esta ruta ser "secreta" — el pago se verifica siempre
 * contra la API real de Mercado Pago dentro de PagoService, nunca
 * confiando en datos que vengan del navegador/cliente.
 *
 * Devuelven una página HTML mínima (no JSON) porque las abre
 * directamente el WebView tras la redirección de Mercado Pago; el
 * WebView de Flutter detecta la URL de destino y actúa en consecuencia
 * (no necesita parsear el contenido de la página).
 */
class PagoMovilApiController extends Controller
{
    public function __construct(protected PagoService $pagoService) {}

    public function exito(Request $request)
    {
        $paymentId = $request->payment_id;

        if (!$paymentId) {
            return $this->paginaResultado('error', 'Datos de pago no válidos.');
        }

        try {
            $pedido = $this->pagoService->confirmarPago($paymentId);
        } catch (\Exception $e) {
            \Log::error('Error verificando pago (móvil)', ['mensaje' => $e->getMessage()]);
            return $this->paginaResultado('error', 'Error al verificar el pago.');
        }

        if (!$pedido) {
            return $this->paginaResultado('fallo');
        }

        if ($pedido->estado_pedido === 'Anulado') {
            return $this->paginaResultado(
                'fallo',
                'Tu pago fue recibido pero el producto ya no tenía stock disponible. Nos pondremos en contacto contigo para el reembolso.'
            );
        }

        return $this->paginaResultado('exito');
    }

    public function fallo()
    {
        return $this->paginaResultado('fallo');
    }

    public function pendiente()
    {
        return $this->paginaResultado('pendiente');
    }

    /**
     * Página HTML mínima. El WebView de Flutter no lee este contenido:
     * detecta a qué URL se navegó (mobile/pago/exito, .../fallo,
     * .../pendiente) y reacciona en consecuencia. Este HTML es solo
     * un colchón visual por si hay algún delay antes de que la app
     * cierre el WebView.
     */
    private function paginaResultado(string $estado, ?string $mensaje = null)
    {
        $titulos = [
            'exito'     => '¡Pago exitoso!',
            'fallo'     => 'El pago no se pudo completar',
            'pendiente' => 'Pago pendiente',
            'error'     => 'Ocurrió un error',
        ];

        $titulo = $titulos[$estado] ?? 'Resultado del pago';
        $texto = $mensaje ?? 'Puedes cerrar esta ventana y volver a la app.';

        return response("
            <html>
                <head><meta name='viewport' content='width=device-width, initial-scale=1'></head>
                <body style='font-family:sans-serif; text-align:center; padding:40px 20px;'>
                    <h2>{$titulo}</h2>
                    <p>{$texto}</p>
                </body>
            </html>
        ", 200)->header('Content-Type', 'text/html');
    }
}