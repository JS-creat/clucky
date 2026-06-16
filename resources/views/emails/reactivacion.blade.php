<x-mail::message>
# ¡Hola, {{ $usuario->nombres }}!

Hace un mes que no sabemos de ti y en nuestra tienda te extrañamos mucho. Queremos que regreses, así que te preparamos una sorpresa.

Usa el siguiente cupón en tu próxima compra para obtener un **10% de descuento** en todo el carrito:

<x-mail::panel>
**REGRESA10**
</x-mail::panel>

<x-mail::button :url="config('app.url')">
Ir a la Tienda Online
</x-mail::button>

¡Te esperamos de vuelta!,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>
