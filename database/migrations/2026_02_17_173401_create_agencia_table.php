<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agencia', function (Blueprint $table) {

            $table->id('id_agencia');

            $table->string('nombre_agencia');

            $table->text('direccion');

            $table->boolean('estado')->default(1);

            $table->timestamps();

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencia');
    }
};
