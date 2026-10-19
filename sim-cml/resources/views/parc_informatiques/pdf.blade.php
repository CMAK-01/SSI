<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SIM-CML PARC INFORMATIQUE MINE</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #333; padding: 5px; vertical-align: top; }
        th { background: #efefef; }
    </style>
</head>
<body>
@php($logo = \App\Support\AppLogo::base64())
@if($logo)
    <div style="text-align: center; margin-bottom: 8px;">
        <img src="{{ $logo }}" alt="Logo" style="height: 60px;">
    </div>
@endif
<h2>SIM-CML PARC INFORMATIQUE MINE</h2>

<table>
    <thead>
    <tr>
        <th>Direction</th>
        <th>Service</th>
        <th>Type équipement</th>
        <th>ID interne équipement</th>
        <th>Nom matériel</th>
        <th>Utilisateur assigné</th>
        <th>État</th>
        <th>Crée le</th>
        <th>Mis à jour le</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td>{{ $item->direction->nom ?? '' }}</td>
            <td>{{ $item->service->nom ?? '' }}</td>
            <td>{{ str_replace('_', ' ', $item->type_equipement) }}</td>
            <td>{{ $item->equipement_id_interne }}</td>
            <td>{{ $item->nom_materiel }}</td>
            <td>{{ $item->utilisateur_assigne }}</td>
            <td>{{ $item->etat }}</td>
            <td>{{ optional($item->created_at)->format('d-m-Y H:i:s') }}</td>
            <td>{{ optional($item->updated_at)->format('d-m-Y H:i:s') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
