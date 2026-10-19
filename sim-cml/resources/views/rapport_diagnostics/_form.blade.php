@php
    /** @var \App\Models\RapportDiagnostic|null $d */
    $d = $rapportDiagnostic ?? null;
@endphp

<fieldset style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
    <legend><strong>1. Informations générales</strong></legend>

    <div class="field">
        <label for="date_diagnostic">Date du diagnostic</label>
        <input type="date" id="date_diagnostic" name="date_diagnostic"
               value="{{ old('date_diagnostic', optional($d?->date_diagnostic)->format('Y-m-d')) }}" required>
    </div>

    <div class="field">
        <label for="direction_id">Direction</label>
        <select name="direction_id" id="direction_id" required>
            <option value="">-- choisir --</option>
            @foreach($directions as $direction)
                <option value="{{ $direction->id }}" {{ old('direction_id', $d?->direction_id) == $direction->id ? 'selected' : '' }}>
                    {{ $direction->code }} - {{ $direction->nom }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="service_id">Service / Département</label>
        <select name="service_id" id="service_id" required>
            @isset($services)
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id', $d?->service_id) == $service->id ? 'selected' : '' }}>
                        {{ $service->nom }}
                    </option>
                @endforeach
            @endisset
        </select>
    </div>

    <div class="field">
        <label for="lieu_intervention">Lieu d'intervention</label>
        <input type="text" id="lieu_intervention" name="lieu_intervention"
               value="{{ old('lieu_intervention', $d?->lieu_intervention ?? 'SITE MINIER LAUZOUA') }}">
    </div>
</fieldset>

<fieldset style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
    <legend><strong>2. Informations sur l'équipement</strong></legend>

    <div class="field">
        <label for="type_materiel">Type de matériel</label>
        <select id="type_materiel" name="type_materiel" required>
            <option value="">-- choisir --</option>
            @foreach(\App\Models\RapportDiagnostic::TYPES_MATERIEL as $typeValue => $typeLabel)
                <option value="{{ $typeValue }}" {{ old('type_materiel', $d?->type_materiel) === $typeValue ? 'selected' : '' }}>
                    {{ $typeLabel }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="quantite">Quantité</label>
        <input type="number" id="quantite" name="quantite" min="1"
               value="{{ old('quantite', $d?->quantite) }}">
    </div>

    <div class="field">
        <label for="marque_modele">Marque / Modèle</label>
        <input type="text" id="marque_modele" name="marque_modele"
               value="{{ old('marque_modele', $d?->marque_modele) }}">
    </div>

    <div class="field">
        <label for="numero_serie_inventaire">Numéro de série / Inventaire</label>
        <input type="text" id="numero_serie_inventaire" name="numero_serie_inventaire"
               value="{{ old('numero_serie_inventaire', $d?->numero_serie_inventaire) }}">
    </div>

    <div class="field">
        <label for="utilisateur_principal">Utilisateur principal</label>
        <input type="text" id="utilisateur_principal" name="utilisateur_principal"
               value="{{ old('utilisateur_principal', $d?->utilisateur_principal) }}">
    </div>
</fieldset>

<fieldset style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
    <legend><strong>3. Constat initial</strong></legend>

    <div class="field">
        <label for="symptomes_signales">Symptômes signalés</label>
        <textarea id="symptomes_signales" name="symptomes_signales">{{ old('symptomes_signales', $d?->symptomes_signales) }}</textarea>
    </div>

    <div class="field">
        <label for="contexte_apparition">Contexte d'apparition</label>
        <textarea id="contexte_apparition" name="contexte_apparition">{{ old('contexte_apparition', $d?->contexte_apparition) }}</textarea>
    </div>

    <div class="field">
        <label for="historique_incidents">Historique incidents similaires</label>
        <textarea id="historique_incidents" name="historique_incidents">{{ old('historique_incidents', $d?->historique_incidents) }}</textarea>
    </div>
</fieldset>

<fieldset style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
    <legend><strong>4. Vérifications effectuées</strong></legend>

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
        @foreach(\App\Models\RapportDiagnostic::VERIFICATIONS as $key => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>
                    <input type="text" name="{{ $key }}_designation"
                           value="{{ old($key . '_designation', $d?->{$key . '_designation'}) }}"
                           placeholder="{{ $key === 'test_carte_reseau' ? 'Ex : CARTE RÉSEAU WIFI' : 'Désignation' }}">
                </td>
                <td>
                    <select name="{{ $key }}_statut">
                        <option value="">-- choisir --</option>
                        @foreach(\App\Models\RapportDiagnostic::STATUTS as $statutValue => $statutLabel)
                            <option value="{{ $statutValue }}"
                                {{ old($key . '_statut', $d?->{$key . '_statut'}) === $statutValue ? 'selected' : '' }}>
                                {{ $statutLabel }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" name="{{ $key }}_observation"
                           value="{{ old($key . '_observation', $d?->{$key . '_observation'}) }}">
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="field" style="margin-top: 12px;">
        <label for="resultats_diagnostics">Résultats diagnostics</label>
        <textarea id="resultats_diagnostics" name="resultats_diagnostics">{{ old('resultats_diagnostics', $d?->resultats_diagnostics) }}</textarea>
    </div>
</fieldset>

<fieldset style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
    <legend><strong>5. Analyses et causes probables</strong></legend>

    <div class="field">
        <label for="cause_identifiee">Cause identifiée</label>
        <textarea id="cause_identifiee" name="cause_identifiee">{{ old('cause_identifiee', $d?->cause_identifiee) }}</textarea>
    </div>

    <div class="field">
        <label for="facteurs_contributifs">Facteurs contributifs (poussière, surchauffe, obsolescence…)</label>
        <textarea id="facteurs_contributifs" name="facteurs_contributifs">{{ old('facteurs_contributifs', $d?->facteurs_contributifs) }}</textarea>
    </div>
</fieldset>

<fieldset style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
    <legend><strong>6. Actions correctives</strong></legend>

    <div class="field">
        <label for="mesures_prises">Mesures prises (nettoyage, remplacement, mise à jour)</label>
        <textarea id="mesures_prises" name="mesures_prises">{{ old('mesures_prises', $d?->mesures_prises) }}</textarea>
    </div>

    <div class="field">
        <label for="pieces_remplacees">Pièces remplacées / réparées</label>
        <textarea id="pieces_remplacees" name="pieces_remplacees">{{ old('pieces_remplacees', $d?->pieces_remplacees) }}</textarea>
    </div>

    <div class="field">
        <label for="logiciels_maj">Logiciels réinstallés / mis à jour</label>
        <textarea id="logiciels_maj" name="logiciels_maj">{{ old('logiciels_maj', $d?->logiciels_maj) }}</textarea>
    </div>
</fieldset>

<fieldset style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
    <legend><strong>7. Recommandations</strong></legend>

    <div class="field">
        <label for="recommandations_preventives">Préventives (maintenance, sauvegarde, mise à jour)</label>
        <textarea id="recommandations_preventives" name="recommandations_preventives">{{ old('recommandations_preventives', $d?->recommandations_preventives) }}</textarea>
    </div>

    <div class="field">
        <label for="recommandations_correctives">Correctives supplémentaires (upgrade, remplacement futur)</label>
        <textarea id="recommandations_correctives" name="recommandations_correctives">{{ old('recommandations_correctives', $d?->recommandations_correctives) }}</textarea>
    </div>
</fieldset>

<fieldset style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
    <legend><strong>8. Validation</strong></legend>

    <div class="field">
        <label for="etat_final">État final du matériel</label>
        <select id="etat_final" name="etat_final" required>
            <option value="">-- choisir --</option>
            @foreach(\App\Models\RapportDiagnostic::ETATS_FINAUX as $etatValue => $etatLabel)
                <option value="{{ $etatValue }}" {{ old('etat_final', $d?->etat_final) === $etatValue ? 'selected' : '' }}>
                    {{ $etatLabel }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="technicien_si">Technicien SI</label>
        <input type="text" id="technicien_si" name="technicien_si"
               value="{{ old('technicien_si', $d?->technicien_si) }}">
    </div>

    <div class="field">
        <label for="chef_service_informatique">Chef de service informatique</label>
        <input type="text" id="chef_service_informatique" name="chef_service_informatique"
               value="{{ old('chef_service_informatique', $d?->chef_service_informatique) }}">
    </div>
</fieldset>
