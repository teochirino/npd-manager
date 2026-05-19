<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('perfil', 10)->nullable()->after('password');
            $table->string('role')->nullable()->after('perfil');
            $table->string('area', 10)->nullable()->after('role');
            $table->string('area_color')->nullable();
            $table->string('area_bg')->nullable();
            $table->string('initials', 5)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['perfil', 'role', 'area', 'area_color', 'area_bg', 'initials']);
        });
    }
};