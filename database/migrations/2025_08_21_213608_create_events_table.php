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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary(); // ID sous forme d'UUID comme clé primaire
            $table->string('title'); // Titre de l'événement
            $table->text('description')->nullable(); // Description (nullable)
            $table->date('start_date')->nullable(); // Date de début (nullable)
            $table->date('end_date')->nullable(); // Date de fin (nullable)
            $table->float('price')->nullable(); // Prix (nullable)
            $table->string('image')->nullable(); // Image en base64 ou chemin (nullable)
            $table->boolean('is_active')->default(false); // Statut actif/inactif
            $table->uuid('city_id'); // Clé étrangère vers cities
            $table->timestamps(); // Champs created_at et updated_at

            // Clé étrangère
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
