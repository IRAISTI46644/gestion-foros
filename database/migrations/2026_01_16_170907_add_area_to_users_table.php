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
        Schema::table('users', function (Blueprint $table) {
            // Verificamos si no existen para evitar el error de "Duplicate column"
            if (!Schema::hasColumn('users', 'direccion')) {
                $table->string('direccion')->nullable();
            }
            if (!Schema::hasColumn('users', 'area')) {
                $table->string('area')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['direccion', 'area']);
        });
    }
};