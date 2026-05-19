<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks_predefined', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phase_id')->constrained('phases')->onDelete('cascade');
            $table->string('name');
            $table->string('department');
            $table->integer('default_duration')->default(1);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks_predefined');
    }
};