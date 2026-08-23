<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>
        Boleta {{ $serie }}-{{ $correlativo }}
    </title>

    <style>

        @page {
            margin: 35px 40px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #222;
            font-size: 11px;
            margin: 0;
        }

        .header {
            width: 100%;
            margin-bottom: 25px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .slogan {
            color: #666;
            font-size: 10px;
        }

        .empresa {
            font-size: 10px;
            line-height: 1.5;
            margin-top: 12px;
        }

        .comprobante {
            border: 1px solid #999;
            border-radius: 4px;
            text-align: center;
            padding: 12px;
        }

        .comprobante-titulo {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .comprobante-electronica {
            font-size: 9px;
            letter-spacing: 3px;
            margin-top: 3px;
        }

        .numero {
            font-size: 17px;
            font-weight: bold;
            margin-top: 10px;
        }

        .separador {
            border-top: 1px solid #ddd;
            margin: 18px 0;
        }

        .seccion-titulo {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .cliente {
            width: 100%;
            border-collapse: collapse;
            background: #f7f7f7;
            border-radius: 4px;
        }

        .cliente td {
            padding: 7px 9px;
            vertical-align: top;
        }

        .etiqueta {
            color: #777;
            font-size: 9px;
            text-transform: uppercase;
        }

        .valor {
            font-size: 10px;
            font-weight: bold;
            margin-top: 2px;
        }

        .productos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .productos thead {
            background: #eeeeee;
        }

        .productos th {
            padding: 9px 7px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            border-bottom: 1px solid #ccc;
        }

        .productos td {
            padding: 9px 7px;
            border-bottom: 1px solid #eeeeee;
            vertical-align: top;
        }

        .descripcion {
            font-weight: bold;
        }

        .variante {
            color: #777;
            font-size: 9px;
            margin-top: 3px;
        }

        .derecha {
            text-align: right !important;
        }

        .centro {
            text-align: center !important;
        }

        .envio {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .envio td {
            padding: 8px 7px;
            border-bottom: 1px solid #eeeeee;
        }

        .resumen-wrapper {
            width: 100%;
            margin-top: 20px;
        }

        .resumen {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .resumen td {
            padding: 5px 0;
        }

        .resumen .total {
            border-top: 2px solid #222;
            font-size: 15px;
            font-weight: bold;
            padding-top: 10px;
        }

        .igv-info {
            width: 100%;
            margin-top: 20px;
            font-size: 9px;
            color: #666;
        }

        .monto-letras {
            margin-top: 18px;
            padding: 10px;
            background: #f7f7f7;
            border-radius: 4px;
            font-size: 9px;
        }

        .footer {
            margin-top: 35px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            text-align: center;
            color: #777;
            font-size: 9px;
        }

        .gracias {
            font-size: 12px;
            font-weight: bold;
            color: #222;
            margin-bottom: 5px;
        }

    </style>
</head>

<body>

    {{-- ENCABEZADO --}}
    <div class="header">

        <table class="header-table">

            <tr>

                <td width="58%">

                    <div class="logo">
                        B-EDEN
                    </div>

                    <div class="slogan">
                        Moda · Estilo · Calidad
                    </div>

                    <div class="empresa">

                        <strong>DIAZ ZEA EDUARDO ARTURO</strong><br>

                        RUC: 10472160678<br>

                        JR. BOLOGNESI N° 908,
                        CONCEPCION

                    </div>

                </td>

                <td width="42%">

                    <div class="comprobante">

                        <div class="comprobante-titulo">
                            BOLETA DE VENTA
                        </div>

                        <div class="comprobante-electronica">
                            ELECTRÓNICA
                        </div>

                        <div style="margin-top: 8px;">
                            R.U.C. 10472160678
                        </div>

                        <div class="numero">
                            {{ $serie }}-{{ $correlativo }}
                        </div>

                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- CLIENTE --}}
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


    {{-- PRODUCTOS --}}
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


    {{-- ENVÍO --}}
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


    {{-- RESUMEN --}}
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

            <tr>

                <td class="total">
                    TOTAL
                </td>

                <td class="derecha total">
                    S/ {{ number_format($total, 2) }}
                </td>

            </tr>

        </table>

    </div>


    {{-- INFORMACIÓN DE PAGO --}}
    <div class="igv-info">

        <strong>Forma de pago:</strong>
        Contado

        &nbsp;&nbsp;&nbsp;

        <strong>Moneda:</strong>
        Soles (PEN)

    </div>


    {{-- PIE --}}
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