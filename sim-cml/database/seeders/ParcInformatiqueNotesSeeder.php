<?php

namespace Database\Seeders;

use App\Models\ParcInformatiqueNote;
use Illuminate\Database\Seeder;

class ParcInformatiqueNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notes = [
            'BON' => 'Bon',
            'NEUF' => 'Neuf',
            'VIEILLISSANT' => 'Vieillissant',
            'A_REMPLACER' => 'A remplacer',
            'SYSTEME_NON_ACTIVE' => 'Système non activé',
            'RAM_INSUFFISANTE' => 'RAM insuffisante',
            'PAS_D_AUTONOMIE' => "Pas d'autonomie",
            'AUTRE' => 'Autres (à préciser)',
        ];

        foreach ($notes as $code => $label) {
            ParcInformatiqueNote::updateOrCreate(
                ['code' => $code],
                ['label' => $label]
            );
        }
    }
}
