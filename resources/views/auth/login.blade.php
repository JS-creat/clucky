<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - C Lucky</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen flex flex-col">

<!--Navbar-->
<header class="border-b bg-white py-4">
    <div class="flex items-center pl-4">
        <img
            src="{{ asset('images/logo.jpg') }}"
            alt="Logo C Lucky"
            class="h-12 object-contain"
        >
    </div>
</header>

<!-- CONTENT -->
<main class="flex-1 flex justify-center items-center bg-gray-50">

    <div class="grid md:grid-cols-2 gap-12 max-w-4xl w-full px-6">

        <!-- LOGIN -->
        <div class="bg-white p-8 rounded shadow">

            <h2 class="text-lg font-semibold mb-4">Iniciar sesión</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <input
                    type="email"
                    name="correo"
                    placeholder="Correo electrónico"
                    class="w-full border px-4 py-2 mb-3 rounded"
                    required
                >

                <input
                    type="password"
                    name="contrasena"
                    placeholder="Contraseña"
                    class="w-full border px-4 py-2 mb-4 rounded"
                    required
                >

                <button class="w-full bg-black text-white py-2 rounded">
                    Iniciar sesión
                </button>
            </form>

        </div>

        <!-- REGISTER -->
        <div class="bg-white p-8 rounded shadow text-center">
            <h2 class="text-lg font-semibold mb-4">¿Eres nuevo en C’Lucky?</h2>
            <p class="text-gray-600 mb-6">
                Crea una cuenta para comprar más rápido y acceder a ofertas especiales
            </p>

            <a href="/register" class="bg-black text-white px-6 py-2 rounded inline-block">
                Crear cuenta
            </a>
        </div>

    </div>
</main>

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
