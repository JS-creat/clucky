<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <nav class="border-b sticky top-0 bg-white z-50">
        <div class="max-w-full mx-auto px-4 sm:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo C'Lucky"
                            class="h-14 w-auto transition-transform hover:scale-105">
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <div class="max-w-5xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-black uppercase italic mb-8">Tu Bolsa de Compras</h1>

        @if(count($carrito) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-4">
                    @foreach($carrito as $id => $detalles)
                        <div class="flex bg-white p-4 shadow-sm rounded-sm border items-center gap-4">
                            <img src="{{ asset('productos/' . $detalles['imagen']) }}" class="w-20 h-24 object-cover">
                            <div class="flex-1">
                                <h3 class="font-bold uppercase text-sm">{{ $detalles['nombre'] }}</h3>
                                <div class="flex items-center gap-3 mt-2">

                                    <a href="{{ route('carrito.disminuir', $id) }}"
                                        class="px-2 py-1 border text-sm font-bold hover:bg-gray-100">−</a>

                                    <span class="text-sm font-bold">
                                        {{ $detalles['cantidad'] }}
                                    </span>

                                    <a href="{{ route('carrito.aumentar', $id) }}"
                                        class="px-2 py-1 border text-sm font-bold hover:bg-gray-100">+</a>

                                    <a href="{{ route('carrito.eliminar', $id) }}"
                                        class="ml-4 text-red-500 text-xs uppercase font-bold hover:underline">
                                        Eliminar
                                    </a>

                                </div>

                                <p class="font-black mt-2 text-[#f50057]">S/ {{ number_format($detalles['precio'], 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-white p-6 shadow-lg border h-fit sticky top-24">
                    <h2 class="font-bold uppercase text-xs tracking-widest mb-4">Resumen del pedido</h2>
                    <div class="flex justify-between border-b pb-4 mb-4">
                        <span class="text-gray-600 text-sm">Subtotal</span>
                        <span class="font-bold">S/ {{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between mb-8">
                        <span class="font-black text-lg">TOTAL</span>
                        <span class="font-black text-lg text-[#f50057]">S/ {{ number_format($total, 2) }}</span>
                    </div>

                    <a href="{{ route('carrito.checkout') }}"
                        class="block w-full bg-black text-white text-center py-4 font-bold uppercase text-xs tracking-[0.2em] hover:bg-gray-800 transition">
                        Finalizar Compra
                    </a>
                </div>
            </div>
        @else
            <div class="text-center py-20 bg-white border">
                <p class="text-gray-400 mb-4 tracking-widest uppercase">Tu bolsa está vacía</p>
                <a href="{{ url('/') }}" class="underline font-bold uppercase text-xs">Volver a la tienda</a>
            </div>
        @endif
    </div>

    <script>
        public function aumentar($id) {
            $carrito = session() -> get('carrito', []);

            if (isset($carrito[$id])) {
                $carrito[$id]['cantidad']++;
                session() -> put('carrito', $carrito);
            }

            return redirect() -> route('carrito.index');
        }

        public function disminuir($id) {
            $carrito = session() -> get('carrito', []);

            if (isset($carrito[$id])) {

                if ($carrito[$id]['cantidad'] > 1) {
                    $carrito[$id]['cantidad']--;
                } else {
                    unset($carrito[$id]); // si queda en 0 lo eliminamos
                }

                session() -> put('carrito', $carrito);
            }

            return redirect() -> route('carrito.index');
        }

        public function eliminar($id) {
            $carrito = session() -> get('carrito', []);

            if (isset($carrito[$id])) {
                unset($carrito[$id]);
                session() -> put('carrito', $carrito);
            }

            return redirect() -> route('carrito.index');
        }

    </script>
</body>

</html>
