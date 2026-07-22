<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cupon_usos', function (Blueprint $table) {
            $table->id('id_cupon_uso');

            // FK a cupones
            $table->unsignedBigInteger('id_cupon');
            $table->foreign('id_cupon')
                  ->references('id_cupon')
                  ->on('cupones')
                  ->onDelete('cascade');

            // FK a usuario
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('cascade');

            // Cuánto se descontó
            $table->decimal('monto_descuento', 10, 2);

            // Cuánto era el carrito
            $table->decimal('monto_carrito', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cupon_usos');
    }
};
