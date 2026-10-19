<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'SIM-CML RAPPORT DE DIAGNOSTIC' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 12px; }
        th, td { border: 1px solid #333; padding: 5px; vertical-align: top; text-align: left; }
        th { background: #efefef; }
        .entete { text-align: center; margin-bottom: 12px; }
        .entete img { height: 60px; margin-bottom: 6px; }
        .section-titre { background: #dbeafe; font-weight: bold; padding: 6px; border: 1px solid #333; margin-top: 10px; }
        .label { font-weight: bold; width: 35%; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
@foreach($items as $item)
    <div class="{{ !$loop->last ? 'page-break' : '' }}">
        <div class="entete">
            @php($logo = \App\Support\AppLogo::base64())
            @if($logo)
                <img src="{{ $logo }}" alt="Logo">
            @endif
            <h2 style="margin: 4px 0;">DIAGNOSTIC MATERIEL INFORMATIQUE CML</h2>
            <div>{{ $title ?? '' }}</div>
        </div>

        <div class="section-titre">1. INFORMATIONS GÉNÉRALES</div>
        <table>
            <tr>
                <td class="label">Date du diagnostic</td>
                <td>{{ optional($item->date_diagnostic)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">Service / Département</td>
                <td>{{ $item->direction->nom ?? '' }} — {{ $item->service->nom ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Lieu d'intervention</td>
                <td>{{ $item->lieu_intervention }}</td>
            </tr>
        </table>

        <div class="section-titre">2. INFORMATIONS SUR L'ÉQUIPEMENT</div>
        <table>
            <tr>
                <td class="label">Type de matériel</td>
                <td>{{ \App\Models\RapportDiagnostic::typeMaterielLabel($item->type_materiel) }}</td>
            </tr>
            <tr>
                <td class="label">Quantité</td>
                <td>{{ $item->quantite }}</td>
            </tr>
            <tr>
                <td class="label">Marque / Modèle</td>
                <td>{{ $item->marque_modele }}</td>
            </tr>
            <tr>
                <td class="label">Numéro de série / Inventaire</td>
                <td>{{ $item->numero_serie_inventaire }}</td>
            </tr>
            <tr>
                <td class="label">Utilisateur principal</td>
                <td>{{ $item->utilisateur_principal }}</td>
            </tr>
        </table>

        <div class="section-titre">3. CONSTAT INITIAL</div>
        <table>
            <tr>
                <td class="label">Symptômes signalés</td>
                <td>{{ $item->symptomes_signales }}</td>
            </tr>
            <tr>
                <td class="label">Contexte d'apparition</td>
                <td>{{ $item->contexte_apparition }}</td>
            </tr>
            <tr>
                <td class="label">Historique incidents similaires</td>
                <td>{{ $item->historique_incidents }}</td>
            </tr>
        </table>

        <div class="section-titre">4. VÉRIFICATIONS EFFECTUÉES</div>
        <table>
            <thead>
            <tr>
                <th>Éléments</th>
                <th>Désignations</th>
                <th>Statut</th>
                <th>Observations</th>
            </tr>
            </thead>
            <tbody>
            @foreach(\App\Models\RapportDiagnostic::VERIFICATIONS as $key => $labelVerif)
                <tr>
                    <td>{{ $labelVerif }}</td>
                    <td>{{ $item->{$key . '_designation'} }}</td>
                    <td>{{ \App\Models\RapportDiagnostic::statutLabel($item->{$key . '_statut'}) }}</td>
                    <td>{{ $item->{$key . '_observation'} }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <table>
            <tr>
                <td class="label">Résultats diagnostics</td>
                <td>{{ $item->resultats_diagnostics }}</td>
            </tr>
        </table>

        <div class="section-titre">5. ANALYSES ET CAUSES PROBABLES</div>
        <table>
            <tr>
                <td class="label">Cause identifiée</td>
                <td>{{ $item->cause_identifiee }}</td>
            </tr>
            <tr>
                <td class="label">Facteurs contributifs (poussière, surchauffe, obsolescence…)</td>
                <td>{{ $item->facteurs_contributifs }}</td>
            </tr>
        </table>

        <div class="section-titre">6. ACTIONS CORRECTIVES</div>
        <table>
            <tr>
                <td class="label">Mesures prises (nettoyage, remplacement, mise à jour)</td>
                <td>{{ $item->mesures_prises }}</td>
            </tr>
            <tr>
                <td class="label">Pièces remplacées / réparées</td>
                <td>{{ $item->pieces_remplacees }}</td>
            </tr>
            <tr>
                <td class="label">Logiciels réinstallés / mis à jour</td>
                <td>{{ $item->logiciels_maj }}</td>
            </tr>
        </table>

        <div class="section-titre">7. RECOMMANDATIONS</div>
        <table>
            <tr>
                <td class="label">Préventives (maintenance, sauvegarde, mise à jour)</td>
                <td>{{ $item->recommandations_preventives }}</td>
            </tr>
            <tr>
                <td class="label">Correctives supplémentaires (upgrade, remplacement futur)</td>
                <td>{{ $item->recommandations_correctives }}</td>
            </tr>
        </table>

        <div class="section-titre">8. VALIDATION</div>
        <table>
            <tr>
                <td class="label">État final du matériel</td>
                <td>{{ \App\Models\RapportDiagnostic::etatFinalLabel($item->etat_final) }}</td>
            </tr>
        </table>
        <table style="margin-top: 30px;">
            <tr>
                <td style="width: 50%; text-align: center; border: none;">
                    <strong>TECHNICIEN SI</strong><br><br><br>
                    {{ $item->technicien_si }}
                </td>
                <td style="width: 50%; text-align: center; border: none;">
                    <strong>CHEF DE SERVICE INFORMATIQUE</strong><br><br><br>
                    {{ $item->chef_service_informatique }}
                </td>
            </tr>
        </table>
    </div>
@endforeach
</body>
</html>
