@extends('emails.layout')

@section('contenido')
    <h2 style="margin-top:0; color:#111; font-size:20px;">¡Gracias por tu compra!</h2>
    
    <p style="color:#555; font-size:14px; line-height:1.5;">
        Hola <strong>{{ $pedido->usuario->nombres ?? 'Cliente' }}</strong>,
    </p>

    <p style="color:#555; font-size:14px; line-height:1.5;">
        Confirmamos la recepción de tu pago para el pedido <strong style="color:#000;">#{{ $pedido->numero_pedido }}</strong>.
    </p>

    <p style="color:#555; font-size:14px; line-height:1.5;">
        Adjunto a este correo encontrarás tu <strong>Boleta de Venta Electrónica (PDF)</strong> emitida oficialmente ante SUNAT.
    </p>

    <div style="background:#f9f9f9; border-radius:8px; padding:15px; margin:20px 0; border:1px solid #eee;">
        <h4 style="margin:0 0 10px 0; font-size:14px; color:#111;">Resumen del Pedido:</h4>
        <table style="width:100%; font-size:13px; color:#555; border-collapse:collapse;">
            <tr>
                <td style="padding:4px 0;">Envío:</td>
                <td style="text-align:right; font-weight:bold;">S/ {{ number_format($pedido->costo_envio, 2) }}</td>
            </tr>
            <tr>
                <td style="padding:4px 0; border-top:1px dashed #ccc; font-weight:bold; color:#000;">Total Pagado:</td>
                <td style="text-align:right; font-weight:bold; border-top:1px dashed #ccc; color:#000; font-size:15px;">
                    S/ {{ number_format($pedido->total_pedido, 2) }}
                </td>
            </tr>
        </table>
    </div>

    <p style="color:#888; font-size:12px; margin-bottom:0;">
        Si deseas realizar alguna consulta sobre tu pedido, puedes responder directamente a este correo.
    </p>
@endsection