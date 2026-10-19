<?php

namespace App\Exports;

use App\Models\RapportMensuel;
use App\Support\AppLogo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RapportMensuelsExport implements FromCollection, WithCustomStartCell, WithDrawings, WithHeadings
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
