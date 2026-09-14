@extends('layouts.app')

@section('content')
    <h1>Parc Informatique Mine - Historique</h1>

    <div class="actions" style="margin-bottom: 12px;">
        <a class="btn" href="{{ route('parc-informatiques.create') }}">Nouvel équipement</a>
        <a class="btn btn-secondary" href="{{ route('parc-informatiques.export.excel') }}">Export Excel</a>
        <a class="btn btn-secondary" href="{{ route('parc-informatiques.export.pdf') }}">Export PDF</a>
    </div>

    <div class="card" style="margin-bottom: 16px; padding: 16px; background: #f0f9ff; border: 1px solid #bae6fd;">
        <h3 style="margin-top: 0; margin-bottom: 12px; font-size: 16px;">Importer depuis Excel</h3>
        <form method="POST" action="{{ route('parc-informatiques.import') }}" enctype="multipart/form-data" style="display: flex; gap: 12px; align-items: flex-end;">
            @csrf
            <div class="field" style="flex: 1; margin-bottom: 0;">
                <label for="fichier_excel">Fichier Excel (.xlsx, .xls, .csv)</label>
                <input type="file" id="fichier_excel" name="fichier_excel" accept=".xlsx,.xls,.csv" required>
            </div>
            <button class="btn" type="submit">Importer</button>
        </form>
        <p style="margin-top: 8px; font-size: 12px; color: #475569;">
            Colonnes attendues : Direction, Service, Type équipement, ID interne équipement, Nom matériel, Marque / Modèle, OS / Logiciels, Utilisateur assigné, Numéro BC, Date achat, Prix achat, Fin garantie, Fournisseur, État, Notes libres
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

    <table>
        <thead>
        <tr>
            <th>Direction</th>
            <th>Service</th>
            <th>Type</th>
            <th>ID Équipement</th>
            <th>Nom matériel</th>
            <th>Utilisateur</th>
            <th>État</th>
            <th>Notes</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td>{{ $item->direction->nom ?? '-' }}</td>
                <td>{{ $item->service->nom ?? '-' }}</td>
                <td>{{ str_replace('_', ' ', $item->type_equipement) }}</td>
                <td>{{ $item->equipement_id_interne }}</td>
                <td>{{ $item->nom_materiel }}</td>
                <td>{{ $item->utilisateur_assigne }}</td>
                <td>{{ $item->etat }}</td>
                <td>
                    @foreach($item->noteItems as $noteItem)
                        <span>{{ $noteItem->note->label ?? '' }}</span>@if(!$loop->last), @endif
                    @endforeach
                </td>
                <td>
                    <div class="actions">
                        <a class="btn btn-secondary" href="{{ route('parc-informatiques.edit', $item) }}">Modifier</a>
                        <form method="POST" action="{{ route('parc-informatiques.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit" onclick="return confirm('Supprimer cet élément ?')">Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="9">Aucune donnée.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top: 12px;">
        {{ $items->links() }}
    </div>
@endsection
