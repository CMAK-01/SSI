<?php

namespace App\Http\Requests;

use App\Models\RapportDiagnostic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRapportDiagnosticRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'date_diagnostic' => ['required', 'date'],
            'direction_id' => ['required', 'integer', 'exists:directions,id'],
            'service_id' => [
                'required',
                'integer',
                'exists:services,id',
                Rule::exists('services', 'id')->where(function ($query) {
                    return $query->where('direction_id', $this->input('direction_id'));
                }),
            ],
            'lieu_intervention' => ['nullable', 'string', 'max:255'],
            'type_materiel' => ['required', Rule::in(array_keys(RapportDiagnostic::TYPES_MATERIEL))],
            'quantite' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'marque_modele' => ['nullable', 'string', 'max:255'],
            'numero_serie_inventaire' => ['nullable', 'string', 'max:255'],
            'utilisateur_principal' => ['nullable', 'string', 'max:255'],
            'symptomes_signales' => ['nullable', 'string'],
            'contexte_apparition' => ['nullable', 'string'],
            'historique_incidents' => ['nullable', 'string'],
            'resultats_diagnostics' => ['nullable', 'string'],
            'cause_identifiee' => ['nullable', 'string'],
            'facteurs_contributifs' => ['nullable', 'string'],
            'mesures_prises' => ['nullable', 'string'],
            'pieces_remplacees' => ['nullable', 'string'],
            'logiciels_maj' => ['nullable', 'string'],
            'recommandations_preventives' => ['nullable', 'string'],
            'recommandations_correctives' => ['nullable', 'string'],
            'etat_final' => ['required', Rule::in(array_keys(RapportDiagnostic::ETATS_FINAUX))],
            'technicien_si' => ['nullable', 'string', 'max:255'],
            'chef_service_informatique' => ['nullable', 'string', 'max:255'],
        ];

        foreach (array_keys(RapportDiagnostic::VERIFICATIONS) as $key) {
            $rules[$key . '_designation'] = ['nullable', 'string', 'max:255'];
            $rules[$key . '_statut'] = ['nullable', Rule::in(array_keys(RapportDiagnostic::STATUTS))];
            $rules[$key . '_observation'] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'service_id.exists' => 'Le service sélectionné est invalide.',
        ];
    }
}
