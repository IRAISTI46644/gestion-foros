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
    Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('titulo');
        $table->text('descripcion');
        $table->string('categoria'); // "Equipo no disponible", "Falla técnica", etc.
        $table->string('prioridad')->default('media'); // baja, media, alta
        $table->string('estado')->default('abierto'); // abierto, en proceso, resuelto, rechazado
        $table->timestamp('fecha_limite')->nullable(); // Para el SLA
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
