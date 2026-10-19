<?php

namespace Tests\Feature;

use App\Models\Direction;
use App\Models\RapportDiagnostic;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RapportDiagnosticTest extends TestCase
{
    use RefreshDatabase;

    private function makeDirectionService(): array
    {
        $direction = Direction::create(['code' => 'DIR-DIAG', 'nom' => 'Direction Diag']);
        $service = Service::create(['direction_id' => $direction->id, 'nom' => 'Service Diag']);

        return [$direction, $service];
    }

    private function validPayload(Direction $direction, Service $service, array $overrides = []): array
    {
        return array_merge([
            'date_diagnostic' => '2026-08-20',
            'direction_id' => $direction->id,
            'service_id' => $service->id,
            'lieu_intervention' => 'SITE MINIER LAUZOUA',
            'type_materiel' => 'LAPTOP',
            'quantite' => 2,
            'marque_modele' => 'HP ProBook',
            'utilisateur_principal' => 'Agent Y',
            'test_carte_reseau_designation' => 'CARTE RÉSEAU WIFI',
            'test_carte_reseau_statut' => 'ENDOMMAGE',
            'test_ram_statut' => 'BON_ETAT',
            'etat_final' => 'PARTIEL',
            'technicien_si' => 'Tech 1',
        ], $overrides);
    }

    public function test_diagnostic_can_be_created(): void
    {
        [$direction, $service] = $this->makeDirectionService();

        $response = $this->post(route('rapport-diagnostics.store'), $this->validPayload($direction, $service));

        $response->assertRedirect(route('rapport-diagnostics.index'));
        $this->assertDatabaseHas('rapport_diagnostics', [
            'type_materiel' => 'LAPTOP',
            'quantite' => 2,
            'etat_final' => 'PARTIEL',
            'test_carte_reseau_statut' => 'ENDOMMAGE',
        ]);
    }

    public function test_single_diagnostic_pdf_downloads(): void
    {
        [$direction, $service] = $this->makeDirectionService();
        $diagnostic = RapportDiagnostic::create($this->validPayload($direction, $service));

        $response = $this->get(route('rapport-diagnostics.export.single-pdf', $diagnostic));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_quantite_must_be_a_positive_integer(): void
    {
        [$direction, $service] = $this->makeDirectionService();

        $response = $this->post(route('rapport-diagnostics.store'), $this->validPayload($direction, $service, [
            'quantite' => 0,
        ]));

        $response->assertSessionHasErrors('quantite');
    }

    public function test_diagnostic_requires_valid_type_materiel(): void
    {
        [$direction, $service] = $this->makeDirectionService();

        $response = $this->post(route('rapport-diagnostics.store'), $this->validPayload($direction, $service, [
            'type_materiel' => 'TABLETTE',
        ]));

        $response->assertSessionHasErrors('type_materiel');
    }

    public function test_historique_is_ordered_by_date_diagnostic_desc(): void
    {
        [$direction, $service] = $this->makeDirectionService();

        // Enregistré en premier mais date la plus ancienne
        $ancien = RapportDiagnostic::create($this->validPayload($direction, $service, [
            'date_diagnostic' => '2026-06-15',
        ]));
        // Enregistré en second mais date la plus récente
        $recent = RapportDiagnostic::create($this->validPayload($direction, $service, [
            'date_diagnostic' => '2026-08-20',
        ]));

        $response = $this->get(route('rapport-diagnostics.index'));

        $response->assertOk();
        $dates = $response->viewData('items')->pluck('date_diagnostic')->map->format('Y-m-d')->all();
        $this->assertSame(['2026-08-20', '2026-06-15'], $dates);
    }

    public function test_diagnostic_update_rejects_stale_updated_at(): void
    {
        [$direction, $service] = $this->makeDirectionService();

        $diagnostic = RapportDiagnostic::create($this->validPayload($direction, $service));
        $staleUpdatedAt = optional($diagnostic->updated_at)->toISOString();

        $this->travel(1)->seconds();

        $diagnostic->update(['marque_modele' => 'Mise à jour concurrente']);

        $response = $this->from(route('rapport-diagnostics.edit', $diagnostic))
            ->put(route('rapport-diagnostics.update', $diagnostic), $this->validPayload($direction, $service, [
                'marque_modele' => 'Ma modification obsolète',
                'updated_at' => $staleUpdatedAt,
            ]));

        $response->assertRedirect(route('rapport-diagnostics.edit', $diagnostic));
        $response->assertSessionHasErrors('concurrency');

        $this->assertDatabaseHas('rapport_diagnostics', [
            'id' => $diagnostic->id,
            'marque_modele' => 'Mise à jour concurrente',
        ]);
    }

    public function test_diagnostic_index_page_loads(): void
    {
        $response = $this->get(route('rapport-diagnostics.index'));
        $response->assertOk();
    }

    public function test_diagnostic_excel_export_downloads(): void
    {
        [$direction, $service] = $this->makeDirectionService();
        RapportDiagnostic::create($this->validPayload($direction, $service));

        $response = $this->get(route('rapport-diagnostics.export.excel'));

        $response->assertOk();
        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('content-type')
        );
    }

    public function test_diagnostic_pdf_export_downloads(): void
    {
        [$direction, $service] = $this->makeDirectionService();
        RapportDiagnostic::create($this->validPayload($direction, $service));

        $response = $this->get(route('rapport-diagnostics.export.pdf'));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_existing_excel_exports_still_work(): void
    {
        $this->get(route('rapport-mensuels.export.excel'))->assertOk();
        $this->get(route('parc-informatiques.export.excel'))->assertOk();
    }

    public function test_new_materiel_types_and_designations_are_accepted(): void
    {
        [$direction, $service] = $this->makeDirectionService();

        foreach (['ONDULEURS', 'ROUTEURS', 'POINT_ACCES'] as $type) {
            $response = $this->post(route('rapport-diagnostics.store'), $this->validPayload($direction, $service, [
                'type_materiel' => $type,
                'inspection_connecteurs_designation' => 'Câble RJ45',
                'test_ram_designation' => 'DDR4 8Go',
            ]));

            $response->assertRedirect(route('rapport-diagnostics.index'));
            $this->assertDatabaseHas('rapport_diagnostics', [
                'type_materiel' => $type,
                'inspection_connecteurs_designation' => 'Câble RJ45',
                'test_ram_designation' => 'DDR4 8Go',
            ]);
        }
    }

    public function test_logo_is_detected_and_displayed_in_layout(): void
    {
        $this->assertTrue(\App\Support\AppLogo::exists());

        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSee('images/logo', false);
    }
}
