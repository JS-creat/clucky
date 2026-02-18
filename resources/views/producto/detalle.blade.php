<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->nombre_producto }} - C'Lucky</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Suavizar el cambio de imagen principal */
        #view-principal {
            transition: opacity 0.2s ease-in-out;
        }

        .thumbnail-active {
            border-color: black !important;
            border-width: 2px !important;
        }
    </style>
</head>

<body class="bg-white text-gray-900 antialiased">

    <nav class="border-b sticky top-0 bg-white z-50">
        <div class="max-w-full mx-auto px-4 sm:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo C'Lucky"
                            class="h-14 w-auto transition-transform hover:scale-105">
                    </a>
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

                    {{-- Icono de Carrito con Contador Dinámico --}}
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
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-12 items-start">

            <div class="w-full lg:w-[60%] space-y-4">
                <div class="aspect-[4/5] bg-gray-50 overflow-hidden rounded-sm border border-gray-100 group">
                    <img id="view-principal" src="{{ asset('productos/' . $producto->imagen) }}"
                        class="w-full h-full object-contain p-4 transition-all duration-500 group-hover:scale-105"
                        alt="{{ $producto->nombre_producto }}">
                </div>

                <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                    <div class="w-20 h-24 flex-shrink-0 border-2 border-black p-1 cursor-pointer thumbnail-btn"
                        onclick="cambiarImagen(this, '{{ asset('productos/' . $producto->imagen) }}')">
                        <img src="{{ asset('productos/' . $producto->imagen) }}" class="w-full h-full object-cover">
                    </div>

                    @if($producto->galeria)
                        @foreach($producto->galeria as $foto)
                            <div class="w-20 h-24 flex-shrink-0 border border-gray-200 p-1 cursor-pointer thumbnail-btn hover:border-gray-400 transition"
                                onclick="cambiarImagen(this, '{{ asset('productos/' . $foto) }}')">
                                <img src="{{ asset('productos/' . $foto) }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="w-full lg:w-[40%] sticky top-32">
                <div class="border-b pb-6">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">
                        {{ $producto->marca }}</p>
                    <h1 class="text-3xl font-black text-gray-900 uppercase italic leading-none">
                        {{ $producto->nombre_producto }}
                    </h1>
                    <p class="text-[10px] text-gray-400 mt-4 tracking-widest">SKU: {{ $producto->codigo }}</p>
                </div>

                <div class="py-8">
                    @if($producto->precio_oferta)
                        <div class="flex items-center gap-4">
                            <span class="text-4xl font-black text-[#f50057]">S/
                                {{ number_format($producto->precio_oferta, 2) }}</span>
                            <span class="text-lg text-gray-400 line-through font-medium">S/
                                {{ number_format($producto->precio, 2) }}</span>
                            <span
                                class="bg-[#f50057] text-white text-[11px] font-bold px-3 py-1 rounded-full">-{{ round((($producto->precio - $producto->precio_oferta) / $producto->precio) * 100) }}%</span>
                        </div>
                    @else
                        <span class="text-4xl font-black text-gray-900">S/ {{ number_format($producto->precio, 2) }}</span>
                    @endif
                    <p class="text-[10px] text-gray-500 mt-3 font-medium uppercase tracking-tighter">Precios exclusivos
                        online • Envío disponible</p>
                </div>

                @php
                    $colorPalette = ['negro' => '#000000', 'blanco' => '#FFFFFF', 'rojo' => '#DC2626', 'azul' => '#2563EB', 'verde' => '#16A34A', 'gris' => '#4B5563', 'beige' => '#F5F5DC', 'rosa' => '#DB2777'];
                    $coloresDisponibles = $producto->color ? explode(',', $producto->color) : [];
                @endphp

                <div class="mb-8">
                    <h3 class="text-xs font-bold uppercase tracking-widest mb-4">Color: <span id="color-seleccionado"
                            class="text-gray-400 font-normal ml-2">Selecciona uno</span></h3>
                    <div class="flex flex-wrap gap-4">
                        @foreach($coloresDisponibles as $col)
                            @php $nombreLimpio = trim(strtolower($col));
                            $hex = $colorPalette[$nombreLimpio] ?? '#cbd5e1'; @endphp
                            <button type="button" onclick="seleccionarColor(this, '{{ trim($col) }}')"
                                class="group relative w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center transition-all hover:scale-110 color-option-btn"
                                title="{{ trim($col) }}">
                                <span class="w-7 h-7 rounded-full shadow-inner"
                                    style="background-color: {{ $hex }};"></span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xs font-bold uppercase tracking-widest">Talla:</h3>
                    </div>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach(explode(',', $producto->talla) as $talla)
                            <button
                                class="talla-btn border border-gray-200 py-4 text-[11px] font-black hover:border-black transition-all uppercase"
                                onclick="selectTalla(this)">
                                {{ trim($talla) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Sección del Botón Corregida --}}
                <div class="mt-10">
                    <form id="form-carrito" action="{{ route('carrito.add', $producto->id_producto) }}" method="POST">

                        <input type="hidden" name="color" id="input-color">
                        <input type="hidden" name="talla" id="input-talla">

                        @if($errors->any())
                            <div class="bg-red-100 text-red-700 p-2 mb-3 rounded">
                                Debes seleccionar color y talla.
                            </div>
                        @endif


                        @csrf
                        @if(session('success'))
                            <button type="button"
                                class="w-full bg-green-600 text-white py-5 font-bold uppercase text-xs tracking-[0.3em] flex items-center justify-center gap-3 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                ¡Añadido!
                            </button>
                        @else
                            <button type="submit"
                                class="w-full bg-black text-white py-5 font-bold uppercase text-xs tracking-[0.3em] shadow-2xl hover:bg-gray-800 transition-all flex items-center justify-center gap-3 active:scale-[0.98]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 11-8 0m-4 8h16l-1.244 6.323A2 2 0 0114.805 23H9.195a2 2 0 01-1.951-1.677L6 11z" />
                                </svg>
                                Añadir a la bolsa
                            </button>
                        @endif
                    </form>
                </div>

                <div class="mt-12 border-t pt-8">
                    <details class="group cursor-pointer" open>
                        <summary
                            class="font-bold uppercase text-[11px] tracking-widest list-none flex justify-between items-center">
                            Descripción del producto
                            <span class="group-open:rotate-180 transition-transform">↓</span>
                        </summary>
                        <p class="mt-4 text-sm text-gray-600 leading-relaxed italic border-l-2 border-gray-100 pl-4">
                            {{ $producto->descripcion }}
                        </p>
                    </details>
                </div>
            </div>
        </div>
    </main>

    <script>
        function cambiarImagen(elemento, ruta) {
            const mainImg = document.getElementById('view-principal');
            mainImg.style.opacity = '0';
            setTimeout(() => {
                mainImg.src = ruta;
                mainImg.style.opacity = '1';
            }, 200);

            document.querySelectorAll('.thumbnail-btn').forEach(btn => btn.classList.replace('border-black', 'border-gray-200'));
            elemento.classList.replace('border-gray-200', 'border-black');
            elemento.classList.add('border-2');
        }

        function seleccionarColor(elemento, nombre) {
            const label = document.getElementById('color-seleccionado');
            label.innerText = nombre;
            document.getElementById('input-color').value = nombre;
            label.className = "text-black font-black ml-2";

            document.querySelectorAll('.color-option-btn').forEach(btn => btn.classList.remove('ring-2', 'ring-black', 'ring-offset-2'));
            elemento.classList.add('ring-2', 'ring-black', 'ring-offset-2');
        }

        function selectTalla(elemento) {
            document.querySelectorAll('.talla-btn').forEach(btn => btn.className = "talla-btn border border-gray-200 py-4 text-[11px] font-black hover:border-black transition-all uppercase");
            elemento.className = "talla-btn border-2 border-black bg-black text-white py-4 text-[11px] font-black uppercase";
            document.getElementById('input-talla').value = elemento.innerText.trim();
        }

        document.getElementById('form-carrito').addEventListener('submit', function (e) {
            const color = document.getElementById('input-color').value;
            const talla = document.getElementById('input-talla').value;

            if (!color || !talla) {
                e.preventDefault();

                let error = document.getElementById('error-msg');

                if (!error) {
                    error = document.createElement('div');
                    error.id = "error-msg";
                    error.className = "bg-red-100 text-red-700 p-3 mb-4 rounded text-sm";
                    this.prepend(error);
                }

                error.innerText = "Debes seleccionar color y talla antes de añadir.";
            }
        });
    </script>
</body>

</html>
