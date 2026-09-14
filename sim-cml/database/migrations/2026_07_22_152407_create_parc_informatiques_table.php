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
        Schema::create('parc_informatiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direction_id')->constrained('directions')->restrictOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->enum('type_equipement', [
                'LAPTOP',
                'DESKTOP',
                'ECRAN',
                'EQUIPEMENTS_RESEAUX',
                'ONDULEURS_OU_AUTRES',
                'POSTES_TELEPHONIQUES',
            ]);
            $table->string('equipement_id_interne');
            $table->string('nom_materiel');
            $table->string('marque_modele');
            $table->string('os_logiciels')->nullable();
            $table->string('utilisateur_assigne')->nullable();
            $table->string('numero_bc')->nullable();
            $table->date('date_achat')->nullable();
            $table->decimal('prix_achat', 12, 2)->nullable();
            $table->date('fin_garantie')->nullable();
            $table->string('fournisseur')->nullable();
            $table->enum('etat', ['EN_SERVICE', 'HORS_SERVICE'])->default('EN_SERVICE');
            $table->text('notes_libres')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parc_informatiques');
    }
};
