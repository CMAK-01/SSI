<?php

namespace App\Exports;

use App\Models\RapportMensuel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RapportMensuelsExport implements FromCollection, WithHeadings
{
    private ?string $from;
    private ?string $to;

    public function __construct(?string $from = null, ?string $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = RapportMensuel::query()->with(['direction:id,nom', 'service:id,nom']);

        if ($this->from) {
            $query->whereDate('date_intervention', '>=', $this->from);
        }

        if ($this->to) {
            $query->whereDate('date_intervention', '<=', $this->to);
        }

        return $query->get()->map(function (RapportMensuel $rapport) {
            return [
                optional($rapport->date_intervention)->format('d-m-Y'),
                optional($rapport->direction)->nom,
                optional($rapport->service)->nom,
                $rapport->equipement_concerne,
                $rapport->utilisateur_equipement,
                $rapport->tache_effectuee,
                $rapport->observation,
                optional($rapport->created_at)->format('d-m-Y H:i:s'),
                optional($rapport->updated_at)->format('d-m-Y H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Direction',
            'Service',
            'Equipement',
            'Utilisateur',
            'Tâche',
            'Observation',
            'Crée le',
            'Mis à jour le',
        ];
    }
}
