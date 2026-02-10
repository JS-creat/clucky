<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white">
    <div class="flex-shrink-0 flex items-center">
        <a href="{{ url('/') }}">
            <img src="{{ asset('images/logo.jpg') }}" alt="Logo C'Lucky"
                class="h-14 w-auto transition-transform hover:scale-105">
        </a>
    </div>
    <main class="max-w-6xl mx-auto px-4 sm:px-8 py-10">
        <div class="flex flex-col lg:flex-row gap-8 items-start">

            <div class="w-full lg:w-[55%] space-y-4">
                <div class="aspect-[4/5] bg-gray-50 overflow-hidden rounded-md border border-gray-100">
                    <img id="view-principal" src="{{ asset('productos/' . $producto->imagen) }}"
                        class="w-full h-full object-contain p-4 transition-opacity duration-300"
                        alt="{{ $producto->nombre_producto }}">
                </div>

                <div class="flex gap-2">
                    <div class="w-20 h-24 border border-black p-1 cursor-pointer thumbnail-btn"
                        onclick="cambiarImagen(this, '{{ asset('productos/' . $producto->imagen) }}')">
                        <img src="{{ asset('productos/' . $producto->imagen) }}" class="w-full h-full object-cover">
                    </div>

                    @if($producto->galeria)
                        @foreach($producto->galeria as $foto)
                            <div class="w-20 h-24 border border-gray-200 p-1 cursor-pointer thumbnail-btn hover:border-gray-400 transition"
                                onclick="cambiarImagen(this, '{{ asset('productos/' . $foto) }}')">
                                <img src="{{ asset('productos/' . $foto) }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="w-full lg:w-[45%] sticky top-24">
                <div class="border-b pb-4">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-tighter">{{ $producto->marca }}</p>
                    <h1 class="text-xl md:text-2xl font-black text-gray-900 uppercase italic leading-none mt-1">
                        {{ $producto->nombre_producto }}
                    </h1>
                    <p class="text-[10px] text-gray-400 mt-2">SKU: {{ $producto->codigo }}</p>
                </div>

                <div class="py-6 bg-gray-50/50 px-4 mt-4">
                    @if($producto->precio_oferta)
                        <div class="flex items-baseline gap-3">
                            <span class="text-3xl font-black text-[#f50057]">S/
                                {{ number_format($producto->precio_oferta, 2) }}</span>
                            <span class="text-sm text-gray-400 line-through">S/
                                {{ number_format($producto->precio, 2) }}</span>
                            <span class="bg-[#f50057] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                -{{ round((($producto->precio - $producto->precio_oferta) / $producto->precio) * 100) }}%
                            </span>
                        </div>
                    @else
                        <span class="text-3xl font-black text-gray-900">S/ {{ number_format($producto->precio, 2) }}</span>
                    @endif
                    <p class="text-[10px] text-gray-500 mt-2">Precios exclusivos online</p>
                </div>

                <div class="mt-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-xs font-bold uppercase tracking-widest">Selecciona tu talla:</h3>
                        <a href="#" class="text-[10px] underline text-gray-400 uppercase">Guía de tallas</a>
                    </div>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach(explode(',', $producto->talla) as $talla)
                            <button
                                class="talla-btn border border-gray-200 py-3 text-xs font-bold hover:border-black transition-all uppercase"
                                onclick="selectTalla(this)">
                                {{ trim($talla) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8">
                    <button
                        class="w-full bg-gray-700 text-white py-4 font-bold uppercase text-sm tracking-[0.2em] shadow-lg hover:bg-gray-900 transition-all duration-300 flex items-center justify-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 11-8 0m-4 8h16l-1.244 6.323A2 2 0 0114.805 23H9.195a2 2 0 01-1.951-1.677L6 11z" />
                        </svg>
                        Añadir a la bolsa
                    </button>
                </div>

                <div class="mt-10 border-t pt-6 space-y-4 text-sm text-gray-600">
                    <details class="group open:bg-gray-50 transition-all cursor-pointer p-2 rounded-md" open>
                        <summary class="font-bold uppercase text-xs list-none flex justify-between items-center">
                            Descripción del producto
                            <span class="group-open:rotate-180 transition-transform">↓</span>
                        </summary>
                        <p class="mt-3 text-[13px] leading-relaxed italic">
                            {{ $producto->descripcion }}
                        </p>
                    </details>
                </div>
            </div>
        </div>
    </main>

    <script>
        function cambiarImagen(elemento, url) {
            // 1. Cambiar la imagen principal con un efecto suave
            const principal = document.getElementById('view-principal');
            principal.style.opacity = '0';

            setTimeout(() => {
                principal.src = url;
                principal.style.opacity = '1';
            }, 150);

            // 2. Resaltar la miniatura seleccionada (borde negro)
            document.querySelectorAll('.thumbnail-btn').forEach(btn => {
                btn.classList.remove('border-black');
                btn.classList.add('border-gray-200');
            });
            elemento.classList.add('border-black');
            elemento.classList.remove('border-gray-200');
        }
    </script>
</body>

</html>
