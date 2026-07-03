<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimiento_stock', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_variante')
                ->constrained('producto_variante', 'id_variante')
                ->cascadeOnDelete();

            $table->enum('tipo', ['entrada', 'salida']);
            $table->unsignedInteger('cantidad');
            $table->string('motivo');

            $table->foreignId('id_pedido')
                ->nullable()
                ->constrained('pedido', 'id_pedido')
                ->nullOnDelete();

            $table->foreignId('id_usuario')
                ->nullable()
                ->constrained('usuario', 'id_usuario')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimiento_stock');
    }
};
