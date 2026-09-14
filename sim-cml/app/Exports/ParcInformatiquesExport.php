<?php

namespace App\Exports;

use App\Models\ParcInformatique;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ParcInformatiquesExport implements FromCollection, WithHeadings
{
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
