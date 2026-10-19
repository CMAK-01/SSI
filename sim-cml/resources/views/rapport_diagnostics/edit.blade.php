@extends('layouts.app')

@section('content')
    <h1>Modifier rapport de diagnostic</h1>

    <form method="POST" action="{{ route('rapport-diagnostics.update', $rapportDiagnostic) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="updated_at" value="{{ optional($rapportDiagnostic->updated_at)->toISOString() }}">

        @include('rapport_diagnostics._form')

        <div class="actions">
            <button class="btn" type="submit">Mettre à jour</button>
            <a class="btn btn-secondary" href="{{ route('rapport-diagnostics.index') }}">Retour</a>
        </div>
    </form>

    <script>
        const directionEl = document.getElementById('direction_id');
        const serviceEl = document.getElementById('service_id');
        const selectedService = "{{ old('service_id', $rapportDiagnostic->service_id) }}";

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
