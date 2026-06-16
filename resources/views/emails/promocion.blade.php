<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px;">
    <div style="max-width:600px; margin:auto; background:white; padding:30px; border-radius:8px;">
        <h2 style="font-size:20px; font-weight:bold;">¡Nueva oferta disponible!</h2>
        <p style="color:#555;">El producto <strong>{{ $producto->nombre_producto }}</strong> ahora tiene un precio especial.</p>

        <div style="margin:20px 0; padding:15px; background:#f9f9f9; border-radius:6px;">
            <p style="margin:0; font-size:14px; color:#999; text-decoration:line-through;">Antes: S/ {{ number_format($producto->precio, 2) }}</p>
            <p style="margin:5px 0 0; font-size:22px; font-weight:bold; color:#e53e3e;">Ahora: S/ {{ number_format($producto->precio_oferta, 2) }}</p>
        </div>

        <a href="{{ url('/producto/' . $producto->id_producto) }}"
           style="display:inline-block; background:black; color:white; padding:12px 24px; border-radius:4px; text-decoration:none; font-weight:bold;">
            Ver producto
        </a>

        <p style="margin-top:30px; font-size:11px; color:#aaa;">
            Recibes este correo porque estás registrado en B-EDEN.
        </p>
    </div>
</body>
</html>
