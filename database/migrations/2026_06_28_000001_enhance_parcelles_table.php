<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parcelles', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->date('last_irrigation')->nullable();
            $table->date('last_traitement')->nullable();
            $table->date('next_intervention')->nullable();
        });

        Schema::create('parcelle_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcelle_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path');
            $table->string('legende')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('parcelles', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'last_irrigation', 'last_traitement', 'next_intervention']);
        });
        Schema::dropIfExists('parcelle_photos');
    }
};