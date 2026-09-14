@extends('layouts.app')

@section('content')
    <h1>Nouvel équipement - Parc Informatique Mine</h1>

    <form method="POST" action="{{ route('parc-informatiques.store') }}">
        @csrf

        <div class="field">
            <label for="direction_id">Direction</label>
            <select name="direction_id" id="direction_id" required>
                <option value="">-- choisir --</option>
                @foreach($directions as $direction)
                    <option value="{{ $direction->id }}" {{ old('direction_id') == $direction->id ? 'selected' : '' }}>
                        {{ $direction->code }} - {{ $direction->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="service_id">Service</label>
            <select name="service_id" id="service_id" required></select>
        </div>

        <div class="field">
            <label for="type_equipement">Type d'équipement</label>
            <select name="type_equipement" id="type_equipement" required>
                @foreach(['LAPTOP','DESKTOP','ECRAN','EQUIPEMENTS_RESEAUX','ONDULEURS_OU_AUTRES','POSTES_TELEPHONIQUES'] as $type)
                    <option value="{{ $type }}" {{ old('type_equipement') === $type ? 'selected' : '' }}>{{ str_replace('_', ' ', $type) }}</option>
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
            @endphp
            <div class="field">
                <label for="{{ $name }}">{{ $label }}</label>
                <input type="{{ str_contains($name, 'date') || str_contains($name, 'garantie') ? 'date' : (str_contains($name, 'prix') ? 'number' : 'text') }}"
                       step="{{ str_contains($name, 'prix') ? '0.01' : '' }}"
                       id="{{ $name }}" name="{{ $name }}" value="{{ old($name) }}" {{ $isOptional ? '' : 'required' }}>
            </div>
        @endforeach

        <div class="field">
            <label for="etat">Etat</label>
            <select name="etat" id="etat" required>
                <option value="EN_SERVICE" {{ old('etat') === 'EN_SERVICE' ? 'selected' : '' }}>En service</option>
                <option value="HORS_SERVICE" {{ old('etat') === 'HORS_SERVICE' ? 'selected' : '' }}>Hors service</option>
            </select>
        </div>

        <div class="field">
            <label>Notes (choix multiples)</label>
            @foreach($notes as $note)
                <div>
                    <input type="checkbox" name="note_ids[]" value="{{ $note->id }}" id="note_{{ $note->id }}"
                        {{ in_array($note->id, old('note_ids', [])) ? 'checked' : '' }}>
                    <label for="note_{{ $note->id }}">{{ $note->label }}</label>
                </div>
            @endforeach
        </div>

        <div class="field">
            <label for="autre_precision">Autres (à préciser)</label>
            <input type="text" id="autre_precision" name="autre_precision" value="{{ old('autre_precision') }}">
        </div>

        <div class="actions">
            <button class="btn" type="submit">Enregistrer</button>
            <a class="btn btn-secondary" href="{{ route('parc-informatiques.index') }}">Retour</a>
        </div>
    </form>

    <script>
        const directionEl = document.getElementById('direction_id');
        const serviceEl = document.getElementById('service_id');
        const oldServiceId = "{{ old('service_id') }}";

        async function loadServices(directionId, selectedServiceId = null) {
            serviceEl.innerHTML = '<option value="">Chargement...</option>';
            if (!directionId) {
                serviceEl.innerHTML = '<option value="">-- choisir une direction --</option>';
                return;
            }

            const response = await fetch(`/services/by-direction/${directionId}`);
            const services = await response.json();

            serviceEl.innerHTML = '<option value="">-- choisir --</option>';
            services.forEach(service => {
                const opt = document.createElement('option');
                opt.value = service.id;
                opt.textContent = service.nom;
                if (selectedServiceId && String(selectedServiceId) === String(service.id)) {
                    opt.selected = true;
                }
                serviceEl.appendChild(opt);
            });
        }

        directionEl.addEventListener('change', () => loadServices(directionEl.value));

        if (directionEl.value) {
            loadServices(directionEl.value, oldServiceId);
        } else {
            serviceEl.innerHTML = '<option value="">-- choisir une direction --</option>';
        }
    </script>
@endsection
