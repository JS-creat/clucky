<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro - C Lucky</title>
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

                <h2 class="text-xl md:text-2xl font-semibold text-center mb-8">
                    Registrarse
                </h2>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-4">
                        <input class="w-full border rounded-lg px-4 py-3 text-base" type="text" name="nombres"
                            placeholder="Nombres" value="{{ old('nombres') }}" required autofocus>
                        @error('nombres')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <input class="w-full border rounded-lg px-4 py-3 text-base" type="text" name="apellidos"
                            placeholder="Apellidos" value="{{ old('apellidos') }}" required>
                        @error('apellidos')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <input class="w-full border rounded-lg px-4 py-3 text-base" type="email" name="correo"
                            placeholder="Correo electrónico" value="{{ old('correo') }}" required>
                        @error('correo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="relative mb-1">
                        <input id="password" class="w-full border rounded-lg px-4 py-3 text-base pr-12" type="password"
                            name="password" placeholder="Contraseña" required>
                        <button type="button" onclick="togglePass('password', 'eye-password')"
                            class="absolute right-3 top-3.5 text-gray-400 hover:text-black transition-colors">
                            <svg id="eye-password" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mb-4 pl-2">{{ $message }}</p>
                    @enderror

                    <div class="relative mb-6">
                        <input id="password_confirmation" class="w-full border rounded-lg px-4 py-3 text-base pr-12"
                            type="password" name="password_confirmation" placeholder="Confirmar contraseña" required>
                        <button type="button" onclick="togglePass('password_confirmation', 'eye-confirmation')"
                            class="absolute right-3 top-3.5 text-gray-400 hover:text-black transition-colors">
                            <svg id="eye-confirmation" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </div>

                    <button
                        class="w-full bg-black text-white py-3 rounded-lg text-base font-bold hover:bg-gray-800 transition">
                        Crear cuenta
                    </button>

                </form>

                <p class="text-center text-sm text-gray-600 mt-6">
                    ¿Ya tienes una cuenta?
                    <a href="{{ route('login') }}" 
                       class="text-black underline font-medium hover:text-gray-600 transition cursor-pointer">
                        Iniciar sesión
                    </a>
                </p>

            </div>
        </div>
    </main>

    <script>
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
            }
        }
    </script>
    
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