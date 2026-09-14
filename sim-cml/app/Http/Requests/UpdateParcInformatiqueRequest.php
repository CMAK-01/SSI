<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParcInformatiqueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'direction_id' => ['required', 'integer', 'exists:directions,id'],
            'service_id' => [
                'required',
                'integer',
                'exists:services,id',
                Rule::exists('services', 'id')->where(function ($query) {
                    return $query->where('direction_id', $this->input('direction_id'));
                }),
            ],
            'type_equipement' => ['required', Rule::in(['LAPTOP', 'DESKTOP', 'ECRAN', 'EQUIPEMENTS_RESEAUX', 'ONDULEURS_OU_AUTRES', 'POSTES_TELEPHONIQUES'])],
            'equipement_id_interne' => ['nullable', 'string', 'max:100'],
            'nom_materiel' => ['required', 'string', 'max:255'],
            'marque_modele' => ['required', 'string', 'max:255'],
            'os_logiciels' => ['nullable', 'string', 'max:255'],
            'utilisateur_assigne' => ['required', 'string', 'max:255'],
            'numero_bc' => ['nullable', 'string', 'max:100'],
            'date_achat' => ['nullable', 'date'],
            'prix_achat' => ['nullable', 'numeric', 'min:0'],
            'fin_garantie' => ['nullable', 'date', 'after_or_equal:date_achat'],
            'fournisseur' => ['nullable', 'string', 'max:255'],
            'etat' => ['required', Rule::in(['EN_SERVICE', 'HORS_SERVICE'])],
            'notes_libres' => ['nullable', 'string'],

            'note_ids' => ['nullable', 'array'],
            'note_ids.*' => ['integer', 'exists:parc_informatique_notes,id'],
            'autre_precision' => ['nullable', 'string', 'max:255'],
        ];
    }
}
