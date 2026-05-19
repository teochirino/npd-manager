<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gate_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('gate_key'); // Gate 1, Gate 2, etc.
            $table->enum('status', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->json('items_checked')->nullable(); // Guardar qué items del gate están marcados
            $table->date('approved_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('comments')->nullable();
            $table->timestamps();
            
            $table->unique(['project_id', 'gate_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_approvals');
    }
};