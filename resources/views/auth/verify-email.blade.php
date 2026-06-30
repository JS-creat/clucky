@extends('layouts.app')

@section('title', 'Verificar email - B-EDEN')

@section('content')
    <div class="flex justify-center items-center py-10 md:py-20 px-4 flex-grow">
        <div class="w-full max-w-md">
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

                <div class="space-y-4" x-data="{
            cooldown: localStorage.getItem('resend_cooldown') ? parseInt(localStorage.getItem('resend_cooldown')) : 0,
            init() {
                if (this.cooldown > 0) {
                    this.startTimer();
                }
            },
            startTimer() {
                let interval = setInterval(() => {
                    if (this.cooldown > 0) {
                        this.cooldown--;
                        localStorage.setItem('resend_cooldown', this.cooldown);
                    } else {
                        clearInterval(interval);
                        localStorage.removeItem('resend_cooldown');
                    }
                }, 1000);
            },
            handleSubmit() {
                this.cooldown = 60; // 1 minuto de bloqueo para evitar spam
                localStorage.setItem('resend_cooldown', this.cooldown);
                this.startTimer();
            }
         }">

                    <form method="POST" action="{{ route('verification.send') }}" class="w-full" @submit="handleSubmit()">
                        @csrf
                        <button type="submit" :disabled="cooldown > 0"
                            :class="cooldown > 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-black hover:bg-gray-800 cursor-pointer'"
                            class="w-full text-white py-3 rounded-lg text-base font-bold transition">
                            <span
                                x-text="cooldown === 0 ? 'Reenviar correo de verificación' : 'Reenviar en ' + cooldown + 's'"></span>
                        </button>
                    </form>
                </div>

                <div class="text-center text-xs text-gray-500 mt-6" x-data="{
                        logoutAndRedirect() {
                            fetch('{{ route('logout') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                }
                            }).finally(() => {
                                window.location.href = '{{ route('register') }}';
                            });
                        }
                     }">
                    ¿No eres tú?
                    <button @click="logoutAndRedirect()"
                        class="text-black underline font-medium hover:text-gray-600 transition cursor-pointer bg-transparent border-0 p-0">
                        Registrarse
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection
