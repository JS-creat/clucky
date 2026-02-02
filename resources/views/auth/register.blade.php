<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro - C Lucky</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100">

    <!-- NAVBAR -->
    <header class="border-b bg-white py-4">
        <div class="flex items-center pl-4">
            <img src="{{ asset('images/logo.jpg') }}" alt="Logo C Lucky" class="h-12 md:h-14">
        </div>
    </header>

    <!-- MAIN -->
    <main class="py-10 md:py-20 flex justify-center">

        <div class="w-full max-w-md px-4">

            <div class="bg-white p-2 md:p-4 rounded shadow">

                <h2 class="text-xl md:text-2xl font-semibold text-center mb-8">
                    Registrarse
                </h2>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <input class="w-full border rounded-lg px-4 py-3 mb-4 text-base" type="text" name="nombres"
                        placeholder="Nombres" required>

                    <input class="w-full border rounded-lg px-4 py-3 mb-4 text-base" type="text" name="apellidos"
                        placeholder="Apellidos" required>

                    <input class="w-full border rounded-lg px-4 py-3 mb-4 text-base" type="email" name="correo"
                        placeholder="Correo electrónico" required>

                    <input class="w-full border rounded-lg px-4 py-3 mb-4 text-base" type="password" name="password"
                        placeholder="Contraseña" required>

                    <input class="w-full border rounded-lg px-4 py-3 mb-6 text-base" type="password"
                        name="password_confirmation" placeholder="Confirmar contraseña" required>

                    <button class="w-full bg-black text-white py-3 rounded-lg text-base">
                        Crear cuenta
                    </button>

                </form>

                <p class="text-center text-sm text-gray-600 mt-6">
                    ¿Ya tienes cuenta?
                    <a href="{{ route('login') }}" class="underline">Inicia sesión</a>
                </p>

            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <!-- FOOTER -->
    <footer class="bg-black text-white py-12 mt-auto">
        <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-8 text-sm">

            <div>
                <h3 class="font-semibold mb-3">Servicio al cliente</h3>
                <p>Preguntas frecuentes</p>
                <p>Formas de pago</p>
                <p>Métodos de envío</p>
                <p>Devoluciones</p>
            </div>

            <div>
                <h3 class="font-semibold mb-3">Contáctanos</h3>
                <p>contacto@clucky.com</p>
                <p>+51 999 999 999</p>
                <p>Lunes a viernes</p>
            </div>

            <div>
                <h3 class="font-semibold mb-3">Acerca de</h3>
                <p>Quiénes somos</p>
                <p>Términos y condiciones</p>
                <p>Privacidad</p>
            </div>

        </div>

        <p class="text-center text-xs text-gray-400 mt-8">
            © 2026 C’Lucky. Todos los derechos reservados.
        </p>
    </footer>

</body>

</html>
