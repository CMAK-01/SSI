<?php

namespace App\Exports;

use App\Models\ParcInformatique;
use App\Support\AppLogo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ParcInformatiquesExport implements FromCollection, WithCustomStartCell, WithDrawings, WithHeadings
{
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
        return ParcInformatique::query()
            ->with(['direction:id,nom', 'service:id,nom'])
            ->get()
            ->map(function (ParcInformatique $item) {
                return [
                    $item->id,
                    optional($item->direction)->nom,
                    optional($item->service)->nom,
                    str_replace('_', ' ', $item->type_equipement),
                    $item->equipement_id_interne,
                    $item->nom_materiel,
                    $item->marque_modele,
                    $item->os_logiciels,
                    $item->utilisateur_assigne,
                    $item->numero_bc,
                    optional($item->date_achat)->format('d-m-Y'),
                    $item->prix_achat,
                    optional($item->fin_garantie)->format('d-m-Y'),
                    $item->fournisseur,
                    $item->etat,
                    $item->notes_libres,
                    optional($item->created_at)->format('d-m-Y H:i:s'),
                    optional($item->updated_at)->format('d-m-Y H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Direction',
            'Service',
            'Type équipement',
            'ID interne équipement',
            'Nom matériel',
            'Marque / Modèle',
            'OS / Logiciels',
            'Utilisateur assigné',
            'Numéro BC',
            'Date achat',
            'Prix achat',
            'Fin garantie',
            'Fournisseur',
            'État',
            'Notes libres',
            'Crée le',
            'Mis à jour le',
        ];
    }
}
