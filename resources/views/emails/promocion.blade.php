@extends('emails.layout')

@section('contenido')
    <h2 style="margin:0 0 16px; font-size:20px; font-weight:900; color:#000;">
        ¡Nueva oferta disponible!
    </h2>

    <p style="color:#555; font-size:15px; line-height:1.6;">
        El producto <strong>{{ $producto->nombre_producto }}</strong> ahora tiene un precio especial.
    </p>

    <div style="margin:24px 0; padding:20px; background:#f9f9f9; border-radius:8px; text-align:center;">
        <p style="margin:0; font-size:13px; color:#999; text-decoration:line-through;">
            Antes: S/ {{ number_format($producto->precio, 2) }}
        </p>
        <p style="margin:8px 0 0; font-size:26px; font-weight:900; color:#e53e3e;">
            Ahora: S/ {{ number_format($producto->precio_oferta, 2) }}
        </p>
    </div>

    <div style="text-align:center; margin-top:24px;">
        <a href="{{ url('/producto/' . $producto->id_producto) }}"
           style="display:inline-block; background:#000; color:#fff; padding:14px 32px; border-radius:6px; text-decoration:none; font-weight:900; font-size:14px; letter-spacing:1px;">
            Ver producto
        </a>
    </div>
@endsection
