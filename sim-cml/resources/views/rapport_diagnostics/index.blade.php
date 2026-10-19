@extends('layouts.app')

@section('content')
    <h1>Rapport de diagnostic - Historique</h1>

    <div class="actions" style="margin-bottom: 12px;">
        <a class="btn" href="{{ route('rapport-diagnostics.create') }}">Nouveau diagnostic</a>
        <a class="btn btn-secondary" href="{{ route('rapport-diagnostics.export.excel', ['month' => request('month')]) }}">Export Excel</a>
        <a class="btn btn-secondary" href="{{ route('rapport-diagnostics.export.pdf', ['month' => request('month')]) }}">Export PDF</a>
    </div>

    <form method="GET" action="{{ route('rapport-diagnostics.index') }}" style="margin-bottom: 12px;">
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
            <th>Type matériel</th>
            <th>Marque / Modèle</th>
            <th>Utilisateur</th>
            <th>État final</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td>{{ optional($item->date_diagnostic)->format('d/m/Y') }}</td>
                <td>{{ $item->direction->nom ?? '-' }}</td>
                <td>{{ $item->service->nom ?? '-' }}</td>
                <td>{{ \App\Models\RapportDiagnostic::typeMaterielLabel($item->type_materiel) }}</td>
                <td>{{ $item->marque_modele }}</td>
                <td>{{ $item->utilisateur_principal }}</td>
                <td>{{ \App\Models\RapportDiagnostic::etatFinalLabel($item->etat_final) }}</td>
                <td>
                    <div class="actions">
                        <a class="btn btn-secondary" href="{{ route('rapport-diagnostics.export.single-pdf', $item) }}">PDF</a>
                        <a class="btn btn-secondary" href="{{ route('rapport-diagnostics.edit', $item) }}">Modifier</a>
                        <form method="POST" action="{{ route('rapport-diagnostics.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit" onclick="return confirm('Supprimer ce diagnostic ?')">Supprimer</button>
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
