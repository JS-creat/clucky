<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>
        Boleta {{ $serie }}-{{ $correlativo }}
    </title>

    <style>
        @page {
            size: A4 portrait;
            margin: 28px 32px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #222;
            font-size: 10px;
            margin: 0;
            background: #fff;
        }


        .header {
            width: 100%;
            margin-bottom: 18px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-container {
            text-align: center;
            padding-bottom: 8px;
        }

        .logo {
            max-width: 150px;
            max-height: 70px;
        }

        .marca {
            font-size: 25px;
            font-weight: bold;
            letter-spacing: 3px;
            margin-top: 5px;
        }

        .slogan {
            color: #777;
            font-size: 9px;
            margin-top: 3px;
            letter-spacing: 1px;
        }

        .empresa {
            text-align: center;
            font-size: 9px;
            line-height: 1.5;
            margin-top: 9px;
        }

        .comprobante {
            width: 100%;
            border: 1px solid #777;
            border-radius: 5px;
            text-align: center;
            padding: 12px 10px;
            margin-top: 14px;
        }

        .comprobante-titulo {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .comprobante-electronica {
            font-size: 8px;
            letter-spacing: 3px;
            color: #666;
            margin-top: 3px;
        }

        .ruc {
            font-size: 9px;
            margin-top: 7px;
        }

        .numero {
            font-size: 16px;
            font-weight: bold;
            margin-top: 8px;
            letter-spacing: 1px;
        }

        .separador {
            border-top: 1px solid #ddd;
            margin: 16px 0;
        }

        .seccion-titulo {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 7px;
        }

        .cliente {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }

        .cliente td {
            padding: 7px 9px;
            vertical-align: top;
            border-bottom: 1px solid #eee;
        }

        .cliente tr:last-child td {
            border-bottom: none;
        }

        .etiqueta {
            color: #777;
            font-size: 8px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .valor {
            font-size: 9.5px;
            font-weight: bold;
        }

        .productos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .productos thead {
            background: #eeeeee;
        }

        .productos th {
            padding: 8px 6px;
            text-align: left;
            font-size: 8px;
            text-transform: uppercase;
            border-bottom: 1px solid #bbb;
        }

        .productos td {
            padding: 8px 6px;
            border-bottom: 1px solid #eeeeee;
            vertical-align: top;
        }

        .descripcion {
            font-weight: bold;
            font-size: 9px;
        }

        .variante {
            color: #777;
            font-size: 8px;
            margin-top: 3px;
        }

        .centro {
            text-align: center !important;
        }

        .derecha {
            text-align: right !important;
        }

        .envio {
            width: 100%;
            border-collapse: collapse;
        }

        .envio td {
            padding: 8px 6px;
            border-bottom: 1px solid #eeeeee;
            vertical-align: top;
        }

        .resumen-wrapper {
            width: 100%;
            margin-top: 18px;
        }

        .resumen {
            width: 48%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .resumen td {
            padding: 5px 0;
            font-size: 9px;
        }

        .resumen .total td {
            border-top: 2px solid #222;
            padding-top: 9px;
            font-size: 14px;
            font-weight: bold;
        }

        .pago {
            margin-top: 18px;
            padding: 9px 10px;
            border: 1px solid #ddd;
            font-size: 8.5px;
        }

        .monto-letras {
            margin-top: 15px;
            padding: 9px;
            background: #f7f7f7;
            border-radius: 4px;
            font-size: 8.5px;
        }

        .footer {
            margin-top: 25px;
            border-top: 1px solid #ddd;
            padding-top: 12px;
            text-align: center;
            color: #777;
            font-size: 8px;
        }

        .gracias {
            font-size: 11px;
            font-weight: bold;
            color: #222;
            margin-bottom: 4px;
        }
    </style>
</head>

<body>


    <div class="header">

        <div class="logo-container">

            @if(file_exists(public_path('public/logo.png')))
                <img
                    src="{{ public_path('public/logo.png') }}"
                    class="logo"
                >
            @else
                <div class="marca">
                    B-EDEN
                </div>
            @endif

            <div class="slogan">
                Estilo que inspira confianza.
            </div>

            <div class="empresa">
                RUC: 10472160678<br>
                JR. BOLOGNESI N° 908, CONCEPCIÓN
            </div>

        </div>

        {{-- COMPROBANTE --}}

        <div class="comprobante">

            <div class="comprobante-titulo">
                BOLETA DE VENTA
            </div>

            <div class="comprobante-electronica">
                ELECTRÓNICA
            </div>

            <div class="ruc">
                R.U.C. 10472160678
            </div>

            <div class="numero">
                {{ $serie }} - {{ $correlativo }}
            </div>

        </div>

    </div>


    <div class="separador"></div>

    <div class="seccion-titulo">
        Datos del cliente
    </div>

    <table class="cliente">

        <tr>

            <td width="50%">

                <div class="etiqueta">
                    Cliente
                </div>

                <div class="valor">
                    {{ $cliente['nombre'] ?? 'CLIENTE GENERAL' }}
                </div>

            </td>

            <td width="25%">

                <div class="etiqueta">
                    Documento
                </div>

                <div class="valor">
                    {{ $cliente['num_doc'] ?? '00000000' }}
                </div>

            </td>

            <td width="25%">

                <div class="etiqueta">
                    Fecha
                </div>

                <div class="valor">
                    {{ optional($pedido->fecha_pedido)->format('d/m/Y') ?? now()->format('d/m/Y') }}
                </div>

            </td>

        </tr>

        <tr>

            <td colspan="3">

                <div class="etiqueta">
                    Dirección
                </div>

                <div class="valor">
                    {{ $pedido->direccion ?? $pedido->direccion_envio ?? 'No registrada' }}
                </div>

            </td>

        </tr>

    </table>


    <div class="separador"></div>

    <div class="seccion-titulo">
        Detalle de compra
    </div>

    <table class="productos">

        <thead>

            <tr>

                <th width="7%" class="centro">
                    #
                </th>

                <th width="45%">
                    Descripción
                </th>

                <th width="10%" class="centro">
                    Cant.
                </th>

                <th width="19%" class="derecha">
                    P. Unit.
                </th>

                <th width="19%" class="derecha">
                    Total
                </th>

            </tr>

        </thead>

        <tbody>

            @foreach($productos as $index => $producto)

                <tr>

                    <td class="centro">
                        {{ $index + 1 }}
                    </td>

                    <td>

                        <div class="descripcion">
                            {{ $producto['descripcion'] }}
                        </div>

                        @if($producto['variante'])

                            <div class="variante">
                                {{ $producto['variante'] }}
                            </div>

                        @endif

                    </td>

                    <td class="centro">
                        {{ $producto['cantidad'] }}
                    </td>

                    <td class="derecha">
                        S/ {{ number_format($producto['precio_unitario'], 2) }}
                    </td>

                    <td class="derecha">
                        S/ {{ number_format($producto['subtotal'], 2) }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>


    @if($costoEnvio > 0)

        <table class="envio">

            <tr>

                <td width="7%" class="centro">
                    {{ $productos->count() + 1 }}
                </td>

                <td width="45%">

                    <div class="descripcion">
                        Servicio de envío
                    </div>

                    @if($pedido->tipoEntrega)

                        <div class="variante">
                            {{ $pedido->tipoEntrega->nombre ?? '' }}
                        </div>

                    @endif

                </td>

                <td width="10%" class="centro">
                    1
                </td>

                <td width="19%" class="derecha">
                    S/ {{ number_format($costoEnvio, 2) }}
                </td>

                <td width="19%" class="derecha">
                    S/ {{ number_format($costoEnvio, 2) }}
                </td>

            </tr>

        </table>

    @endif


    <div class="resumen-wrapper">

        <table class="resumen">

            <tr>

                <td>
                    Productos
                </td>

                <td class="derecha">
                    S/ {{ number_format($totalProductos, 2) }}
                </td>

            </tr>

            @if($costoEnvio > 0)

                <tr>

                    <td>
                        Envío
                    </td>

                    <td class="derecha">
                        S/ {{ number_format($costoEnvio, 2) }}
                    </td>

                </tr>

            @endif

            <tr>

                <td>
                    Base imponible
                </td>

                <td class="derecha">
                    S/ {{ number_format($valorVenta, 2) }}
                </td>

            </tr>

            <tr>

                <td>
                    IGV (18%)
                </td>

                <td class="derecha">
                    S/ {{ number_format($igv, 2) }}
                </td>

            </tr>

            <tr class="total">

                <td>
                    TOTAL
                </td>

                <td class="derecha">
                    S/ {{ number_format($total, 2) }}
                </td>

            </tr>

        </table>

    </div>

    <div class="pago">

        <strong>Forma de pago:</strong>
        Contado

        &nbsp;&nbsp;&nbsp;&nbsp;

        <strong>Moneda:</strong>
        Soles (PEN)

    </div>


    <div class="footer">

        <div class="gracias">
            Gracias por comprar en B-EDEN
        </div>

        Comprobante electrónico emitido conforme a la normativa vigente.
        <br>

        B-EDEN · Moda, estilo y calidad

    </div>

</body>
</html>