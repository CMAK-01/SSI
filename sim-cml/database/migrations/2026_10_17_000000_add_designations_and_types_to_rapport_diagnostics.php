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
        // Nouveaux types de matériel autorisés
        Schema::table('rapport_diagnostics', function (Blueprint $table) {
            $table->enum('type_materiel', [
                'LAPTOP',
                'DESKTOP',
                'SERVEUR',
                'IMPRIMANTE',
                'ONDULEURS',
                'ROUTEURS',
                'POINT_ACCES',
            ])->change();
        });

        // Cellule « Désignation » pour chaque vérification
        // (test_carte_reseau possède déjà la sienne)
        Schema::table('rapport_diagnostics', function (Blueprint $table) {
            $verifications = [
                'inspection_connecteurs',
                'inspection_coque',
                'test_ram',
                'test_disque',
                'test_alimentation',
                'test_carte_mere',
                'test_pave_tactile',
                'test_drivers',
                'test_bios',
                'test_os',
            ];
            foreach ($verifications as $key) {
                $table->string($key . '_designation')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapport_diagnostics', function (Blueprint $table) {
            $table->enum('type_materiel', ['LAPTOP', 'DESKTOP', 'SERVEUR', 'IMPRIMANTE'])->change();
        });

        Schema::table('rapport_diagnostics', function (Blueprint $table) {
            $table->dropColumn([
                'inspection_connecteurs_designation',
                'inspection_coque_designation',
                'test_ram_designation',
                'test_disque_designation',
                'test_alimentation_designation',
                'test_carte_mere_designation',
                'test_pave_tactile_designation',
                'test_drivers_designation',
                'test_bios_designation',
                'test_os_designation',
            ]);
        });
    }
};
