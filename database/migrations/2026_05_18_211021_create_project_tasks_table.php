<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('task_predefined_id')->constrained('tasks_predefined')->onDelete('cascade');
            $table->enum('status', ['No iniciado', 'En progreso', 'Completado'])->default('No iniciado');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Índice único para evitar duplicados
            $table->unique(['project_id', 'task_predefined_id'], 'project_task_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};