<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {

        /*
        =====================================
        ROLES
        =====================================
        */
        Schema::create('rol', function (Blueprint $table) {
            $table->id('id_rol');
            $table->string('nombre_rol', 50);
        });


        /*
        =====================================
        TIPO DOCUMENTO
        =====================================
        */
        Schema::create('tipo_documento', function (Blueprint $table) {
            $table->id('id_tipo_documento');
            $table->string('nombre_tipo_documento', 50);
        });


        /*
        =====================================
        USUARIO
        =====================================
        */
        Schema::create('usuario', function (Blueprint $table) {

            $table->id('id_usuario');

            $table->string('nombres', 50);
            $table->string('apellidos', 50);

            $table->string('correo', 100)->unique();

            $table->timestamp('email_verified_at')->nullable();

            $table->string('contrasena', 255);

            $table->string('numero_documento', 20)->nullable();

            $table->string('telefono', 20)->nullable();

            $table->unsignedBigInteger('id_tipo_documento')->nullable();

            $table->unsignedBigInteger('id_rol')->default(2);

            $table->rememberToken();

            $table->timestamps();

            $table->foreign('id_tipo_documento')->references('id_tipo_documento')->on('tipo_documento');

            $table->foreign('id_rol')->references('id_rol')->on('rol');
        });



        /*
        =====================================
        UBIGEO
        =====================================
        */

        Schema::create('departamento', function (Blueprint $table) {

            $table->id('id_departamento');

            $table->string('nombre_departamento', 100);
        });


        Schema::create('provincia', function (Blueprint $table) {

            $table->id('id_provincia');

            $table->string('nombre_provincia', 100);

            $table->unsignedBigInteger('id_departamento');

            $table->foreign('id_departamento')->references('id_departamento')->on('departamento');
        });


        Schema::create('distrito', function (Blueprint $table) {

            $table->id('id_distrito');

            $table->string('nombre_distrito', 100);

            $table->decimal('costo_envio', 6, 2)->default(0);

            $table->unsignedBigInteger('id_provincia');

            $table->foreign('id_provincia')->references('id_provincia')->on('provincia');
        });



        /*
        =====================================
        AGENCIA
        =====================================
        */

        Schema::create('agencia', function (Blueprint $table) {

            $table->id('id_agencia');

            $table->string('nombre_agencia', 100);

            $table->text('direccion');

            $table->decimal('costo_envio', 6, 2);

            $table->unsignedBigInteger('id_distrito');

            $table->boolean('estado')->default(1);

            $table->timestamps();

            $table->foreign('id_distrito')->references('id_distrito')->on('distrito');
        });



        /*
        =====================================
        GENERO
        =====================================
        */

        Schema::create('genero', function (Blueprint $table) {

            $table->id('id_genero');

            $table->string('nombre_genero', 20);
        });



        /*
        =====================================
        CATEGORIA
        =====================================
        */

        Schema::create('categoria', function (Blueprint $table) {

            $table->id('id_categoria');

            $table->string('nombre_categoria', 50);

            $table->boolean('estado_categoria')->default(1); // Activa / Inactiva

            $table->timestamps(); // created_at y updated_at

        });



        /*
        =====================================
        PROMOCIONES
        =====================================
        */

        Schema::create('promociones', function (Blueprint $table) {

            $table->id('id_promocion');

            $table->string('nombre_promocion', 50);

            $table->text('descripcion')->nullable();

            $table->decimal('descuento', 6, 2);

            $table->dateTime('fecha_inicio');

            $table->dateTime('fecha_fin');

            $table->boolean('estado_promocion')->default(1);
        });



        /*
        =====================================
        PRODUCTO
        =====================================
        */

        Schema::create('producto', function (Blueprint $table) {

            $table->id('id_producto');

            $table->string('nombre_producto', 150);

            $table->text('descripcion')->nullable();

            $table->decimal('precio', 10, 2);

            $table->decimal('precio_oferta', 10, 2)->nullable();

            $table->string('imagen')->nullable();

            $table->text('galeria')->nullable();

            $table->string('marca', 50)->nullable();

            $table->boolean('estado_producto')->default(1);

            $table->unsignedBigInteger('id_genero')->nullable();

            $table->unsignedBigInteger('id_categoria')->nullable();

            $table->unsignedBigInteger('id_promocion')->nullable();

            $table->timestamps();

            $table->foreign('id_genero')->references('id_genero')->on('genero');

            $table->foreign('id_categoria')->references('id_categoria')->on('categoria');

            $table->foreign('id_promocion')->references('id_promocion')->on('promociones');
        });



        /*
        =====================================
        VARIANTES
        =====================================
        */

        Schema::create('producto_variante', function (Blueprint $table) {

            $table->id('id_variante');

            $table->unsignedBigInteger('id_producto');

            $table->string('talla', 50);

            $table->string('color', 50)->nullable();

            $table->integer('stock')->default(0);

            $table->string('sku', 50)->unique();

            $table->timestamps();

            $table->foreign('id_producto')->references('id_producto')->on('producto');
        });



        /*
        =====================================
        CUPONES
        =====================================
        */

        Schema::create('cupones', function (Blueprint $table) {

            $table->id('id_cupon');

            $table->string('codigo_cupon', 50)->unique();

            $table->decimal('monto_cupon', 6, 2);

            $table->decimal('monto_compra_minima', 6, 2);

            $table->date('fecha_vencimiento');

            $table->boolean('estado_cupon')->default(1);
        });



        /*
        =====================================
        TIPO ENTREGA
        =====================================
        */

        Schema::create('tipo_entrega', function (Blueprint $table) {

            $table->id('id_tipo_entrega');

            $table->string('nombre_tipo_entrega', 100);

            $table->boolean('estado')->default(1);
        });



        /*
        =====================================
        PEDIDO
        =====================================
        */

        Schema::create('pedido', function (Blueprint $table) {

            $table->id('id_pedido');

            $table->string('numero_pedido', 20)->unique();

            $table->dateTime('fecha_pedido')->useCurrent();

            $table->decimal('total_pedido', 10, 2);

            $table->enum('estado_pedido', ['Pendiente', 'Confirmado', 'En camino', 'Listo para recoger', 'Entregado', 'Anulado'])->default('Pendiente');

            $table->string('nombre_agencia')->nullable();

            $table->text('direccion_agencia')->nullable();

            $table->dateTime('fecha_envio')->nullable();

            $table->date('fecha_entrega_estimada')->nullable();

            $table->dateTime('fecha_entrega_real')->nullable();

            $table->unsignedBigInteger('id_distrito')->nullable();

            $table->unsignedBigInteger('id_usuario')->nullable();

            $table->unsignedBigInteger('id_cupon')->nullable();

            $table->unsignedBigInteger('id_tipo_entrega')->nullable();

            $table->timestamps();
        });



        /*
        =====================================
        DETALLE PEDIDO
        =====================================
        */

        Schema::create('detalle_pedido', function (Blueprint $table) {

            $table->id('id_detalle_pedido');

            $table->unsignedBigInteger('id_pedido');

            $table->unsignedBigInteger('id_variante');

            $table->integer('cantidad');

            $table->decimal('precio_unitario', 10, 2);

            $table->decimal('subtotal', 10, 2);

            $table->foreign('id_pedido')->references('id_pedido')->on('pedido');

            $table->foreign('id_variante')->references('id_variante')->on('producto_variante');
        });



        /*
        =====================================
        CARRITO
        =====================================
        */

        Schema::create('carrito', function (Blueprint $table) {

            $table->id('id_carrito');

            $table->unsignedBigInteger('id_usuario')->unique();

            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuario');
        });



        Schema::create('detalle_carrito', function (Blueprint $table) {

            $table->id('id_detalle_carrito');

            $table->unsignedBigInteger('id_carrito');

            $table->unsignedBigInteger('id_variante');

            $table->integer('cantidad')->default(1);

            $table->timestamps();

            $table->unique(['id_carrito', 'id_variante']);
        });



        /*
        =====================================
        FAVORITOS
        =====================================
        */

        Schema::create('favoritos', function (Blueprint $table) {

            $table->id('id_favorito');

            $table->unsignedBigInteger('id_usuario');

            $table->unsignedBigInteger('id_producto');

            $table->timestamps();

            $table->unique(['id_usuario', 'id_producto']);

            $table->foreign('id_usuario')->references('id_usuario')->on('usuario');

            $table->foreign('id_producto')->references('id_producto')->on('producto');
        });



        /*
        =====================================
        SANCTUM: PERSONAL ACCESS TOKENS
        =====================================
        */
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        /*
                =====================================
                BANNERS
                =====================================
*/
        Schema::create('banners', function (Blueprint $table) {
            $table->id('id_banner');
            $table->string('titulo', 100);
            $table->string('subtitulo', 150)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('etiqueta', 50)->nullable();
            $table->string('texto_boton', 50)->nullable();
            $table->string('url_boton', 255)->nullable();
            $table->string('imagen', 255);
            $table->integer('orden')->default(0);
            $table->boolean('estado')->default(1);
            $table->timestamps();
        });
    }



    public function down(): void
    {

        // Eliminar en orden inverso al de creación
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('favoritos');
        Schema::dropIfExists('detalle_carrito');
        Schema::dropIfExists('carrito');
        Schema::dropIfExists('detalle_pedido');
        Schema::dropIfExists('pedido');
        Schema::dropIfExists('tipo_entrega');
        Schema::dropIfExists('cupones');
        Schema::dropIfExists('producto_variante');
        Schema::dropIfExists('producto');
        Schema::dropIfExists('promociones');
        Schema::dropIfExists('categoria');
        Schema::dropIfExists('genero');
        Schema::dropIfExists('agencia');
        Schema::dropIfExists('distrito');
        Schema::dropIfExists('provincia');
        Schema::dropIfExists('departamento');
        Schema::dropIfExists('usuario');
        Schema::dropIfExists('tipo_documento');
        Schema::dropIfExists('rol');
        Schema::dropIfExists('banners');
    }
};
