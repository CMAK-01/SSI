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
    {
        $data = [
            'DTAM' => [
                'nom' => 'DTAM',
                'services' => [
                    'Service Maintenance Industrielle',
                    'Service Maintenance Mécanique',
                ],
            ],
            'DTARG' => [
                'nom' => 'DTARG',
                'services' => [
                    'Service Géologie Minière',
                    'Service Laboratoire',
                ],
            ],
            'DTAP' => [
                'nom' => 'DTAP',
                'services' => [
                    'Service Exploitation Minière',
                    'Service Traitement',
                ],
            ],
            'DAF' => [
                'nom' => 'DAF',
                'services' => [
                    'Service Administration du site',
                    'Service achat et approvisionnement',
                    'Service Comptabilité',
                    'Service Informatique',
                    'Service Moyens Généraux',
                    'Service Ressources Humaines',
                ],
            ],
            'DCL' => [
                'nom' => 'DCL',
                'services' => [
                    'Service Commercial et Marketing',
                    'Service Logistique et transit',
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
