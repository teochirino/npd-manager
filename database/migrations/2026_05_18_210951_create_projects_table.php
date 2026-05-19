<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_id', 20)->unique();
            $table->string('name');
            $table->foreignId('engineer_id')->constrained('users')->onDelete('cascade');
            $table->date('final_date')->nullable();
            $table->integer('progress')->default(0);
            $table->enum('status', ['Terminado', 'En linea', 'En pausa', 'Cancelado', 'Fuera de linea'])->default('En linea');
            $table->enum('active_phase', ['Diseño', 'Desarrollo', 'Implementacion', 'Lanzamiento', 'Seguimiento'])->default('Diseño');
            $table->date('phase_date')->nullable();
            $table->string('category', 100)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};