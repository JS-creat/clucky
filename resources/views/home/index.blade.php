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
                    <a href="{{ route('home') }}"
                        class="{{ !request()->has('categoria') && !request()->has('promocion') ? 'text-pink-600 font-bold' : '' }}">
                        Todo
                    </a>

                    <a href="{{ route('home', ['categoria' => 'Mujer']) }}"
                        class="{{ request('categoria') == 'Mujer' ? 'text-pink-600 font-bold' : '' }}">
                        Mujer
                    </a>

                    <a href="{{ route('home', ['categoria' => 'Hombre']) }}"
                        class="{{ request('categoria') == 'Hombre' ? 'text-pink-600 font-bold' : '' }}">
                        Hombre
                    </a>

                    <a href="{{ route('home', ['promocion' => 1]) }}"
                        class="{{ request()->has('promocion') ? 'text-red-600 font-bold italic' : '' }}">
                        Promociones
                    </a>

                </div>

                <div class="flex items-center space-x-5">
                    <button class="hover:scale-110 transition"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg></button>
                    @auth
                        <a href="{{ route('perfil.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </a>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="hover:scale-110 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </a>
                    @endguest

                    <a href="{{ route('carrito.index') }}" class="hover:scale-110 transition relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 11-8 0m-4 8h16l-1.244 6.323A2 2 0 0114.805 23H9.195a2 2 0 01-1.951-1.677L6 11z" />
                        </svg>
                        <span
                            class="absolute -top-1 -right-1 bg-[#f50057] text-white text-[10px] rounded-full h-4 w-4 flex items-center justify-center font-bold">
                            {{ session('carrito') ? count(session('carrito')) : 0 }}
                        </span>
                    </a>
                </div>

                @auth
                    @if(auth()->user()->id_rol == 1)
                        <a href="{{ route('admin.dashboard') }}"
                            class="text-sm font-bold text-gray-900 hover:underline">
                            Panel Admin
                        </a>
                    @endif
                @endauth
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

    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-12">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($productos as $item)
                <a href="{{ url('/producto/' . $item->id_producto) }}"
                    class="group cursor-pointer block bg-white border border-gray-200 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">

                    <div class="relative aspect-[3/4] bg-gray-100 overflow-hidden mb-4 rounded-lg">
                        @if($item->precio_oferta)
                            <span
                                class="absolute top-0 left-0 bg-red-600 text-white text-[10px] font-bold px-2 py-1 z-10 uppercase">
                                Oferta
                            </span>
                        @endif
                        <img src="{{ asset('productos/' . $item->imagen) }}" alt="{{ $item->nombre_producto }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] text-gray-400 uppercase font-bold">{{ $item->marca }}</p>
                        <h3 class="text-sm font-medium text-gray-800">{{ $item->nombre_producto }}</h3>
                        <div class="h-[1px] bg-gray-100 my-2"></div>

                        <div class="flex flex-col">
                            @if($item->precio_oferta)
                                <span class="text-sm font-black text-[#f50057]">
                                    S/ {{ number_format($item->precio_oferta, 2) }}
                                </span>
                                <span class="text-[11px] text-gray-400 line-through">
                                    S/ {{ number_format($item->precio, 2) }}
                                </span>
                            @else
                                <span class="text-sm font-bold text-black">
                                    S/ {{ number_format($item->precio, 2) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

    </div>

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
