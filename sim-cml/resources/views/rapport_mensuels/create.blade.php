@extends('layouts.app')

@section('content')
    <h1>Nouveau rapport mensuel</h1>

    <form method="POST" action="{{ route('rapport-mensuels.store') }}">
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
            <label for="date_intervention">Date d'intervention</label>
            <input type="date" id="date_intervention" name="date_intervention" value="{{ old('date_intervention') }}" required>
        </div>

        <div class="field">
            <label for="equipement_concerne">Équipement concerné</label>
            <select id="equipement_concerne" name="equipement_concerne" required>
                <option value="">-- choisir --</option>
                @foreach(['Laptop', 'Desktop', 'Ecran', 'Equipements reseaux', 'Onduleurs ou autres', 'Postes telephoniques', 'Printers', 'Téléphones portables'] as $equipement)
                    <option value="{{ $equipement }}" {{ old('equipement_concerne') === $equipement ? 'selected' : '' }}>
                        {{ $equipement }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="utilisateur_equipement">Utilisateur de l'équipement</label>
            <input type="text" id="utilisateur_equipement" name="utilisateur_equipement" value="{{ old('utilisateur_equipement') }}" required>
        </div>

        <div class="field">
            <label for="tache_effectuee">Tâche effectuée</label>
            <textarea id="tache_effectuee" name="tache_effectuee" required>{{ old('tache_effectuee') }}</textarea>
        </div>

        <div class="field">
            <label for="observation">Observation</label>
            <select id="observation" name="observation" required>
                <option value="OK" {{ old('observation') === 'OK' ? 'selected' : '' }}>OK</option>
                <option value="EN_COURS" {{ old('observation') === 'EN_COURS' ? 'selected' : '' }}>EN COURS</option>
            </select>
        </div>

        <div class="actions">
            <button class="btn" type="submit">Enregistrer</button>
            <a class="btn btn-secondary" href="{{ route('rapport-mensuels.index') }}">Retour</a>
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
