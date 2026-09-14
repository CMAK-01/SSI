<?php

namespace App\Imports;

use App\Models\Direction;
use App\Models\ParcInformatique;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParcInformatiquesImport implements ToCollection, WithHeadingRow
{
    private int $importedCount = 0;
    private array $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $rowArray = $row->toArray();

            $normalized = [];
            foreach ($rowArray as $key => $value) {
                $normalizedKey = $this->normalizeHeading($key);
                $normalized[$normalizedKey] = is_string($value) ? trim($value) : $value;
            }

            if (empty(array_filter($normalized, fn($v) => $v !== null && $v !== ''))) {
                continue;
            }

            try {
                $data = $this->mapRow($normalized, $line);
                ParcInformatique::create($data);
                $this->importedCount++;
            } catch (\Exception $e) {
                $this->errors[] = "Ligne {$line} : " . $e->getMessage();
            }
        }
    }

    private function mapRow(array $row, int $line): array
    {
        $directionName = $this->getValue($row, ['direction', 'direction_nom', 'nom_direction']);
        $serviceName = $this->getValue($row, ['service', 'service_nom', 'nom_service']);
        $typeEquipement = $this->getValue($row, ['type_equipement', 'type', 'type_equipements']);
        $equipementIdInterne = $this->getValue($row, ['id_interne_equipement', 'equipement_id_interne', 'id_equipement', 'id']);
        $nomMateriel = $this->getValue($row, ['nom_materiel', 'materiel', 'nom']);
        $marqueModele = $this->getValue($row, ['marque_modele', 'marque', 'modele', 'marque_modele']);
        $osLogiciels = $this->getValue($row, ['os_logiciels', 'os', 'logiciels']);
        $utilisateurAssigne = $this->getValue($row, ['utilisateur_assigne', 'utilisateur']);
        $numeroBc = $this->getValue($row, ['numero_bc', 'numero_bc', 'bc']);
        $dateAchat = $this->getValue($row, ['date_achat', 'date_achat']);
        $prixAchat = $this->getValue($row, ['prix_achat', 'prix']);
        $finGarantie = $this->getValue($row, ['fin_garantie', 'fin_garantie']);
        $fournisseur = $this->getValue($row, ['fournisseur']);
        $etat = $this->getValue($row, ['etat', 'etat_equipement']) ?? 'EN_SERVICE';
        $notesLibres = $this->getValue($row, ['notes_libres', 'notes', 'note']);

        if (empty($directionName)) {
            throw new \Exception("La direction est requise.");
        }
        if (empty($serviceName)) {
            throw new \Exception("Le service est requis.");
        }
        if (empty($typeEquipement)) {
            throw new \Exception("Le type d'équipement est requis.");
        }
        if (empty($nomMateriel)) {
            throw new \Exception("Le nom du matériel est requis.");
        }
        if (empty($marqueModele)) {
            throw new \Exception("La marque/modèle est requise.");
        }

        $direction = Direction::where('code', trim($directionName))
            ->orWhere('nom', 'like', trim($directionName))
            ->first();
        if (!$direction) {
            throw new \Exception("Direction '{$directionName}' introuvable.");
        }

        $service = Service::where('nom', 'like', trim($serviceName))
            ->where('direction_id', $direction->id)
            ->first();
        if (!$service) {
            throw new \Exception("Service '{$serviceName}' introuvable pour la direction '{$directionName}'.");
        }

        $parsedType = $this->parseTypeEquipement($typeEquipement);
        $parsedEtat = $this->parseEtat($etat);

        return [
            'direction_id' => $direction->id,
            'service_id' => $service->id,
            'type_equipement' => $parsedType,
            'equipement_id_interne' => $equipementIdInterne,
            'nom_materiel' => $nomMateriel,
            'marque_modele' => $marqueModele,
            'os_logiciels' => $osLogiciels,
            'utilisateur_assigne' => $utilisateurAssigne,
            'numero_bc' => $numeroBc,
            'date_achat' => $this->parseDate($dateAchat),
            'prix_achat' => $prixAchat !== null && $prixAchat !== '' ? (float) $prixAchat : null,
            'fin_garantie' => $this->parseDate($finGarantie),
            'fournisseur' => $fournisseur,
            'etat' => $parsedEtat,
            'notes_libres' => $notesLibres,
        ];
    }

    private function getValue(array $row, array $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (isset($row[$key]) && $row[$key] !== '' && $row[$key] !== null) {
                return $row[$key];
            }
        }
        return null;
    }

    private function parseTypeEquipement(string $value): string
    {
        $value = strtoupper(trim($value));
        $value = str_replace([' ', '-'], '_', $value);
        $value = preg_replace('/[^A-Z0-9_]/', '', $value);

        $mapping = [
            'LAPTOP' => 'LAPTOP',
            'DESKTOP' => 'DESKTOP',
            'ECRAN' => 'ECRAN',
            'EQUIPEMENTS_RESEAUX' => 'EQUIPEMENTS_RESEAUX',
            'EQUIPEMENT_RESEAU' => 'EQUIPEMENTS_RESEAUX',
            'ONDULEURS_OU_AUTRES' => 'ONDULEURS_OU_AUTRES',
            'ONDULEUR' => 'ONDULEURS_OU_AUTRES',
            'POSTES_TELEPHONIQUES' => 'POSTES_TELEPHONIQUES',
            'POSTE_TELEPHONIQUE' => 'POSTES_TELEPHONIQUES',
        ];

        if (isset($mapping[$value])) {
            return $mapping[$value];
        }

        // Recherche partielle
        foreach ($mapping as $key => $mapped) {
            if (str_contains($value, $key) || str_contains($key, $value)) {
                return $mapped;
            }
        }

        throw new \Exception("Type d'équipement '{$value}' non reconnu.");
    }

    private function parseEtat(string $value): string
    {
        $value = strtoupper(trim($value));
        $value = str_replace([' ', '-'], '_', $value);

        return str_contains($value, 'HORS') ? 'HORS_SERVICE' : 'EN_SERVICE';
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestamp(($value - 25569) * 86400)->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Format de date invalide : '{$value}'.");
        }
    }

    private function normalizeHeading(string $heading): string
    {
        $heading = Str::lower($heading);
        $heading = str_replace(
            ['à', 'á', 'â', 'ã', 'ä', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ'],
            ['a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y'],
            $heading
        );
        $heading = preg_replace('/[^a-z0-9]+/', '_', $heading);
        return trim($heading, '_');
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
