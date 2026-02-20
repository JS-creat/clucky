<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login - C Lucky</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-50">

    <!--Navbar-->
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
    <!-- CONTENT -->
    <main class="py-20 flex justify-center">

        <div class="flex flex-col md:flex-row gap-8 max-w-5xl w-full px-4">

            <!-- LOGIN -->
            <div class="bg-white p-10 md:p-12 rounded shadow">

                <h2 class="text-lg font-semibold mb-4">Iniciar sesión</h2>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <input type="email" name="correo" placeholder="Correo electrónico"
                        class="w-full border px-4 py-2 mb-3 rounded" required>

                    <input type="password" name="contrasena" placeholder="Contraseña"
                        class="w-full border px-4 py-2 mb-4 rounded" required>

                    <button class="w-full bg-black text-white py-2 rounded">
                        Iniciar sesión
                    </button>
                </form>

            </div>

            <!-- REGISTER -->
            <div class="bg-white p-10 md:p-12 rounded shadow text-center">
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
        <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-8 justify-items-center"> 

            <div class="w-full text-left"> 
                <h3 class="text-base font-semibold mb-4">
                    Servicio al cliente
                </h3>
                <div class="space-y-2 text-sm">
                    <p>Preguntas frecuentes</p>
                    <p>Métodos de pago</p>
                    <p>Cambios y devoluciones</p>
                </div>
            </div>

            <div class="w-full text-left">
                <h3 class="text-base font-semibold mb-4">
                    Contáctanos
                </h3>
                <div class="space-y-2 text-sm">
                    <p>cluckyropaconcepcion@gmail.com</p>
                    <p>Teléfono: +51 964 374 401</p>
                    <p>9:00 am a 8:00 pm de lunes a viernes</p>
                    <p>Jr. Bolognesi N° 908, Concepción</p>
                </div>
            </div>

            <div class="w-full text-left">
                <h3 class="text-base font-semibold mb-4">
                    Acerca de nosotros
                </h3>
                <div class="space-y-2 text-sm">
                    <p>Quiénes somos</p>
                    <p>Términos y condiciones</p>
                </div>
                
                <div class="flex space-x-4 mt-4"> 
                    <a href="https://www.facebook.com/Lucibet" target="_blank" rel="noopener noreferrer" 
                    class="text-white hover:text-gray-300 transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/cluckyropa/?fbclid=IwY2xjawQFnChleHRuA2FlbQIxMABicmlkETJheWMzNVhpVjR2Z2tqemhtc3J0YwZhcHBfaWQQMjIyMDM5MTc4ODIwMDg5MgABHqtJOvKcDAf2tV7iZJ7dzap-9Jk9N5Uu6Zo04ngSrm3D-9U8MWyDVzKUNieH_aem_MZtk7gBLhQV-MLABQnNcnw" target="_blank" rel="noopener noreferrer"
                    class="text-white hover:text-gray-300 transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>

        </div>

        <p class="text-center text-xs text-gray-400 mt-8">
            © 2026 C’Lucky. Todos los derechos reservados.
        </p>
    </footer>

</body>

</html>
