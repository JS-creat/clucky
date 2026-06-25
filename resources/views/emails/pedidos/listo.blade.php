@extends('emails.layout')

@section('contenido')
    <h2 style="margin:0 0 16px; font-size:20px; font-weight:900; color:#1dcc06;">
        Pedido Listo
    </h2>

    <p style="color:#555; font-size:15px; line-height:1.6;">
        Hola <strong>{{ $pedido->usuario->nombres }}</strong>,<br>
        Te informamos que tu pedido <strong>#{{ $pedido->numero_pedido }}</strong> esta listo para recoger. Puedes verificar la direccion de entrega en tu perfil de B-EDEN.
    </p>

    <div style="margin:24px 0; padding:16px; background:#f9f9f9; border-radius:8px;">
        <p style="margin:0; font-size:13px; color:#555; line-height:1.6;">
            Si tienes dudas sobre la entrega puedes comunicarte con nosotros por WhatsApp o mediante una llamada.
        </p>
    </div>
@endsection
