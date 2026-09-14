@extends('layouts.app')

@section('content')
    <h1>Modifier équipement - Parc Informatique Mine</h1>

    <form method="POST" action="{{ route('parc-informatiques.update', $parcInformatique) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="updated_at" value="{{ optional($parcInformatique->updated_at)->toISOString() }}">

        <div class="field">
            <label for="direction_id">Direction</label>
            <select name="direction_id" id="direction_id" required>
                <option value="">-- choisir --</option>
                @foreach($directions as $direction)
                    <option value="{{ $direction->id }}" {{ old('direction_id', $parcInformatique->direction_id) == $direction->id ? 'selected' : '' }}>
                        {{ $direction->code }} - {{ $direction->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="service_id">Service</label>
            <select name="service_id" id="service_id" required>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id', $parcInformatique->service_id) == $service->id ? 'selected' : '' }}>
                        {{ $service->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="type_equipement">Type d'équipement</label>
            <select name="type_equipement" id="type_equipement" required>
                @foreach(['LAPTOP','DESKTOP','ECRAN','EQUIPEMENTS_RESEAUX','ONDULEURS_OU_AUTRES','POSTES_TELEPHONIQUES'] as $type)
                    <option value="{{ $type }}" {{ old('type_equipement', $parcInformatique->type_equipement) === $type ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', $type) }}
                    </option>
                @endforeach
            </select>
        </div>

        @foreach([
            'equipement_id_interne' => "ID de l'équipement",
            'nom_materiel' => 'Nom du matériel',
            'marque_modele' => 'Marque/Modèle',
            'os_logiciels' => 'OS/Logiciels',
            'utilisateur_assigne' => 'Utilisateur assigné',
            'numero_bc' => 'Numéro du BC',
            'date_achat' => 'Date achat',
            'prix_achat' => "Prix d'achat",
            'fin_garantie' => 'Fin de garantie',
            'fournisseur' => 'Fournisseur'
        ] as $name => $label)
            @php
                $isOptional = in_array($name, ['equipement_id_interne', 'numero_bc', 'date_achat', 'prix_achat', 'fin_garantie', 'fournisseur']);
                $value = old($name, $parcInformatique->{$name});
            @endphp
            <div class="field">
                <label for="{{ $name }}">{{ $label }}</label>
                <input type="{{ str_contains($name, 'date') || str_contains($name, 'garantie') ? 'date' : (str_contains($name, 'prix') ? 'number' : 'text') }}"
                       step="{{ str_contains($name, 'prix') ? '0.01' : '' }}"
                       id="{{ $name }}" name="{{ $name }}"
                       value="{{ (str_contains($name, 'date') || str_contains($name, 'garantie')) && $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d') : $value }}"
                       {{ $isOptional ? '' : 'required' }}>
            </div>
        @endforeach

        <div class="field">
            <label for="etat">Etat</label>
            <select name="etat" id="etat" required>
                <option value="EN_SERVICE" {{ old('etat', $parcInformatique->etat) === 'EN_SERVICE' ? 'selected' : '' }}>En service</option>
                <option value="HORS_SERVICE" {{ old('etat', $parcInformatique->etat) === 'HORS_SERVICE' ? 'selected' : '' }}>Hors service</option>
            </select>
        </div>

        <div class="field">
            <label>Notes (choix multiples)</label>
            @php $oldNoteIds = old('note_ids', $selectedNoteIds ?? []); @endphp
            @foreach($notes as $note)
                <div>
                    <input type="checkbox" name="note_ids[]" value="{{ $note->id }}" id="note_{{ $note->id }}"
                        {{ in_array($note->id, $oldNoteIds) ? 'checked' : '' }}>
                    <label for="note_{{ $note->id }}">{{ $note->label }}</label>
                </div>
            @endforeach
        </div>

        <div class="field">
            <label for="autre_precision">Autres (à préciser)</label>
            <input type="text" id="autre_precision" name="autre_precision" value="{{ old('autre_precision', $autrePrecision) }}">
        </div>

        <div class="actions">
            <button class="btn" type="submit">Mettre à jour</button>
            <a class="btn btn-secondary" href="{{ route('parc-informatiques.index') }}">Retour</a>
        </div>
    </form>

    <script>
        const directionEl = document.getElementById('direction_id');
        const serviceEl = document.getElementById('service_id');
        const selectedService = "{{ old('service_id', $parcInformatique->service_id) }}";

        async function loadServices(directionId) {
            if (!directionId) return;
            const response = await fetch(`/services/by-direction/${directionId}`);
            const services = await response.json();

            serviceEl.innerHTML = '<option value="">-- choisir --</option>';
            services.forEach(service => {
                const opt = document.createElement('option');
                opt.value = service.id;
                opt.textContent = service.nom;
                if (String(service.id) === String(selectedService)) opt.selected = true;
                serviceEl.appendChild(opt);
            });
        }

        directionEl.addEventListener('change', async () => {
            const current = serviceEl.value;
            await loadServices(directionEl.value);
            if (current) serviceEl.value = current;
        });
    </script>
@endsection
