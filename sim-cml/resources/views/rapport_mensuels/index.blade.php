@extends('layouts.app')

@section('content')
    <h1>Rapport mensuel - Historique</h1>

    <div class="actions" style="margin-bottom: 12px;">
        <a class="btn" href="{{ route('rapport-mensuels.create') }}">Nouveau rapport</a>
        <a class="btn btn-secondary" href="{{ route('rapport-mensuels.export.excel', ['month' => request('month')]) }}">Export Excel</a>
        <a class="btn btn-secondary" href="{{ route('rapport-mensuels.export.pdf', ['month' => request('month')]) }}">Export PDF</a>
    </div>

    <div class="card" style="margin-bottom: 16px; padding: 16px; background: #f0f9ff; border: 1px solid #bae6fd;">
        <h3 style="margin-top: 0; margin-bottom: 12px; font-size: 16px;">Importer depuis Excel</h3>
        <form method="POST" action="{{ route('rapport-mensuels.import') }}" enctype="multipart/form-data" style="display: flex; gap: 12px; align-items: flex-end;">
            @csrf
            <div class="field" style="flex: 1; margin-bottom: 0;">
                <label for="fichier_excel">Fichier Excel (.xlsx, .xls, .csv)</label>
                <input type="file" id="fichier_excel" name="fichier_excel" accept=".xlsx,.xls,.csv" required>
            </div>
            <button class="btn" type="submit">Importer</button>
        </form>
        <p style="margin-top: 8px; font-size: 12px; color: #475569;">
            Colonnes attendues : Date, Direction, Service, Equipement, Utilisateur, Tâche, Observation
        </p>
    </div>

    @if(session('import_errors'))
        <div class="alert alert-error" style="margin-bottom: 16px;">
            <strong>Erreurs lors de l'import :</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('rapport-mensuels.index') }}" style="margin-bottom: 12px;">
        <div class="field" style="max-width: 260px;">
            <label for="month">Filtrer par mois</label>
            <input type="month" id="month" name="month" value="{{ request('month') }}">
        </div>
        <button class="btn" type="submit">Filtrer</button>
    </form>

    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Direction</th>
            <th>Service</th>
            <th>Équipement</th>
            <th>Utilisateur</th>
            <th>Tâche</th>
            <th>Observation</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td>{{ optional($item->date_intervention)->format('d/m/Y') }}</td>
                <td>{{ $item->direction->nom ?? '-' }}</td>
                <td>{{ $item->service->nom ?? '-' }}</td>
                <td>{{ $item->equipement_concerne }}</td>
                <td>{{ $item->utilisateur_equipement }}</td>
                <td>{{ $item->tache_effectuee }}</td>
                <td>{{ $item->observation }}</td>
                <td>
                    <div class="actions">
                        <a class="btn btn-secondary" href="{{ route('rapport-mensuels.edit', $item) }}">Modifier</a>
                        <form method="POST" action="{{ route('rapport-mensuels.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit" onclick="return confirm('Supprimer cet enregistrement ?')">Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="8">Aucune donnée.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:12px;">
        {{ $items->links() }}
    </div>
@endsection
