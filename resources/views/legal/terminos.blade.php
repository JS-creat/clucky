@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-12">
    <h1 class="text-2xl sm:text-3xl font-bold mb-6">Términos y Condiciones</h1>
    <p class="text-sm text-gray-500 mb-8">Última actualización: {{ now()->format('d/m/Y') }}</p>

    <div class="space-y-6 text-sm sm:text-base text-gray-700">
        <section>
            <h2 class="font-semibold text-lg mb-2">1. Aceptación de los términos</h2>
            <p>Al acceder y utilizar el sitio web de B-EDEN, usted acepta cumplir con los presentes Términos y Condiciones. Si no está de acuerdo, le recomendamos no utilizar nuestros servicios.</p>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">2. Productos y precios</h2>
            <p>Los precios mostrados en el sitio incluyen los impuestos correspondientes y están sujetos a modificación sin previo aviso. Nos reservamos el derecho de corregir errores en precios o descripciones.</p>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">3. Proceso de compra y pagos</h2>
            <p>Las compras se confirman una vez procesado el pago a través de los métodos habilitados en el sitio. El cliente recibirá una confirmación por correo electrónico.</p>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">4. Envíos y entregas</h2>
            <p>Los plazos de entrega son estimados y pueden variar según la ubicación del cliente y disponibilidad del producto.</p>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">5. Cambios y devoluciones</h2>
            <p>El cliente cuenta con [X] días desde la recepción del producto para solicitar un cambio o devolución, siempre que el producto se encuentre en las condiciones originales.</p>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">6. Propiedad intelectual</h2>
            <p>Todo el contenido del sitio (imágenes, textos, logotipos) es propiedad de B-EDEN y no puede ser reproducido sin autorización.</p>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">7. Modificaciones</h2>
            <p>B-EDEN se reserva el derecho de modificar estos términos en cualquier momento. Los cambios serán publicados en esta misma página.</p>
        </section>

        <section>
            <h2 class="font-semibold text-lg mb-2">8. Contacto</h2>
            <p>Para consultas relacionadas a estos términos, puede escribirnos al WhatsApp +51 964 374 401 o visitarnos en Jr. Bolognesi N° 908, Concepción.</p>
        </section>
    </div>
</div>
@endsection
