<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background:#f4f4f4; font-family: Arial, sans-serif;">
    <div style="max-width:600px; margin:40px auto; padding:0 20px;">

        {{-- HEADER --}}
        <div style="background:#000; padding:24px 30px; border-radius:12px 12px 0 0; text-align:center;">
            <h1 style="margin:0; color:#fff; font-size:22px; font-weight:900; letter-spacing:4px; text-transform:uppercase;">
                B-EDEN
            </h1>
        </div>

        {{-- CONTENIDO --}}
        <div style="background:#fff; padding:36px 30px;">
            @yield('contenido')
        </div>

        {{-- FOOTER --}}
        <div style="background:#f9f9f9; padding:20px 30px; border-radius:0 0 12px 12px; border-top:1px solid #eee; text-align:center;">
            <p style="margin:0; font-size:11px; color:#aaa;">
                Recibes este correo porque tienes una cuenta en B-EDEN.<br>
                Si tienes dudas contáctanos por WhatsApp o llamada.
            </p>
        </div>

    </div>
</body>
</html>
