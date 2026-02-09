@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Modifier la naissance</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('naissances.update', $naissance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nom du lapin</label>
            <input type="text" name="nom_lapin" class="form-control" value="{{ $naissance->nom_lapin }}" required>
        </div>

        <div class="mb-3">
            <label>Sexe</label>
            <select name="sexe" class="form-select" required>
                <option value="M" {{ $naissance->sexe == 'M' ? 'selected' : '' }}>Mâle</option>
                <option value="F" {{ $naissance->sexe == 'F' ? 'selected' : '' }}>Femelle</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Date de naissance</label>
            <input type="date" name="date_naissance" class="form-control" value="{{ $naissance->date_naissance }}" required>
        </div>

        <div class="mb-3">
            <label>Poids (kg)</label>
            <input type="number" step="0.01" name="poids" class="form-control" value="{{ $naissance->poids }}" required>
        </div>

        <button class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('naissances.index') }}" class="btn btn-secondary">Retour</a>
    </form>
</div>
@endsection
