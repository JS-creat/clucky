<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            // Agregamos el campo de control después de updated_at
            $table->timestamp('reactivation_email_sent_at')->nullable()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            // Por si alguna vez necesitas borrar este campo
            $table->dropColumn('reactivation_email_sent_at');
        });
    }
};
