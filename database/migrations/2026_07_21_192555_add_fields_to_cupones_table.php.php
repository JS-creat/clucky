<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Primero renombramos la columna
        Schema::table('cupones', function (Blueprint $table) {
            $table->renameColumn('monto_cupon', 'valor_descuento');
        });

        // Luego agregamos las nuevas columnas
        Schema::table('cupones', function (Blueprint $table) {
            $table->string('descripcion')
                ->nullable()
                ->after('codigo_cupon');

            $table->enum('tipo_descuento', ['porcentaje', 'monto_fijo'])
                ->default('monto_fijo')
                ->after('valor_descuento');

            $table->integer('uso_maximo_global')
                ->nullable()
                ->after('fecha_vencimiento');

            $table->integer('uso_maximo_por_usuario')
                ->nullable()
                ->after('uso_maximo_global');

            $table->integer('usos_actuales')
                ->default(0)
                ->after('uso_maximo_por_usuario');
        });
    }

    public function down(): void
    {
        Schema::table('cupones', function (Blueprint $table) {
            $table->dropColumn([
                'descripcion',
                'tipo_descuento',
                'uso_maximo_global',
                'uso_maximo_por_usuario',
                'usos_actuales',
            ]);
        });

        // Restaurar el nombre original
        Schema::table('cupones', function (Blueprint $table) {
            $table->renameColumn('valor_descuento', 'monto_cupon');
        });
    }
};
