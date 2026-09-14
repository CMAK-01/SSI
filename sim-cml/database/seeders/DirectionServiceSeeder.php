<?php

namespace Database\Seeders;

use App\Models\Direction;
use App\Models\Service;
use Illuminate\Database\Seeder;

class DirectionServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {$data = [
    'DTAM' => [
        'nom' => 'DTAM',
        'services' => [
            'Service DTAM',
            'Service Maintenance Industrielle',
            'Service Maintenance Mécanique',
        ],
    ],
    'DTARG' => [
        'nom' => 'DTARG',
        'services' => [
            'Service DTARG',
            'Service Géologie Minière',
            'Service Laboratoire',
        ],
    ],
    'DTAP' => [
        'nom' => 'DTAP',
        'services' => [
            'Service DTAP',
            'Service Exploitation Minière',
            'Service Traitement',
        ],
    ],
    'DAF' => [
        'nom' => 'DAF',
        'services' => [
            'Service Administration du site',
            'Service Comptabilité',
            'Service Gestion Financière',
            'Service Informatique',
        ],
    ],
    'DCL' => [
        'nom' => 'DCL',
        'services' => [
            'Service Commercial et Marketing',
            'Service Logistique et transit',
        ],
    ],
    'DG' => [
        'nom' => 'DG',
        'services' => [
            'Service DG',
            'Service Audit et Contrôle de Gestion',
            'Service Communication',
        ],
    ],
    'DAARH' => [
        'nom' => 'DAARH',
        'services' => [
            'Service Développement RH & Carrière',
            'Service GAP/PAIE',
            'Infirmerie',
        ],
    ],
    'DAAGL' => [
        'nom' => 'DAAGL',
        'services' => [
            'Service achat et approvisionnement',
            'Service Moyens Généraux',
        ],
    ],
];

        foreach ($data as $code => $directionData) {
            $direction = Direction::updateOrCreate(
                ['code' => $code],
                ['nom' => $directionData['nom']]
            );

            foreach ($directionData['services'] as $serviceNom) {
                Service::updateOrCreate(
                    ['direction_id' => $direction->id, 'nom' => $serviceNom],
                    []
                );
            }
        }
    }
}
