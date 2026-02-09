@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Détails de la naissance</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Nom du lapin :</strong> {{ $naissance->nom_lapin }}</p>
            <p><strong>Sexe :</strong> {{ $naissance->sexe }}</p>
            <p><strong>Date de naissance :</strong> {{ $naissance->date_naissance }}</p>
            <p><strong>Poids :</strong> {{ $naissance->poids }} kg</p>
        </div>
    </div>

    {{-- Bouton icône retour --}}
    <a href="{{ route('naissances.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left-circle"></i>
    </a>
</div>
@endsection
