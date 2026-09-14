<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRapportMensuelRequest extends FormRequest
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
            'date_intervention' => ['required', 'date'],
            'equipement_concerne' => ['required', 'string', 'max:255'],
            'utilisateur_equipement' => ['required', 'string', 'max:255'],
            'tache_effectuee' => ['required', 'string'],
            'observation' => ['required', Rule::in(['OK', 'EN_COURS'])],
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.exists' => 'Le service sélectionné est invalide.',
        ];
    }
}
