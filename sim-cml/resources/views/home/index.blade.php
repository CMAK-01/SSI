@extends('layouts.app')

@section('content')
    <h1>Bienvenue dans SIM-CML</h1>
    <p>Section informatique Mine-CML</p>

    <div class="grid">
        <div class="card">
            <h2>Rapport mensuel</h2>
            <p>Saisie des interventions mensuelles, suivi, historique et exports.</p>
            <a class="btn" href="{{ route('rapport-mensuels.index') }}">Ouvrir</a>
        </div>

        <div class="card">
            <h2>Parc Informatique Mine</h2>
            <p>Gestion des équipements, utilisateurs, états, notes multiples, historique et exports.</p>
            <a class="btn" href="{{ route('parc-informatiques.index') }}">Ouvrir</a>
        </div>
    </div>
@endsection
