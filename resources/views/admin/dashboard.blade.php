@extends('admin.layout')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
    body {
        font-family: 'DM Sans', sans-serif;
    }

    .font-syne {
        font-family: 'Syne', sans-serif;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-up {
        animation: fadeUp .45s ease forwards;
        opacity: 0;
    }

    .d1 { animation-delay: .05s; }
    .d2 { animation-delay: .10s; }
    .d3 { animation-delay: .15s; }
    .d4 { animation-delay: .20s; }
    .d5 { animation-delay: .25s; }
    .d6 { animation-delay: .30s; }

    .dashboard-card {
        background: #fff;
        border: 1px solid #eef0f2;
        border-radius: 18px;
    }

    .dashboard-card:hover {
        border-color: #e2e5e8;
    }
</style>

<div class="space-y-6">

    <div class="fade-up d1 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">
                Panel administrativo
            </p>

            <h1 class="font-syne text-2xl sm:text-3xl font-bold text-gray-950">
                Panel general
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Resumen de ventas, pedidos e inventario.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <div class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600">
                {{ now()->isoFormat('dddd, D [de] MMMM') }}
            </div>

            <button
                onclick="window.location.reload()"
                class="px-4 py-2.5 bg-gray-950 hover:bg-gray-800 text-white rounded-xl text-sm font-semibold transition">
                Actualizar
            </button>
        </div>
    </div>


    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <div class="dashboard-card fade-up d1 p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Ventas este mes
                    </p>

                    <p class="font-syne text-2xl font-bold text-gray-950 mt-2">
                        S/ {{ number_format($ventasMesActual, 2) }}
                    </p>
                </div>

                <div class="w-10 h-10 rounded-xl bg-lime-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-lime-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-2">
                @if($crecimientoVentas >= 0)
                    <span class="px-2 py-1 rounded-lg bg-green-50 text-green-700 text-xs font-bold">
                        ↑ {{ $crecimientoVentas }}%
                    </span>
                @else
                    <span class="px-2 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-bold">
                        ↓ {{ abs($crecimientoVentas) }}%
                    </span>
                @endif

                <span class="text-xs text-gray-400">
                    vs mes anterior
                </span>
            </div>
        </div>


        <div class="dashboard-card fade-up d2 p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Pedidos
                    </p>

                    <p class="font-syne text-2xl font-bold text-gray-950 mt-2">
                        {{ $pedidosHoy }}
                    </p>
                </div>

                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 3h18v18H3z"/>
                        <path d="M3 9h18M9 3v6"/>
                    </svg>
                </div>
            </div>

            <div class="mt-4">
                <span class="text-xs text-gray-400">
                    pedidos registrados hoy
                </span>
            </div>
        </div>


        <div class="dashboard-card fade-up d3 p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Clientes
                    </p>

                    <p class="font-syne text-2xl font-bold text-gray-950 mt-2">
                        {{ number_format($totalClientes) }}
                    </p>
                </div>

                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </div>
            </div>

            <div class="mt-4">
                <span class="text-xs text-gray-400">
                    clientes registrados
                </span>
            </div>
        </div>


        <div class="dashboard-card fade-up d4 p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Inventario
                    </p>

                    <p class="font-syne text-2xl font-bold text-gray-950 mt-2">
                        {{ number_format($totalProductos) }}
                    </p>
                </div>

                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 8l-9-5-9 5 9 5 9-5z"/>
                        <path d="M3 8v8l9 5 9-5V8M12 13v8"/>
                    </svg>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <span class="text-xs text-red-500 font-medium">
                    {{ $sinStock }} sin stock
                </span>

                <span class="text-xs text-yellow-600 font-medium">
                    {{ $stockBajo }} stock bajo
                </span>
            </div>
        </div>

    </div>


    <div class="dashboard-card fade-up d2 p-5">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <h2 class="font-syne font-bold text-base text-gray-950">
                    Atención requerida
                </h2>

                <p class="text-xs text-gray-400 mt-1">
                    Elementos que necesitan revisión.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

            <a href="{{ route('admin.pedidos.index') }}"
               class="group flex items-center justify-between p-4 rounded-xl bg-yellow-50 border border-yellow-100 hover:border-yellow-200 transition">

                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-900">
                            Pedidos pendientes
                        </p>

                        <p class="text-xs text-gray-500">
                            Requieren atención
                        </p>
                    </div>
                </div>

                <span class="font-syne font-bold text-lg text-yellow-700">
                    {{ $pedidosPendientes }}
                </span>
            </a>


            <div class="flex items-center justify-between p-4 rounded-xl bg-red-50 border border-red-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-900">
                            Productos sin stock
                        </p>

                        <p class="text-xs text-gray-500">
                            Inventario agotado
                        </p>
                    </div>
                </div>

                <span class="font-syne font-bold text-lg text-red-600">
                    {{ $sinStock }}
                </span>
            </div>


            <div class="flex items-center justify-between p-4 rounded-xl bg-orange-50 border border-orange-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-orange-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 9v4m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z"/>
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-900">
                            Stock bajo
                        </p>

                        <p class="text-xs text-gray-500">
                            Revisa el inventario
                        </p>
                    </div>
                </div>

                <span class="font-syne font-bold text-lg text-orange-600">
                    {{ $stockBajo }}
                </span>
            </div>

        </div>
    </div>


    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        <div class="dashboard-card fade-up d3 p-6 xl:col-span-2">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                <div>
                    <h2 class="font-syne font-bold text-base text-gray-950">
                        Rendimiento de ventas
                    </h2>

                    <p class="text-xs text-gray-400 mt-1">
                        Últimos 30 días
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-900">
                        S/ {{ number_format($ventasMesActual, 2) }}
                    </span>

                    <span class="px-2 py-1 rounded-lg bg-gray-100 text-gray-500 text-xs">
                        Este mes
                    </span>
                </div>
            </div>

            <div x-data x-init="
                new Chart($refs.ventasChart, {
                    type: 'line',
                    data: {
                        labels: {{ Js::from($diasCompletos->pluck('dia')) }},
                        datasets: [{
                            data: {{ Js::from($diasCompletos->pluck('total')) }},
                            borderColor: '#111827',
                            backgroundColor: function(ctx) {
                                const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 280);
                                gradient.addColorStop(0, 'rgba(17,24,39,0.12)');
                                gradient.addColorStop(1, 'rgba(17,24,39,0)');
                                return gradient;
                            },
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#bef264',
                            pointHoverBorderColor: '#111827',
                            pointHoverBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#111827',
                                titleColor: '#9ca3af',
                                bodyColor: '#fff',
                                padding: 12,
                                cornerRadius: 10,
                                callbacks: {
                                    label: function(c) {
                                        return ' S/ ' + c.parsed.y.toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxTicksLimit: 8,
                                    font: {
                                        size: 10
                                    },
                                    color: '#9ca3af'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f3f4f6'
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    },
                                    color: '#9ca3af',
                                    callback: function(value) {
                                        return 'S/' + value;
                                    }
                                }
                            }
                        }
                    }
                });
            ">
                <canvas x-ref="ventasChart" height="115"></canvas>
            </div>

        </div>


        <div class="dashboard-card fade-up d4 p-6">

            <div class="mb-4">
                <h2 class="font-syne font-bold text-base text-gray-950">
                    Estado de pedidos
                </h2>

                <p class="text-xs text-gray-400 mt-1">
                    Distribución actual
                </p>
            </div>

            <div x-data x-init="
                new Chart($refs.donutChart, {
                    type: 'doughnut',
                    data: {
                        labels: {{ Js::from($pedidosPorEstado->keys()) }},
                        datasets: [{
                            data: {{ Js::from($pedidosPorEstado->values()) }},
                            backgroundColor: [
                                '#fbbf24',
                                '#60a5fa',
                                '#a78bfa',
                                '#f97316',
                                '#34d399',
                                '#f87171'
                            ],
                            borderColor: '#fff',
                            borderWidth: 3,
                            hoverOffset: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '68%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 10,
                                        family: 'DM Sans'
                                    },
                                    padding: 12,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            ">
                <canvas x-ref="donutChart" height="190"></canvas>
            </div>

        </div>

    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <div class="dashboard-card fade-up d4 p-6">

            <div class="mb-5">
                <h2 class="font-syne font-bold text-base text-gray-950">
                    Ventas por categoría
                </h2>

                <p class="text-xs text-gray-400 mt-1">
                    Categorías con mayor facturación
                </p>
            </div>

            <div x-data x-init="
                new Chart($refs.catChart, {
                    type: 'bar',
                    data: {
                        labels: {{ Js::from($ventasPorCategoria->pluck('nombre_categoria')) }},
                        datasets: [{
                            data: {{ Js::from($ventasPorCategoria->pluck('total')) }},
                            backgroundColor: [
                                '#111827',
                                '#374151',
                                '#4b5563',
                                '#6b7280',
                                '#9ca3af',
                                '#d1d5db'
                            ],
                            borderRadius: 7,
                            borderSkipped: false,
                            barThickness: 18
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#111827',
                                bodyColor: '#fff',
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(c) {
                                        return ' S/ ' + c.parsed.x.toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f3f4f6'
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    },
                                    color: '#9ca3af',
                                    callback: function(value) {
                                        return 'S/' + value;
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    },
                                    color: '#374151'
                                }
                            }
                        }
                    }
                });
            ">
                <canvas x-ref="catChart" height="210"></canvas>
            </div>

        </div>


        <div class="dashboard-card fade-up d5 p-6">

            <div class="mb-5">
                <h2 class="font-syne font-bold text-base text-gray-950">
                    Productos más vendidos
                </h2>

                <p class="text-xs text-gray-400 mt-1">
                    Rendimiento por unidades e ingresos
                </p>
            </div>

            @php
                $maxUnidades = $topProductos->max('unidades') ?: 1;
            @endphp

            <div class="space-y-4">

                @forelse($topProductos as $i => $prod)

                    <div>
                        <div class="flex items-center justify-between gap-3 mb-2">

                            <div class="flex items-center gap-3 min-w-0">

                                <span class="font-syne font-bold text-xs text-gray-400 w-4">
                                    {{ $i + 1 }}
                                </span>

                                @if($prod->imagen)
                                    <img
                                        src="{{ asset('productos/' . $prod->imagen) }}"
                                        class="w-9 h-9 rounded-lg object-cover border border-gray-100 shrink-0"
                                        alt="{{ $prod->nombre_producto }}"
                                        onerror="this.style.display='none';"
                                    >
                                @else
                                    <div class="w-9 h-9 rounded-lg bg-gray-100 shrink-0"></div>
                                @endif

                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-gray-900 truncate">
                                        {{ $prod->nombre_producto }}
                                    </p>

                                    <p class="text-[11px] text-gray-400 mt-0.5">
                                        {{ $prod->unidades }} unidades
                                    </p>
                                </div>

                            </div>

                            <span class="text-xs font-bold text-gray-900 shrink-0">
                                S/ {{ number_format($prod->ingresos, 0) }}
                            </span>

                        </div>

                        <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden ml-7">
                            <div
                                class="h-full rounded-full bg-gray-900"
                                style="width: {{ ($prod->unidades / $maxUnidades) * 100 }}%">
                            </div>
                        </div>
                    </div>

                @empty

                    <div class="py-10 text-center">
                        <p class="text-sm text-gray-400">
                            Sin datos de ventas todavía.
                        </p>
                    </div>

                @endforelse

            </div>

        </div>

    </div>


    <div class="dashboard-card fade-up d6 p-6">

        <div class="flex items-center justify-between mb-5">

            <div>
                <h2 class="font-syne font-bold text-base text-gray-950">
                    Últimos pedidos
                </h2>

                <p class="text-xs text-gray-400 mt-1">
                    Actividad reciente
                </p>
            </div>

            <a
                href="{{ route('admin.pedidos.index') }}"
                class="text-xs font-semibold text-gray-900 hover:text-lime-700 transition">
                Ver todos →
            </a>

        </div>


        <div class="hidden md:block overflow-x-auto">

            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-[11px] uppercase tracking-wider font-semibold text-gray-400 pb-3">
                            Pedido
                        </th>

                        <th class="text-left text-[11px] uppercase tracking-wider font-semibold text-gray-400 pb-3">
                            Cliente
                        </th>

                        <th class="text-left text-[11px] uppercase tracking-wider font-semibold text-gray-400 pb-3">
                            Fecha
                        </th>

                        <th class="text-right text-[11px] uppercase tracking-wider font-semibold text-gray-400 pb-3">
                            Total
                        </th>

                        <th class="text-right text-[11px] uppercase tracking-wider font-semibold text-gray-400 pb-3">
                            Estado
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($ultimosPedidos as $pedido)

                        @php
                            $badgeClass = match($pedido->estado_pedido) {
                                'Pendiente'  => 'bg-yellow-50 text-yellow-700',
                                'Pagado'     => 'bg-blue-50 text-blue-700',
                                'Enviado'    => 'bg-purple-50 text-purple-700',
                                'En Agencia' => 'bg-orange-50 text-orange-700',
                                'Entregado'  => 'bg-green-50 text-green-700',
                                'Cancelado'  => 'bg-red-50 text-red-700',
                                default      => 'bg-gray-100 text-gray-600'
                            };
                        @endphp

                        <tr class="border-b border-gray-50 last:border-0">

                            <td class="py-3">
                                <span class="text-xs font-bold text-gray-900">
                                    #{{ $pedido->numero_pedido ?? $pedido->id_pedido }}
                                </span>
                            </td>

                            <td class="py-3">
                                <span class="text-xs text-gray-700">
                                    {{ trim(($pedido->nombres ?? '') . ' ' . ($pedido->apellidos ?? '')) ?: 'Cliente' }}
                                </span>
                            </td>

                            <td class="py-3">
                                <span class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($pedido->fecha_pedido)->diffForHumans() }}
                                </span>
                            </td>

                            <td class="py-3 text-right">
                                <span class="text-xs font-bold text-gray-900">
                                    S/ {{ number_format($pedido->total_pedido, 2) }}
                                </span>
                            </td>

                            <td class="py-3 text-right">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-[11px] font-semibold {{ $badgeClass }}">
                                    {{ $pedido->estado_pedido }}
                                </span>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="py-10 text-center text-sm text-gray-400">
                                No hay pedidos registrados.
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>

        </div>


        <div class="md:hidden space-y-3">

            @forelse($ultimosPedidos as $pedido)

                @php
                    $badgeClass = match($pedido->estado_pedido) {
                        'Pendiente'  => 'bg-yellow-50 text-yellow-700',
                        'Pagado'     => 'bg-blue-50 text-blue-700',
                        'Enviado'    => 'bg-purple-50 text-purple-700',
                        'En Agencia' => 'bg-orange-50 text-orange-700',
                        'Entregado'  => 'bg-green-50 text-green-700',
                        'Cancelado'  => 'bg-red-50 text-red-700',
                        default      => 'bg-gray-100 text-gray-600'
                    };
                @endphp

                <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-gray-50">

                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-900">
                            #{{ $pedido->numero_pedido ?? $pedido->id_pedido }}
                        </p>

                        <p class="text-xs text-gray-500 truncate mt-1">
                            {{ trim(($pedido->nombres ?? '') . ' ' . ($pedido->apellidos ?? '')) ?: 'Cliente' }}
                        </p>

                        <p class="text-[11px] text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($pedido->fecha_pedido)->diffForHumans() }}
                        </p>
                    </div>

                    <div class="text-right shrink-0">

                        <p class="text-xs font-bold text-gray-900 mb-1">
                            S/ {{ number_format($pedido->total_pedido, 2) }}
                        </p>

                        <span class="inline-flex px-2 py-1 rounded-lg text-[10px] font-semibold {{ $badgeClass }}">
                            {{ $pedido->estado_pedido }}
                        </span>

                    </div>

                </div>

            @empty

                <p class="py-8 text-center text-sm text-gray-400">
                    No hay pedidos registrados.
                </p>

            @endforelse

        </div>

    </div>

</div>

@endsection