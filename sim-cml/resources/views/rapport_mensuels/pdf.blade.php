<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'SIM-CML RAPPORT MENSUEL' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #333; padding: 6px; }
        th { background: #efefef; }
    </style>
</head>
<body>
<h2>{{ $title ?? 'SIM-CML RAPPORT MENSUEL' }}</h2>

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
        <th>Crée le</th>
        <th>Mis à jour le</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td>{{ optional($item->date_intervention)->format('d/m/Y') }}</td>
            <td>{{ $item->direction->nom ?? '' }}</td>
            <td>{{ $item->service->nom ?? '' }}</td>
            <td>{{ $item->equipement_concerne }}</td>
            <td>{{ $item->utilisateur_equipement }}</td>
            <td>{{ $item->tache_effectuee }}</td>
            <td>{{ $item->observation }}</td>
            <td>{{ optional($item->created_at)->format('d-m-Y H:i:s') }}</td>
            <td>{{ optional($item->updated_at)->format('d-m-Y H:i:s') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
