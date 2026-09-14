<?php

namespace Tests\Feature;

use App\Models\Direction;
use App\Models\ParcInformatique;
use App\Models\RapportMensuel;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcurrencyUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_rapport_mensuel_update_rejects_stale_updated_at(): void
    {
        $direction = Direction::create([
            'code' => 'DIR-T1',
            'nom' => 'Direction Test 1',
        ]);

        $service = Service::create([
            'direction_id' => $direction->id,
            'nom' => 'Service Test 1',
        ]);

        $rapport = RapportMensuel::create([
            'direction_id' => $direction->id,
            'service_id' => $service->id,
            'date_intervention' => '2026-07-20',
            'equipement_concerne' => 'Laptop',
            'utilisateur_equipement' => 'User A',
            'tache_effectuee' => 'Initial task',
            'observation' => 'OK',
        ]);

        $staleUpdatedAt = optional($rapport->updated_at)->toISOString();

        $rapport->update([
            'tache_effectuee' => 'Concurrent update',
        ]);

        $payload = [
            'direction_id' => $direction->id,
            'service_id' => $service->id,
            'date_intervention' => '2026-07-20',
            'equipement_concerne' => 'Laptop',
            'utilisateur_equipement' => 'User A',
            'tache_effectuee' => 'My stale update',
            'observation' => 'OK',
            'updated_at' => $staleUpdatedAt,
        ];

        $response = $this->from(route('rapport-mensuels.edit', $rapport))
            ->put(route('rapport-mensuels.update', $rapport), $payload);

        $response->assertRedirect(route('rapport-mensuels.edit', $rapport));
        $response->assertSessionHasErrors('concurrency');

        $this->assertDatabaseHas('rapport_mensuels', [
            'id' => $rapport->id,
            'tache_effectuee' => 'Concurrent update',
        ]);
    }

    public function test_parc_informatique_update_rejects_stale_updated_at(): void
    {
        $direction = Direction::create([
            'code' => 'DIR-T2',
            'nom' => 'Direction Test 2',
        ]);

        $service = Service::create([
            'direction_id' => $direction->id,
            'nom' => 'Service Test 2',
        ]);

        $item = ParcInformatique::create([
            'direction_id' => $direction->id,
            'service_id' => $service->id,
            'type_equipement' => 'LAPTOP',
            'equipement_id_interne' => 'INT-001',
            'nom_materiel' => 'Materiel A',
            'marque_modele' => 'Model A',
            'os_logiciels' => 'Windows',
            'utilisateur_assigne' => 'Agent 1',
            'numero_bc' => null,
            'date_achat' => null,
            'prix_achat' => null,
            'fin_garantie' => null,
            'fournisseur' => null,
            'etat' => 'EN_SERVICE',
            'notes_libres' => null,
        ]);

        $staleUpdatedAt = optional($item->updated_at)->toISOString();

        $item->update([
            'nom_materiel' => 'Materiel Concurrent',
        ]);

        $payload = [
            'direction_id' => $direction->id,
            'service_id' => $service->id,
            'type_equipement' => 'LAPTOP',
            'equipement_id_interne' => 'INT-001',
            'nom_materiel' => 'Materiel Stale',
            'marque_modele' => 'Model A',
            'os_logiciels' => 'Windows',
            'utilisateur_assigne' => 'Agent 1',
            'numero_bc' => null,
            'date_achat' => null,
            'prix_achat' => null,
            'fin_garantie' => null,
            'fournisseur' => null,
            'etat' => 'EN_SERVICE',
            'notes_libres' => null,
            'updated_at' => $staleUpdatedAt,
        ];

        $response = $this->from(route('parc-informatiques.edit', $item))
            ->put(route('parc-informatiques.update', $item), $payload);

        $response->assertRedirect(route('parc-informatiques.edit', $item));
        $response->assertSessionHasErrors('concurrency');

        $this->assertDatabaseHas('parc_informatiques', [
            'id' => $item->id,
            'nom_materiel' => 'Materiel Concurrent',
        ]);
    }
}
