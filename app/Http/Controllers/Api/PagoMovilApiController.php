<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PagoService;
use Illuminate\Http\Request;

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