<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('sku_id', 20);
            $table->string('name');
            $table->string('code');
            $table->enum('status', ['Pendiente', 'En proceso', 'Completado'])->default('Pendiente');
            $table->json('checklist')->nullable(); // JSON con los entregables
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skus');
    }
};