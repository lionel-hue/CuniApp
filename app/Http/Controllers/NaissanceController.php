<?php

namespace App\Http\Controllers;

use App\Models\Naissance;
use Illuminate\Http\Request;

class NaissanceController extends Controller
{
    // Affiche la liste des naissances
    public function index()
    {
        $naissances = Naissance::all(); // récupère toutes les naissances
        return view('naissances.index', compact('naissances'));
    }

    // Affiche le formulaire de création
    public function create()
    {
        return view('naissances.create');
    }

    // Stocke une nouvelle naissance en base
    public function store(Request $request)
    {
        $request->validate([
            'nom_lapin' => 'required|string|max:255',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'required|date',
            'poids' => 'required|numeric',
        ]);

        Naissance::create($request->all());

        return redirect()->route('naissances.index')
                         ->with('success', 'Naissance ajoutée avec succès !');
    }

    // Affiche une naissance spécifique
    public function show(Naissance $naissance)
    {
        return view('naissances.show', compact('naissance'));
    }

    // Affiche le formulaire d’édition
    public function edit(Naissance $naissance)
    {
        return view('naissances.edit', compact('naissance'));
    }

    // Met à jour une naissance existante
    public function update(Request $request, Naissance $naissance)
    {
        $request->validate([
            'nom_lapin' => 'required|string|max:255',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'required|date',
            'poids' => 'required|numeric',
        ]);

        $naissance->update($request->all());

        return redirect()->route('naissances.index')
                         ->with('success', 'Naissance mise à jour avec succès !');
    }

    // Supprime une naissance
    public function destroy(Naissance $naissance)
    {
        $naissance->delete();

        return redirect()->route('naissances.index')
                         ->with('success', 'Naissance supprimée avec succès !');
    }
}
