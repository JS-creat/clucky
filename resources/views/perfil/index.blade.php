@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto px-6 py-12">
    <h1 class="text-3xl font-bold mb-6">Mi Perfil</h1>

    <div class="bg-white shadow rounded-xl p-6 space-y-4">
        <p><strong>Nombre:</strong> {{ auth()->user()->name }}</p>
        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="mt-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                Cerrar sesión
            </button>
        </form>
    </div>
</div>

@endsection
