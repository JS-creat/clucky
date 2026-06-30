@extends('emails.layout')

@section('contenido')
    <h2 style="margin:0 0 16px; font-size:20px; font-weight:900; color:#e53e3e;">
        Pedido Anulado
    </h2>

    <p style="color:#555; font-size:15px; line-height:1.6;">
        Hola <strong>{{ $pedido->usuario->nombres }}</strong>,<br>
        lamentamos informarte que tu pedido <strong>#{{ $pedido->numero_pedido }}</strong> ha sido anulado.
    </p>

    <div style="margin:24px 0; padding:16px; background:#fff5f5; border-left:4px solid #e53e3e; border-radius:4px;">
        <p style="margin:0; font-size:11px; color:#999; text-transform:uppercase; font-weight:bold;">Motivo</p>
        <p style="margin:8px 0 0; color:#333; font-size:15px;">{{ $pedido->motivo_anulacion }}</p>
    </div>

    <div style="margin:24px 0; padding:16px; background:#f9f9f9; border-radius:8px;">
        <p style="margin:0; font-size:13px; color:#555; line-height:1.6;">
            Si realizaste un pago, nos comunicaremos contigo a la brevedad para coordinar la devolución.
        </p>
    </div>
@endsection
