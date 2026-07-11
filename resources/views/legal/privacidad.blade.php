@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-12">
    <h1 class="text-2xl sm:text-3xl font-bold mb-6">Política de Privacidad</h1>
    <p class="text-sm text-gray-500 mb-8">
        Última actualización: {{ now()->format('d/m/Y') }}
    </p>

    <div class="space-y-6 text-sm sm:text-base text-gray-700">

        <section>
            <h2 class="font-semibold text-lg mb-2">1. Introducción</h2>
            <p>
                En B-EDEN valoramos la privacidad de nuestros clientes. Esta Política de
                Privacidad explica cómo recopilamos, utilizamos y protegemos la información
                personal que usted proporciona al utilizar nuestro sitio web.
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">2. Información que recopilamos</h2>
            <p>Podemos recopilar la siguiente información:</p>

            <ul class="list-disc pl-6 mt-2 space-y-1">
                <li>Datos personales.</li>
                <li>Correo electrónico.</li>
                <li>Número de teléfono.</li>
                <li>Dirección de envío.</li>
                <li>Información relacionada con los pedidos realizados.</li>
            </ul>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">3. Uso de la información</h2>
            <p>La información recopilada se utiliza para:</p>

            <ul class="list-disc pl-6 mt-2 space-y-1">
                <li>Procesar y gestionar sus pedidos.</li>
                <li>Coordinar envíos y entregas.</li>
                <li>Brindar atención al cliente.</li>
                <li>Enviar notificaciones relacionadas con sus compras.</li>
                <li>Mejorar nuestros productos y servicios.</li>
            </ul>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">4. Protección de la información</h2>
            <p>
                B-EDEN adopta medidas de seguridad razonables para proteger la información
                personal de los usuarios contra accesos no autorizados, pérdida o alteración.
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">5. Compartición de información</h2>
            <p>
                No vendemos ni compartimos la información personal de nuestros clientes con
                terceros, excepto cuando sea necesario para procesar un pedido, realizar un
                envío o cuando la ley lo requiera.
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">6. Derechos del usuario</h2>
            <p>
                El usuario puede solicitar la actualización, corrección o eliminación de sus
                datos personales comunicándose con B-EDEN a través de nuestros canales de
                atención.
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">7. Cambios en esta política</h2>
            <p>
                Nos reservamos el derecho de actualizar esta Política de Privacidad cuando sea
                necesario. Cualquier modificación será publicada en esta misma página.
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">8. Contacto</h2>
            <p>
                Si tiene consultas sobre esta Política de Privacidad, puede comunicarse con
                nosotros mediante nuestro WhatsApp <strong>+51 964 374 401</strong> o
                visitarnos en <strong>Jr. Bolognesi N.° 908, Concepción</strong>.
            </p>
        </section>

    </div>
</div>
@endsection
