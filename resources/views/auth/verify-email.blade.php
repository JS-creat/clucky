<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Verificar email - C Lucky</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">

    <nav class="border-b sticky top-0 bg-white z-50">
        <div class="max-w-full mx-auto px-4 sm:px-8">
            <div class="flex justify-between h-20 items-center">

                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo C'Lucky"
                            class="h-14 w-auto transition-transform hover:scale-105">
                    </a>
                </div>

                <div class="hidden md:flex"></div>

            </div>
        </div>
    </nav>

    <main class="py-10 md:py-20 flex justify-center flex-grow">
        <div class="w-full max-w-md px-4">
            <div class="bg-white p-6 md:p-8 rounded shadow-lg">

                <h2 class="text-xl md:text-2xl font-semibold text-center mb-4">
                    Verifica tu correo
                </h2>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-green-700 text-sm text-center font-medium">
                            {{ __('Se ha enviado un nuevo enlace de verificación a tu correo electrónico.') }}
                        </p>
                    </div>
                @endif

                <div class="bg-gray-100 border border-gray-200 rounded-lg p-5 mb-6">
                    <p class="text-gray-700 text-sm leading-relaxed text-justify">
                        {{ __('¡Gracias por registrarte') }}, <strong>{{ auth()->user()->nombres }}</strong>!
                    </p>
                    <p class="text-gray-700 text-sm leading-relaxed mt-2 text-justify">
                        {{ __('Antes de comenzar, verifica tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar a') }} 
                        <strong class="text-gray-900">{{ auth()->user()->correo ?? auth()->user()->email }}</strong>.
                    </p>
                    <p class="text-gray-700 text-sm leading-relaxed mt-2 text-justify">
                        {{ __('Si no recibiste el correo electrónico, revisa tu bandeja de spam o haz clic en el botón de abajo para reenviarlo.') }}
                    </p>
                </div>

                <div class="space-y-4">
                    <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                        @csrf
                        <button type="submit" 
                            class="w-full bg-black text-white py-3 rounded-lg text-base font-bold hover:bg-gray-800 transition cursor-pointer">
                            Reenviar correo de verificación
                        </button>
                    </form>
                </div>

                <!-- Formulario para logout con redirección a registro -->
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>

                <p class="text-center text-xs text-gray-500 mt-6">
                    ¿No eres tú? 
                    <button onclick="logoutAndRedirect()" 
                            class="text-black underline font-medium hover:text-gray-600 transition cursor-pointer bg-transparent border-0 p-0">
                        Registrarse
                    </button>
                </p>

            </div>
        </div>
    </main>

    <footer class="bg-black text-white py-12">
        <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-8 text-sm">
            <div>
                <h3 class="font-semibold mb-3 uppercase tracking-wider">Servicio al cliente</h3>
                <ul class="space-y-2 text-gray-400">
                    <li>Preguntas frecuentes</li>
                    <li>Formas de pago</li>
                    <li>Métodos de envío</li>
                    <li>Devoluciones</li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold mb-3 uppercase tracking-wider">Contáctanos</h3>
                <ul class="space-y-2 text-gray-400">
                    <li>contacto@clucky.com</li>
                    <li>+51 999 999 999</li>
                    <li>Lunes a viernes</li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold mb-3 uppercase tracking-wider">Acerca de</h3>
                <ul class="space-y-2 text-gray-400">
                    <li>Quiénes somos</li>
                    <li>Términos y condiciones</li>
                    <li>Privacidad</li>
                </ul>
            </div>
        </div>
        <p class="text-center text-xs text-gray-500 mt-12">
            © 2026 C’Lucky. Todos los derechos reservados.
        </p>
    </footer>

    <script>
    function logoutAndRedirect() {
        // Primero hacemos logout vía fetch
        fetch('{{ route('logout') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        }).then(() => {
            // Después de logout exitoso, redirigimos al registro
            window.location.href = '{{ route('register') }}';
        }).catch(() => {
            // Si hay error igual intentamos redirigir
            window.location.href = '{{ route('register') }}';
        });
    }
    </script>

</body>

</html>