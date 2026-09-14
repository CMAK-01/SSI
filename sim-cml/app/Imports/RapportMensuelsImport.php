<?php

namespace App\Imports;

use App\Models\Direction;
use App\Models\RapportMensuel;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RapportMensuelsImport implements ToCollection, WithHeadingRow
{
    private int $importedCount = 0;
    private array $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $line = $index + 2; // +1 pour l'en-tête, +1 pour l'index 0-based
            $rowArray = $row->toArray();

            // Normaliser les clés pour être insensible aux accents et à la casse
            $normalized = [];
            foreach ($rowArray as $key => $value) {
                $normalizedKey = $this->normalizeHeading($key);
                $normalized[$normalizedKey] = is_string($value) ? trim($value) : $value;
            }

            // Ignorer les lignes vides
            if (empty(array_filter($normalized, fn($v) => $v !== null && $v !== ''))) {
                continue;
            }

            try {
                $data = $this->mapRow($normalized, $line);
                RapportMensuel::create($data);
                $this->importedCount++;
            } catch (\Exception $e) {
                $this->errors[] = "Ligne {$line} : " . $e->getMessage();
            }
        }
    }

    private function mapRow(array $row, int $line): array
    {
        // Récupération des valeurs par en-têtes normalisés
        $dateValue = $this->getValue($row, ['date', 'date_intervention', 'date intervention']);
        $directionName = $this->getValue($row, ['direction', 'direction_nom', 'nom_direction']);
        $serviceName = $this->getValue($row, ['service', 'service_nom', 'nom_service']);
        $equipement = $this->getValue($row, ['equipement', 'equipement_concerne', 'materiel']);
        $utilisateur = $this->getValue($row, ['utilisateur', 'utilisateur_equipement', 'utilisateur equipement']);
        $tache = $this->getValue($row, ['tache', 'tache_effectuee', 'tache effectuee', 'taches']);
        $observation = $this->getValue($row, ['observation', 'observations', 'statut']) ?? 'OK';

        if (empty($dateValue)) {
            throw new \Exception("La date est requise.");
        }
        if (empty($directionName)) {
            throw new \Exception("La direction est requise.");
        }
        if (empty($serviceName)) {
            throw new \Exception("Le service est requis.");
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

        return [
            'direction_id' => $direction->id,
            'service_id' => $service->id,
            'date_intervention' => $this->parseDate($dateValue),
            'equipement_concerne' => $equipement ?? 'Non spécifié',
            'utilisateur_equipement' => $utilisateur ?? 'Non spécifié',
            'tache_effectuee' => $tache ?? 'Non spécifié',
            'observation' => $this->parseObservation($observation),
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

    private function parseDate($value): string
    {
        if (is_numeric($value)) {
            // Date Excel (timestamp)
            return Carbon::createFromTimestamp(($value - 25569) * 86400)->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Format de date invalide : '{$value}'.");
        }
    }

    private function parseObservation(string $value): string
    {
        $value = strtoupper(trim($value));
        $value = str_replace([' ', '-', 'é', 'è'], ['_', '_', 'E', 'E'], $value);

        return in_array($value, ['EN_COURS', 'ENCOURS']) ? 'EN_COURS' : 'OK';
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
