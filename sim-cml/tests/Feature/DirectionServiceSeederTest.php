<?php

namespace Tests\Feature;

use App\Models\Direction;
use App\Models\RapportMensuel;
use App\Models\Service;
use Database\Seeders\DirectionServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectionServiceSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_expected_directions_and_services(): void
    {
        $this->seed(DirectionServiceSeeder::class);

        $this->assertEquals(8, Direction::count());
        $this->assertDatabaseHas('directions', ['code' => 'DAAGL']);
        $this->assertDatabaseHas('directions', ['code' => 'DAARH']);
        $this->assertDatabaseHas('directions', ['code' => 'DG']);

        // Les services sont bien rattachés à la bonne direction
        $daagl = Direction::where('code', 'DAAGL')->firstOrFail();
        $this->assertDatabaseHas('services', [
            'nom' => 'Service Moyens Généraux',
            'direction_id' => $daagl->id,
        ]);
        $this->assertDatabaseHas('services', [
            'nom' => 'Service achat et approvisionnement',
            'direction_id' => $daagl->id,
        ]);
    }

    public function test_seeder_does_not_delete_referenced_services_or_existing_data(): void
    {
        // Simuler l'ancienne organisation : un service rattaché à DAF
        // et déjà utilisé par un rapport enregistré.
        $daf = Direction::create(['code' => 'DAF', 'nom' => 'DAF']);
        $ancienService = Service::create([
            'direction_id' => $daf->id,
            'nom' => 'Service Moyens Généraux',
        ]);

        $rapport = RapportMensuel::create([
            'direction_id' => $daf->id,
            'service_id' => $ancienService->id,
            'date_intervention' => '2026-08-01',
            'equipement_concerne' => 'Laptop',
            'utilisateur_equipement' => 'Agent X',
            'tache_effectuee' => 'Intervention historique',
            'observation' => 'OK',
        ]);

        // Relancer le seeder (mise à jour des directions/services)
        $this->seed(DirectionServiceSeeder::class);

        // Le rapport existant est intact
        $this->assertDatabaseHas('rapport_mensuels', [
            'id' => $rapport->id,
            'service_id' => $ancienService->id,
            'tache_effectuee' => 'Intervention historique',
        ]);

        // Le service référencé N'A PAS été supprimé
        $this->assertDatabaseHas('services', ['id' => $ancienService->id]);

        // Les nouvelles directions/services ont bien été ajoutés
        $this->assertDatabaseHas('directions', ['code' => 'DAAGL']);
        $this->assertEquals(8, Direction::count());
    }

    public function test_seeder_removes_only_unreferenced_obsolete_services(): void
    {
        $daf = Direction::create(['code' => 'DAF', 'nom' => 'DAF']);
        $obsolete = Service::create([
            'direction_id' => $daf->id,
            'nom' => 'Ancien Service Sans Données',
        ]);

        $this->seed(DirectionServiceSeeder::class);

        // Service obsolète et non référencé => supprimé
        $this->assertDatabaseMissing('services', ['id' => $obsolete->id]);

        // Les services de la nouvelle organisation sont présents
        $this->assertDatabaseHas('services', ['nom' => 'Service Informatique']);
    }
}
