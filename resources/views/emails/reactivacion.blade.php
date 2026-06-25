@extends('emails.layout')

@section('contenido')
    <h2 style="margin:0 0 16px; font-size:20px; font-weight:900; color:#000;">
        ¡Te extrañamos, {{ $usuario->nombres }}!
    </h2>

    <p style="color:#555; font-size:15px; line-height:1.6;">
        Hace un mes que no sabemos de ti y en B-EDEN te extrañamos. Te preparamos una sorpresa para que regreses.
    </p>

    <div style="margin:24px 0; padding:20px; background:#f9f9f9; border-radius:8px; text-align:center;">
        <p style="margin:0; font-size:12px; color:#999; text-transform:uppercase; font-weight:bold;">
            Tu cupón de descuento
        </p>
        <p style="margin:10px 0 0; font-size:28px; font-weight:900; color:#000; letter-spacing:4px;">
            REGRESA10
        </p>
        <p style="margin:8px 0 0; font-size:13px; color:#555;">
            10% de descuento en todo el carrito
        </p>
    </div>

    <div style="text-align:center; margin-top:24px;">
        <a href="{{ config('app.url') }}"
           style="display:inline-block; background:#000; color:#fff; padding:14px 32px; border-radius:6px; text-decoration:none; font-weight:900; font-size:14px; letter-spacing:1px;">
            Ir a la Tienda
        </a>
    </div>
@endsection
