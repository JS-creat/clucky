<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago fallido – B-EDEN</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body class="bg-stone-50 font-sans text-gray-900 antialiased min-h-screen flex flex-col">

    {{-- Navbar --}}
    <nav class="bg-gray-900 border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 flex items-center h-16">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/logo.jpg') }}"
                     class="h-9 w-auto brightness-0 invert opacity-90 hover:opacity-100 transition-opacity">
            </a>
        </div>
    </nav>

    {{-- Contenido --}}
    <div class="flex-1 flex items-center justify-center px-4 py-16">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-10 max-w-md w-full text-center">

            {{-- Icono error --}}
            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>

            <h1 class="text-3xl font-display font-semibold text-gray-900 mb-2">
                Pago no procesado
            </h1>

            <p class="text-gray-500 text-sm leading-relaxed mb-8">
                No pudimos completar tu pago. Verifica la información de tu método de pago
                o intenta nuevamente en unos minutos.
            </p>

            {{-- Info --}}
            <div class="bg-gray-50 rounded-2xl p-5 text-left space-y-3 mb-8">

                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Cliente</span>
                    <span class="font-semibold">
                        {{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Correo</span>
                    <span class="font-semibold">
                        {{ auth()->user()->correo }}
                    </span>
                </div>

                @if(auth()->user()->telefono)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Teléfono</span>
                    <span class="font-semibold">
                        {{ auth()->user()->telefono }}
                    </span>
                </div>
                @endif

                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Estado</span>
                    <span class="font-semibold text-red-500">
                        Pago rechazado
                    </span>
                </div>

            </div>

            {{-- Acciones --}}
            <div class="space-y-3">

                <a href="{{ url('/checkout') }}"
                   class="block w-full bg-gray-900 text-white rounded-2xl py-3.5 text-sm font-semibold hover:bg-gray-800 transition-colors">
                    Intentar nuevamente
                </a>

                <a href="{{ url('/') }}"
                   class="block w-full border border-gray-200 rounded-2xl py-3.5 text-sm font-semibold hover:bg-gray-50 transition-colors">
                    Volver al inicio
                </a>

            </div>

        </div>
    </div>

</body>
</html>
