<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C'Lucky - Tienda Online</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-gray-900">

    <nav class="border-b sticky top-0 bg-white z-50">
        <div class="max-w-full mx-auto px-4 sm:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo C'Lucky"
                            class="h-14 w-auto transition-transform hover:scale-105">
                    </a>
                </div>

                <div class="hidden md:flex space-x-8 font-medium uppercase text-sm tracking-widest">
                    <a href="#" class="hover:text-pink-500 transition">Mujer</a>
                    <a href="#" class="hover:text-pink-500 transition">Hombre</a>
                    <a href="#" class="hover:text-pink-500 transition">Novedades</a>
                    <a href="#" class="text-red-600 hover:text-red-700 font-bold italic">Sale</a>
                </div>

                <div class="flex items-center space-x-5">
                    <button class="hover:scale-110 transition"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg></button>
                    <a href="{{ route('login') }}" class="hover:scale-110 transition"><svg
                            xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg></a>
                    <button class="hover:scale-110 transition relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 11-8 0m-4 8h16l-1.244 6.323A2 2 0 0114.805 23H9.195a2 2 0 01-1.951-1.677L6 11z" />
                        </svg>
                        <span
                            class="absolute -top-1 -right-1 bg-black text-white text-[10px] rounded-full h-4 w-4 flex items-center justify-center">0</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Carrusel -->
    <section class="relative w-full h-[500px] bg-gray-100 overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset('images/banner-home.jpg') }}" alt="Promoción Summer Sale"
                class="w-full h-full object-cover object-center">
            <div class="absolute inset-0 bg-black/20"></div>
        </div>

        <div class="relative max-w-full mx-auto h-full px-4 sm:px-8 flex items-center">
            <div class="text-white">
                <h2 class="text-sm uppercase tracking-[0.3em] font-bold mb-2">Nueva Colección 2026</h2>
                <h1 class="text-5xl md:text-7xl font-black italic mb-6 leading-none">
                    SUMMER <br> SALE
                </h1>
                <p class="text-lg md:text-xl mb-8 max-w-md font-medium">
                    Aprovecha hasta un <span class="text-pink-400 font-bold">70% OFF</span> en prendas seleccionadas.
                </p>
                <a href="#"
                    class="inline-block bg-white text-black px-10 py-4 font-bold uppercase text-sm tracking-widest hover:bg-black hover:text-white transition-colors duration-300">
                    Comprar Ahora
                </a>
            </div>
        </div>
    </section>

</body>

</html>
