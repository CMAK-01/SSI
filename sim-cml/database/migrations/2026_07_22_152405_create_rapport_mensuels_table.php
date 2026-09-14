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
        Schema::create('rapport_mensuels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direction_id')->constrained('directions')->restrictOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->date('date_intervention');
            $table->string('equipement_concerne');
            $table->string('utilisateur_equipement');
            $table->text('tache_effectuee');
            $table->enum('observation', ['OK', 'EN_COURS'])->default('OK');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapport_mensuels');
    }
};
