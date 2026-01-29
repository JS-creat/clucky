<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Tabla ROL
        Schema::create('rol', function (Blueprint $table) {
            $table->id('id_rol'); // Tu PK
            $table->string('nombre_rol', 50);
        });

        // 2. Tabla TIPO DOCUMENTO
        Schema::create('tipo_documento', function (Blueprint $table) {
            $table->id('id_tipo_documento');
            $table->string('nombre_tipo_documento', 50);
        });

        // 3. Tabla USUARIO
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombres', 50);
            $table->string('apellidos', 50);
            $table->string('correo', 100)->unique();
            $table->string('contrasena', 100);
            $table->string('numero_documento', 20)->nullable();
            $table->unsignedBigInteger('id_tipo_documento')->nullable();
            $table->unsignedBigInteger('id_rol')->default(2);

            // Relaciones
            $table->foreign('id_tipo_documento')->references('id_tipo_documento')->on('tipo_documento');
            $table->foreign('id_rol')->references('id_rol')->on('rol');
            $table->timestamps();
        });

        // 4. Tabla CATEGORIA
        Schema::create('categoria', function (Blueprint $table) {
            $table->id('id_categoria');
            $table->string('nombre_categoria', 50);
        });

        // 5. Tabla PRODUCTO
        Schema::create('producto', function (Blueprint $table) {
            $table->id('id_producto');
            $table->string('nombre_producto', 150);
            $table->decimal('precio', 6, 2);
            $table->integer('stock')->default(0);
            $table->unsignedBigInteger('id_categoria')->nullable();
            $table->foreign('id_categoria')->references('id_categoria')->on('categoria');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('producto');
        Schema::dropIfExists('usuario');
        Schema::dropIfExists('categoria');
        Schema::dropIfExists('tipo_documento');
        Schema::dropIfExists('rol');
    }
};
