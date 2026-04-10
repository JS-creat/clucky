<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedido', function (Blueprint $table) {
            $table->unsignedBigInteger('id_agencia')->nullable()->after('id_tipo_entrega');

            $table->foreign('id_agencia')
                ->references('id_agencia')
                ->on('agencia');
        });
    }

    public function down(): void
    {
        Schema::table('pedido', function (Blueprint $table) {
            $table->dropForeign(['id_agencia']);
            $table->dropColumn('id_agencia');
        });
    }
};
