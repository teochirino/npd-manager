<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phases', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Diseño, Desarrollo, Implementacion, Lanzamiento, Seguimiento
            $table->string('gate_key'); // Gate 1, Gate 2, Gate 3, Gate 4, Gate 5
            $table->string('gate_name'); // Concepto aprobado, Diseño liberado, etc.
            $table->string('color');
            $table->string('bg_color');
            $table->string('text_color');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phases');
    }
};