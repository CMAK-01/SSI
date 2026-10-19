<?php

namespace App\Exports;

use App\Models\RapportDiagnostic;
use App\Support\AppLogo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RapportDiagnosticsExport implements FromCollection, WithCustomStartCell, WithDrawings, WithHeadings
{
    private ?string $from;
    private ?string $to;

    public function __construct(?string $from = null, ?string $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Les données commencent à la ligne 5 pour laisser la place au logo.
     */
    public function startCell(): string
    {
        return 'A5';
    }

    /**
     * Insère le logo de l'application en haut du fichier Excel.
     */
    public function drawings()
    {
        $logo = AppLogo::publicPath();

        if (!$logo) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setPath($logo);
        $drawing->setHeight(70);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = RapportDiagnostic::query()
            ->with(['direction:id,nom', 'service:id,nom'])
            ->orderBy('date_diagnostic', 'desc')
            ->orderBy('id', 'desc');

        if ($this->from) {
            $query->whereDate('date_diagnostic', '>=', $this->from);
        }

        if ($this->to) {
            $query->whereDate('date_diagnostic', '<=', $this->to);
        }

        return $query->get()->map(function (RapportDiagnostic $item) {
            $row = [
                optional($item->date_diagnostic)->format('d-m-Y'),
                optional($item->direction)->nom,
                optional($item->service)->nom,
                $item->lieu_intervention,
                RapportDiagnostic::typeMaterielLabel($item->type_materiel),
                $item->quantite,
                $item->marque_modele,
                $item->numero_serie_inventaire,
                $item->utilisateur_principal,
                $item->symptomes_signales,
                $item->contexte_apparition,
                $item->historique_incidents,
            ];

            foreach (array_keys(RapportDiagnostic::VERIFICATIONS) as $key) {
                $row[] = $item->{$key . '_designation'};
                $row[] = RapportDiagnostic::statutLabel($item->{$key . '_statut'});
                $row[] = $item->{$key . '_observation'};
            }

            $row[] = $item->resultats_diagnostics;
            $row[] = $item->cause_identifiee;
            $row[] = $item->facteurs_contributifs;
            $row[] = $item->mesures_prises;
            $row[] = $item->pieces_remplacees;
            $row[] = $item->logiciels_maj;
            $row[] = $item->recommandations_preventives;
            $row[] = $item->recommandations_correctives;
            $row[] = RapportDiagnostic::etatFinalLabel($item->etat_final);
            $row[] = $item->technicien_si;
            $row[] = $item->chef_service_informatique;
            $row[] = optional($item->created_at)->format('d-m-Y H:i:s');
            $row[] = optional($item->updated_at)->format('d-m-Y H:i:s');

            return $row;
        });
    }

    public function headings(): array
    {
        $headings = [
            'Date diagnostic',
            'Direction',
            'Service',
            'Lieu d\'intervention',
            'Type de matériel',
            'Quantité',
            'Marque / Modèle',
            'N° série / Inventaire',
            'Utilisateur principal',
            'Symptômes signalés',
            'Contexte d\'apparition',
            'Historique incidents similaires',
        ];

        foreach (RapportDiagnostic::VERIFICATIONS as $label) {
            $headings[] = $label . ' — Désignation';
            $headings[] = $label . ' — Statut';
            $headings[] = $label . ' — Observation';
        }

        $headings[] = 'Résultats diagnostics';
        $headings[] = 'Cause identifiée';
        $headings[] = 'Facteurs contributifs';
        $headings[] = 'Mesures prises';
        $headings[] = 'Pièces remplacées / réparées';
        $headings[] = 'Logiciels réinstallés / mis à jour';
        $headings[] = 'Recommandations préventives';
        $headings[] = 'Recommandations correctives';
        $headings[] = 'État final du matériel';
        $headings[] = 'Technicien SI';
        $headings[] = 'Chef de service informatique';
        $headings[] = 'Crée le';
        $headings[] = 'Mis à jour le';

        return $headings;
    }
}
