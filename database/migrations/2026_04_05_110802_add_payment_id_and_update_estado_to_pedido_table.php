<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedido', function (Blueprint $table) {
            $table->string('payment_id')->nullable()->after('estado_pedido');
        });

        DB::statement("ALTER TABLE pedido MODIFY COLUMN estado_pedido ENUM(
        'Pendiente',
        'Pagado',
        'Confirmado',
        'En camino',
        'Listo para recoger',
        'Entregado',
        'Anulado'
    ) DEFAULT 'Pendiente'");
    }

    public function down(): void
    {
        Schema::table('pedido', function (Blueprint $table) {
            $table->dropColumn('payment_id');
        });

        DB::statement("ALTER TABLE pedido MODIFY COLUMN estado_pedido ENUM(
        'Pendiente',
        'Confirmado',
        'En camino',
        'Listo para recoger',
        'Entregado',
        'Anulado'
    ) DEFAULT 'Pendiente'");
    }
};
