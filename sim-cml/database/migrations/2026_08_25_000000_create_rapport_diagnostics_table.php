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
        Schema::create('rapport_diagnostics', function (Blueprint $table) {
            $table->id();

            // 1. Informations générales
            $table->date('date_diagnostic');
            $table->foreignId('direction_id')->constrained('directions')->restrictOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->string('lieu_intervention')->default('SITE MINIER LAUZOUA');

            // 2. Informations sur l'équipement
            $table->enum('type_materiel', ['LAPTOP', 'DESKTOP', 'SERVEUR', 'IMPRIMANTE']);
            $table->string('marque_modele')->nullable();
            $table->string('numero_serie_inventaire')->nullable();
            $table->string('utilisateur_principal')->nullable();

            // 3. Constat initial
            $table->text('symptomes_signales')->nullable();
            $table->text('contexte_apparition')->nullable();
            $table->text('historique_incidents')->nullable();

            // 4. Vérifications effectuées (statut + observation par élément)
            $verifications = [
                'inspection_connecteurs',
                'inspection_coque',
                'test_ram',
                'test_disque',
                'test_alimentation',
                'test_carte_mere',
                'test_pave_tactile',
                'test_carte_reseau',
                'test_drivers',
                'test_bios',
                'test_os',
            ];
            foreach ($verifications as $key) {
                $table->string($key . '_statut')->nullable();
                $table->string($key . '_observation')->nullable();
            }
            $table->string('test_carte_reseau_designation')->nullable();
            $table->text('resultats_diagnostics')->nullable();

            // 5. Analyses et causes probables
            $table->text('cause_identifiee')->nullable();
            $table->text('facteurs_contributifs')->nullable();

            // 6. Actions correctives
            $table->text('mesures_prises')->nullable();
            $table->text('pieces_remplacees')->nullable();
            $table->text('logiciels_maj')->nullable();

            // 7. Recommandations
            $table->text('recommandations_preventives')->nullable();
            $table->text('recommandations_correctives')->nullable();

            // 8. Validation
            $table->enum('etat_final', ['FONCTIONNEL', 'PARTIEL', 'NON_FONCTIONNEL'])->nullable();
            $table->string('technicien_si')->nullable();
            $table->string('chef_service_informatique')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapport_diagnostics');
    }
};
