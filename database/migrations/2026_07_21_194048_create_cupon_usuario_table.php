<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cupon_usuario', function (Blueprint $table) {
            $table->id('id_cupon_usuario');

            $table->unsignedBigInteger('id_cupon');
            $table->foreign('id_cupon')
                  ->references('id_cupon')
                  ->on('cupones')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('cascade');

            $table->integer('usos_realizados')->default(0);

            $table->timestamps();

            // Un usuario solo puede tener un registro por cupón
            $table->unique(['id_cupon', 'id_usuario']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cupon_usuario');
    }
};
