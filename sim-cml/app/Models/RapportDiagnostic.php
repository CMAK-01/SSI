<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RapportDiagnostic extends Model
{
    public const TYPES_MATERIEL = [
        'LAPTOP' => 'Laptop',
        'DESKTOP' => 'Desktop',
        'SERVEUR' => 'Serveur',
        'IMPRIMANTE' => 'Imprimante',
        'ONDULEURS' => 'Onduleurs',
        'ROUTEURS' => 'Routeurs',
        'POINT_ACCES' => "Point d'accès",
    ];

    public const STATUTS = [
        'BON_ETAT' => 'Bon état',
        'ENDOMMAGE' => 'Endommagé',
        'NON_TESTE' => 'Non testé',
    ];

    public const ETATS_FINAUX = [
        'FONCTIONNEL' => 'Fonctionnel',
        'PARTIEL' => 'Fonctionnement partiel',
        'NON_FONCTIONNEL' => 'Non fonctionnel',
    ];

    /**
     * Éléments de la section « Vérifications effectuées ».
     * Chaque clé possède deux colonnes : {clé}_statut et {clé}_observation.
     */
    public const VERIFICATIONS = [
        'inspection_connecteurs' => 'Inspection visuelle — Connecteurs (câbles, connecteurs, état physique)',
        'inspection_coque' => 'Inspection visuelle — Coque',
        'test_ram' => 'Test RAM',
        'test_disque' => 'Tests disque',
        'test_alimentation' => 'Tests alimentation',
        'test_carte_mere' => 'Tests carte mère',
        'test_pave_tactile' => 'Tests pavé tactile',
        'test_carte_reseau' => 'Tests carte réseau',
        'test_drivers' => 'Tests logiciels drivers',
        'test_bios' => 'Tests logiciels BIOS',
        'test_os' => 'Tests logiciels OS',
    ];

    protected $fillable = [
        'date_diagnostic',
        'direction_id',
        'service_id',
        'lieu_intervention',
        'type_materiel',
        'quantite',
        'marque_modele',
        'numero_serie_inventaire',
        'utilisateur_principal',
        'symptomes_signales',
        'contexte_apparition',
        'historique_incidents',
        'inspection_connecteurs_designation',
        'inspection_connecteurs_statut',
        'inspection_connecteurs_observation',
        'inspection_coque_designation',
        'inspection_coque_statut',
        'inspection_coque_observation',
        'test_ram_designation',
        'test_ram_statut',
        'test_ram_observation',
        'test_disque_designation',
        'test_disque_statut',
        'test_disque_observation',
        'test_alimentation_designation',
        'test_alimentation_statut',
        'test_alimentation_observation',
        'test_carte_mere_designation',
        'test_carte_mere_statut',
        'test_carte_mere_observation',
        'test_pave_tactile_designation',
        'test_pave_tactile_statut',
        'test_pave_tactile_observation',
        'test_carte_reseau_designation',
        'test_carte_reseau_statut',
        'test_carte_reseau_observation',
        'test_drivers_designation',
        'test_drivers_statut',
        'test_drivers_observation',
        'test_bios_designation',
        'test_bios_statut',
        'test_bios_observation',
        'test_os_designation',
        'test_os_statut',
        'test_os_observation',
        'resultats_diagnostics',
        'cause_identifiee',
        'facteurs_contributifs',
        'mesures_prises',
        'pieces_remplacees',
        'logiciels_maj',
        'recommandations_preventives',
        'recommandations_correctives',
        'etat_final',
        'technicien_si',
        'chef_service_informatique',
    ];

    protected $casts = [
        'date_diagnostic' => 'date',
        'quantite' => 'integer',
    ];

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public static function statutLabel(?string $statut): string
    {
        return self::STATUTS[$statut] ?? '-';
    }

    public static function typeMaterielLabel(?string $type): string
    {
        return self::TYPES_MATERIEL[$type] ?? ($type ?? '-');
    }

    public static function etatFinalLabel(?string $etat): string
    {
        return self::ETATS_FINAUX[$etat] ?? '-';
    }
}
