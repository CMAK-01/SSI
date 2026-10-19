@extends('layouts.app')

@section('content')
    <h1>Nouveau rapport de diagnostic</h1>

    <form method="POST" action="{{ route('rapport-diagnostics.store') }}">
        @csrf

        @include('rapport_diagnostics._form')

        <div class="actions">
            <button class="btn" type="submit">Enregistrer</button>
            <a class="btn btn-secondary" href="{{ route('rapport-diagnostics.index') }}">Retour</a>
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
