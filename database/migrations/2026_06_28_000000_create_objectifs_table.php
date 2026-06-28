<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('objectifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('saison')->nullable();
            $table->decimal('objectif_production', 12, 2)->default(0);
            $table->decimal('objectif_ca', 15, 2)->default(0);
            $table->decimal('objectif_surface', 8, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'saison']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objectifs');
    }
};