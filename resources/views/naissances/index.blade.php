@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Liste des Naissances</h1>

    <a href="{{ route('naissances.create') }}" class="btn btn-success mb-3">
        <i class="bi bi-plus-circle"></i>
    </a>

    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Nom du lapin</th>
                <th>Sexe</th>
                <th>Date de naissance</th>
                <th>Poids (kg)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($naissances as $naissance)
                <tr>
                    <td>{{ $naissance->id }}</td>
                    <td>{{ $naissance->nom_lapin }}</td>
                    <td>{{ $naissance->sexe }}</td>
                    <td>{{ $naissance->date_naissance }}</td>
                    <td>{{ $naissance->poids }}</td>
                    <td>
                        {{-- Bouton modifier (crayon) --}}
                        <a href="{{ route('naissances.edit', $naissance->id) }}" 
                           class="btn btn-primary btn-sm me-2" title="Modifier">
                            <i class="bi bi-pencil"></i>
                        </a>

                        {{-- Bouton supprimer (poubelle) --}}
                        <form action="{{ route('naissances.destroy', $naissance->id) }}" 
                              method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm me-2" 
                                onclick="return confirm('Voulez-vous vraiment supprimer cette naissance ?')" 
                                title="Supprimer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>

                        {{-- Bouton voir (œil) --}}
                        <a href="{{ route('naissances.show', $naissance->id) }}" 
                           class="btn btn-info btn-sm" title="Voir">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
