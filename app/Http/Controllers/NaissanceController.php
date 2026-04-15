<?php

namespace App\Http\Controllers;

use App\Models\Naissance;
use App\Models\Lapereau;
use App\Models\MiseBas;
use App\Models\Femelle;
use Illuminate\Http\Request;
use App\Traits\Notifiable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\FirmAuditLog;

class NaissanceController extends Controller
{
    use Notifiable;

    public function index(Request $request)
    {
        $query = Naissance::with(['miseBas.femelle', 'lapereaux'])->latest();

        // 🔍 Recherche texte (femelle : nom ou code)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('miseBas.femelle', function ($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        // 📅 Filtre par période de mise bas
        if ($request->filled('date_from')) {
            $query->whereHas('miseBas', function ($q) use ($request) {
                $q->whereDate('date_mise_bas', '>=', $request->date_from);
            });
        }
        if ($request->filled('date_to')) {
            $query->whereHas('miseBas', function ($q) use ($request) {
                $q->whereDate('date_mise_bas', '<=', $request->date_to);
            });
        }

        // 🏥 Filtre par état de santé
        if ($request->filled('etat_sante')) {
            $query->where('etat_sante', $request->etat_sante);
        }

        // ✅ Filtre par statut de vérification du sexe
        if ($request->filled('sex_verified')) {
            if ($request->sex_verified === 'verified') {
                $query->where('sex_verified', true);
            } elseif ($request->sex_verified === 'pending') {
                $query->where('sex_verified', false);
            }
        }

        $naissances = $query->paginate(15)->withQueryString();

        // 📊 Stats
        $stats = [
            'total' => Naissance::count(),
            'this_month' => Naissance::whereHas(
                'miseBas',
                fn($q) => $q->whereMonth('date_mise_bas', now()->month)
                    ->whereYear('date_mise_bas', now()->year)
            )->count(),
            'nb_vivant_total' => Lapereau::whereHas('naissance', fn($q) => $q->active())
                ->where('etat', 'vivant')
                ->count(),
            'taux_survie_moyen' => Naissance::active()->get()->avg(fn($n) => $n->taux_survie ?? 0),
            'pending_verification' => Naissance::pendingVerification()->count(),
        ];

        return view('naissances.index', compact('naissances', 'stats'));
    }

    public function create(Request $request)
    {
        // ✅ TODO.MD STEP 4: CRITICAL - Check if user has a firm
        if (!auth()->user()->firm_id) {
            return back()
                ->withErrors(['error' => 'Votre compte n\'est associé à aucune entreprise. Contactez le support.'])
                ->withInput();
        }

        $miseBas = null;
        if ($request->has('mise_bas_id')) {
            $miseBas = MiseBas::with('femelle')->find($request->mise_bas_id);
        }

        $misesBas = MiseBas::with('femelle')
            ->whereDoesntHave('naissances')
            ->orderBy('date_mise_bas', 'desc')
            ->get();

        return view('naissances.create', compact('miseBas', 'misesBas'));
    }

    // public function store(Request $request)
    // {
    //     // ✅ TODO.MD STEP 4: CRITICAL - Check if user has a firm
    //     if (!auth()->user()->firm_id) {
    //         return back()
    //             ->withErrors(['error' => 'Votre compte n\'est associé à aucune entreprise. Contactez le support.'])
    //             ->withInput();
    //     }

    //     // ✅ VALIDATION 1: Basic fields
    //     $validated = $request->validate([
    //         'mise_bas_id' => 'required|exists:mises_bas,id',
    //         'poids_moyen_naissance' => 'nullable|numeric|min:0|max:200',
    //         'etat_sante' => 'required|in:Excellent,Bon,Moyen,Faible',
    //         'observations' => 'nullable|string|max:1000',
    //         'date_sevrage_prevue' => 'required|date|after_or_equal:date_mise_bas',
    //         'date_vaccination_prevue' => 'required|date|after_or_equal:date_mise_bas',
    //     ], [
    //         'mise_bas_id.required' => 'La mise bas est obligatoire',
    //         'mise_bas_id.exists' => 'La mise bas sélectionnée n\'existe pas',
    //         'etat_sante.in' => 'L\'état de santé doit être Excellent, Bon, Moyen ou Faible',
    //     ]);

    //     $miseBas = MiseBas::with('femelle')->findOrFail($validated['mise_bas_id']);

    //     // ✅ VALIDATION 2: Get max allowed lapereaux from mise_bas
    //     $maxVivant = $miseBas->nb_vivant ?? 0;
    //     $maxMortNe = $miseBas->nb_mort_ne ?? 0;
    //     $maxTotal = $maxVivant + $maxMortNe;

    //     // ✅ VALIDATION 3: Lapereaux array
    //     $rabbitsRules = [
    //         'rabbits' => 'required|array|min:1',
    //         'rabbits.*.nom' => 'nullable|string|max:50',
    //         'rabbits.*.sex' => 'nullable|in:male,female',
    //         'rabbits.*.etat' => 'required|in:vivant,mort,vendu',
    //         'rabbits.*.poids_naissance' => 'nullable|numeric|min:0|max:200',
    //         'rabbits.*.etat_sante' => 'nullable|in:Excellent,Bon,Moyen,Faible',
    //         'rabbits.*.observations' => 'nullable|string|max:500',
    //         'rabbits.*.code' => 'nullable|string|max:20',


    //         'rabbits.*.vaccin_type' => 'nullable|in:myxomatose,vhd,pasteurellose,coccidiose,autre',
    //         'rabbits.*.vaccin_nom_autre' => 'nullable|string|max:100',
    //         // 'rabbits.*.vaccin_date' => 'nullable|date',
    //         'rabbits.*.vaccin_dose_numero' => 'nullable|integer|min:1|max:10',
    //         'rabbits.*.vaccin_rappel_prevu' => 'nullable|date',
    //         'rabbits.*.vaccin_notes' => 'nullable|string|max:500',
    //         'rabbits.*.vaccined' => 'nullable|boolean',
    //         'rabbits.*.vaccin_date' => 'nullable|date|required_if:rabbits.*.vaccined,true',
    //     ];

    //     $validated = array_merge($validated, $request->validate($rabbitsRules));

    //     // ✅ VALIDATION 4: Count validation against mise_bas
    //     $vivantCount = collect($validated['rabbits'])
    //         ->where('etat', 'vivant')
    //         ->count();
    //     $mortCount = collect($validated['rabbits'])
    //         ->where('etat', 'mort')
    //         ->count();
    //     $totalRabbits = count($validated['rabbits']);

    //     $errors = [];

    //     if ($maxTotal > 0) {
    //         if ($totalRabbits > $maxTotal) {
    //             $errors[] = "Vous essayez de créer {$totalRabbits} lapereaux mais la mise bas indique un maximum de {$maxTotal} ({$maxVivant} vivants + {$maxMortNe} morts-nés).";
    //         }
    //         if ($vivantCount > $maxVivant) {
    //             $errors[] = "Trop de lapereaux vivants déclarés ({$vivantCount}) par rapport à la mise bas ({$maxVivant}).";
    //         }
    //         if ($mortCount > $maxMortNe) {
    //             $errors[] = "Trop de lapereaux morts-nés déclarés ({$mortCount}) par rapport à la mise bas ({$maxMortNe}).";
    //         }
    //     }

    //     // ✅ VALIDATION 5: Check for duplicate codes if manually entered
    //     foreach ($validated['rabbits'] as $index => $rabbit) {
    //         if (!empty($rabbit['code']) && $rabbit['code'] !== 'Auto-généré') {
    //             if (!Lapereau::isCodeUnique($rabbit['code'])) {
    //                 $errors[] = "Le code '{$rabbit['code']}' pour le lapereau #" . ($index + 1) . " existe déjà.";
    //             }
    //         }
    //     }

    //     if (!empty($errors)) {
    //         return back()->withErrors($errors)->withInput();
    //     }

    //     // ✅ Calculate sevrage date if not provided (6 weeks from birth)
    //     if (empty($validated['date_sevrage_prevue'])) {
    //         $validated['date_sevrage_prevue'] = Carbon::parse($miseBas->date_mise_bas)
    //             ->addWeeks(6)
    //             ->format('Y-m-d');
    //     }



    //     DB::beginTransaction();
    //     try {
    //         // ✅ Create Naissance record (BelongsToUser trait will auto-assign user_id and firm_id)
    //         $naissance = Naissance::create(array_merge($validated, [
    //             'user_id' => auth()->id(),
    //             // firm_id will be auto-assigned by BelongsToUser trait
    //         ]));

    //         // ✅ Create Individual Lapereaux
    //         foreach ($validated['rabbits'] as $rabbitData) {
    //             $rabbitData['naissance_id'] = $naissance->id;
    //             // Auto-generate code if not provided
    //             if (empty($rabbitData['code'])) {
    //                 $rabbitData['code'] = Lapereau::generateUniqueCode();
    //             }
    //             Lapereau::create($rabbitData);
    //         }

    //         // ✅ Update femelle status to Allaitante
    //         $femelle = $miseBas->femelle;
    //         if ($femelle && $femelle->etat === 'Gestante') {
    //             $femelle->update(['etat' => 'Allaitante']);
    //         }

    //         $this->notifyUser([
    //             'type' => 'success',
    //             'title' => '🐰 Naissance & Lapereaux Enregistrés',
    //             'message' => "Portée de {$femelle->nom}: {$totalRabbits} lapereaux enregistrés",
    //             'action_url' => route('naissances.show', $naissance),
    //         ]);

    //         DB::commit();

    //         // ✅ TODO.MD STEP 4: Pass null for firm_id to let Model handle auto-detection
    //         FirmAuditLog::log(
    //             null,  // ✅ Let the model auto-detect from authenticated user
    //             auth()->id(),
    //             'naissance_created',
    //             'nb_vivant',
    //             null,
    //             $naissance->nb_vivant
    //         );

    //         return redirect()->route('naissances.show', $naissance)
    //             ->with('success', 'Naissance et lapereaux enregistrés ! Sexe à vérifier après 10 jours.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()
    //             ->withErrors(['error' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()])
    //             ->withInput();
    //     }
    // }







    // public function store(Request $request)
    // {
    //     if (!auth()->user()->firm_id) {
    //         return back()->withErrors(['error' => 'Votre compte n\'est associé à aucune entreprise.'])->withInput();
    //     }

    //     $validated = $request->validate([
    //         'mise_bas_id' => 'required|exists:mises_bas,id',
    //         'poids_moyen_naissance' => 'nullable|numeric|min:0|max:200',
    //         'etat_sante' => 'required|in:Excellent,Bon,Moyen,Faible',
    //         'observations' => 'nullable|string|max:1000',
    //         'date_sevrage_prevue' => 'required|date',
    //     ]);

    //     $miseBas = MiseBas::with('femelle')->findOrFail($validated['mise_bas_id']);
    //     $maxTotal = ($miseBas->nb_vivant ?? 0) + ($miseBas->nb_mort_ne ?? 0);

    //     // Règles de base pour lapereaux
    //     $rabbitsRules = [
    //         'rabbits' => 'required|array|min:1',
    //         'rabbits.*.nom' => 'nullable|string|max:50',
    //         'rabbits.*.sex' => 'nullable|in:male,female',
    //         'rabbits.*.etat' => 'required|in:vivant,mort,vendu',
    //         'rabbits.*.poids_naissance' => 'nullable|numeric|min:0|max:200',
    //         'rabbits.*.etat_sante' => 'nullable|in:Excellent,Bon,Moyen,Faible',
    //         'rabbits.*.code' => 'nullable|string|max:20',

    //         // ✅ Règles vaccination SIMPLES (pas de required_if)
    //         'rabbits.*.vaccined' => 'nullable|in:1,0,on,off,true,false',
    //         'rabbits.*.vaccin_type' => 'nullable|in:myxomatose,vhd,pasteurellose,coccidiose,autre',
    //         'rabbits.*.vaccin_nom_autre' => 'nullable|string|max:100',
    //         'rabbits.*.vaccin_date' => 'nullable|date',
    //         'rabbits.*.vaccin_dose_numero' => 'nullable|integer|min:1|max:10',
    //     ];

    //     $validated = array_merge($validated, $request->validate($rabbitsRules));

    //     // ✅ VALIDATION MANUELLE (contourne le bug required_if)
    //     $errors = [];
    //     foreach ($validated['rabbits'] ?? [] as $i => $r) {
    //         if (!empty($r['vaccined']) && empty($r['vaccin_date'])) {
    //             $errors["rabbits.{$i}.vaccin_date"] = 'Date requise si vacciné.';
    //         }
    //         if (($r['vaccin_type'] ?? '') === 'autre' && empty($r['vaccin_nom_autre'])) {
    //             $errors["rabbits.{$i}.vaccin_nom_autre"] = 'Nom requis pour "Autre".';
    //         }
    //         if (!empty($r['code']) && !Lapereau::isCodeUnique($r['code'])) {
    //             $errors["rabbits.{$i}.code"] = "Le code '{$r['code']}' existe déjà.";
    //         }
    //     }
    //     if ($maxTotal > 0 && count($validated['rabbits']) > $maxTotal) {
    //         $errors[] = "Dépassement du maximum autorisé ({$maxTotal}).";
    //     }
    //     if (!empty($errors))
    //         return back()->withErrors($errors)->withInput();

    //     DB::beginTransaction();
    //     try {
    //         $naissance = Naissance::create(array_merge($validated, ['user_id' => auth()->id()]));

    //         foreach ($validated['rabbits'] as $rData) {
    //             $rData['naissance_id'] = $naissance->id;
    //             $rData['code'] = empty($rData['code']) ? Lapereau::generateUniqueCode() : $rData['code'];

    //             $lapin = Lapereau::create($rData);

    //             // ✅ Enregistrer vaccination si cochée
    //             if (!empty($rData['vaccined'])) {
    //                 $lapin->update([
    //                     'vaccin_type' => $rData['vaccin_type'] ?? 'myxomatose',
    //                     'vaccin_nom_autre' => $rData['vaccin_nom_autre'] ?? null,
    //                     'vaccin_date' => $rData['vaccin_date'],
    //                     'vaccin_dose_numero' => $rData['vaccin_dose_numero'] ?? 1,
    //                 ]);
    //             }
    //         }

    //         if ($miseBas->femelle?->etat === 'Gestante') {
    //             $miseBas->femelle->update(['etat' => 'Allaitante']);
    //         }

    //         $this->notifyUser([
    //             'type' => 'success',
    //             'title' => '🐰 Naissance Enregistrée',
    //             'message' => "Portée de {$miseBas->femelle->nom} : " . count($validated['rabbits']) . " lapereaux",
    //             'action_url' => route('naissances.show', $naissance),
    //         ]);

    //         DB::commit();
    //         return redirect()->route('naissances.show', $naissance)->with('success', 'Naissance et lapereaux enregistrés !');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()])->withInput();
    //     }
    // }


    // public function store(Request $request)
    // {
    //     if (!auth()->user()->firm_id) {
    //         return back()->withErrors(['error' => 'Votre compte n\'est associé à aucune entreprise.'])->withInput();
    //     }

    //     $validated = $request->validate([
    //         'mise_bas_id' => 'required|exists:mises_bas,id',
    //         'poids_moyen_naissance' => 'nullable|numeric|min:0|max:200',
    //         'etat_sante' => 'required|in:Excellent,Bon,Moyen,Faible',
    //         'observations' => 'nullable|string|max:1000',
    //         'date_sevrage_prevue' => 'required|date',
    //     ]);

    //     $miseBas = MiseBas::with('femelle')->findOrFail($validated['mise_bas_id']);
    //     $maxTotal = ($miseBas->nb_vivant ?? 0) + ($miseBas->nb_mort_ne ?? 0);

    //     // Règles de base pour lapereaux
    //     $rabbitsRules = [
    //         'rabbits' => 'required|array|min:1',
    //         'rabbits.*.nom' => 'nullable|string|max:50',
    //         'rabbits.*.sex' => 'nullable|in:male,female',
    //         'rabbits.*.etat' => 'required|in:vivant,mort,vendu',
    //         'rabbits.*.poids_naissance' => 'nullable|numeric|min:0|max:200',
    //         'rabbits.*.etat_sante' => 'nullable|in:Excellent,Bon,Moyen,Faible',
    //         'rabbits.*.code' => 'nullable|string|max:20',

    //         // ✅ Règles vaccination SIMPLES (pas de required_if)
    //         'rabbits.*.vaccined' => 'nullable|in:1,0,on,off,true,false',
    //         'rabbits.*.vaccin_type' => 'nullable|in:myxomatose,vhd,pasteurellose,coccidiose,autre',
    //         'rabbits.*.vaccin_nom_autre' => 'nullable|string|max:100',
    //         'rabbits.*.vaccin_date' => 'nullable|date',
    //         'rabbits.*.vaccin_dose_numero' => 'nullable|integer|min:1|max:10',
    //     ];

    //     $validated = array_merge($validated, $request->validate($rabbitsRules));

    //     // ✅ NORMALISATION DES VALEURS DE VACCINATION (AJOUTÉ ICI)
    //     foreach ($validated['rabbits'] as &$rabbit) {
    //         // Convertir vaccined en booléen réel
    //         if (isset($rabbit['vaccined'])) {
    //             $rabbit['vaccined'] = in_array($rabbit['vaccined'], [1, '1', 'on', 'true', true], true);
    //         }
    //         // Si vaccin_type = "autre" mais pas de nom, utiliser une valeur par défaut
    //         if (($rabbit['vaccin_type'] ?? '') === 'autre' && empty($rabbit['vaccin_nom_autre'])) {
    //             $rabbit['vaccin_nom_autre'] = 'Vaccin personnalisé';
    //         }
    //     }
    //     unset($rabbit); // Important : libérer la référence

    //     // ✅ VALIDATION MANUELLE (contourne le bug required_if)
    //     $errors = [];
    //     foreach ($validated['rabbits'] ?? [] as $i => $r) {
    //         if (!empty($r['vaccined']) && empty($r['vaccin_date'])) {
    //             $errors["rabbits.{$i}.vaccin_date"] = 'Date requise si vacciné.';
    //         }
    //         if (($r['vaccin_type'] ?? '') === 'autre' && empty($r['vaccin_nom_autre'])) {
    //             $errors["rabbits.{$i}.vaccin_nom_autre"] = 'Nom requis pour "Autre".';
    //         }
    //         if (!empty($r['code']) && !Lapereau::isCodeUnique($r['code'])) {
    //             $errors["rabbits.{$i}.code"] = "Le code '{$r['code']}' existe déjà.";
    //         }
    //     }
    //     if ($maxTotal > 0 && count($validated['rabbits']) > $maxTotal) {
    //         $errors[] = "Dépassement du maximum autorisé ({$maxTotal}).";
    //     }
    //     if (!empty($errors))
    //         return back()->withErrors($errors)->withInput();

    //     DB::beginTransaction();
    //     try {
    //         $naissance = Naissance::create(array_merge($validated, ['user_id' => auth()->id()]));

    //         foreach ($validated['rabbits'] as $rData) {
    //             $rData['naissance_id'] = $naissance->id;
    //             $rData['code'] = empty($rData['code']) ? Lapereau::generateUniqueCode() : $rData['code'];

    //             $lapin = Lapereau::create($rData);

    //             // ✅ Enregistrer vaccination si cochée
    //             if (!empty($rData['vaccined'])) {
    //                 $lapin->update([
    //                     'vaccin_type' => $rData['vaccin_type'] ?? 'myxomatose',
    //                     'vaccin_nom_autre' => $rData['vaccin_nom_autre'] ?? null,
    //                     'vaccin_date' => $rData['vaccin_date'],
    //                     'vaccin_dose_numero' => $rData['vaccin_dose_numero'] ?? 1,
    //                 ]);
    //             }
    //         }

    //         if ($miseBas->femelle?->etat === 'Gestante') {
    //             $miseBas->femelle->update(['etat' => 'Allaitante']);
    //         }

    //         $this->notifyUser([
    //             'type' => 'success',
    //             'title' => '🐰 Naissance Enregistrée',
    //             'message' => "Portée de {$miseBas->femelle->nom} : " . count($validated['rabbits']) . " lapereaux",
    //             'action_url' => route('naissances.show', $naissance),
    //         ]);

    //         DB::commit();
    //         return redirect()->route('naissances.show', $naissance)->with('success', 'Naissance et lapereaux enregistrés !');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()])->withInput();
    //     }
    // }


    public function store(Request $request)
    {
        // ✅ Vérification firme
        if (!auth()->user()->firm_id) {
            return back()->withErrors(['error' => 'Votre compte n\'est associé à aucune entreprise.'])->withInput();
        }

        // ✅ Validation champs naissance
        $validated = $request->validate([
            'mise_bas_id' => 'required|exists:mises_bas,id',
            'poids_moyen_naissance' => 'nullable|numeric|min:0|max:200',
            'etat_sante' => 'required|in:Excellent,Bon,Moyen,Faible',
            'observations' => 'nullable|string|max:1000',
            'date_sevrage_prevue' => 'required|date',
        ]);

        $miseBas = MiseBas::with('femelle')->findOrFail($validated['mise_bas_id']);
        $maxTotal = ($miseBas->nb_vivant ?? 0) + ($miseBas->nb_mort_ne ?? 0);

        // ✅ Règles pour lapereaux + vaccins multiples
        $rabbitsRules = [
            'rabbits' => 'required|array|min:1',
            'rabbits.*.nom' => 'nullable|string|max:50',
            'rabbits.*.sex' => 'nullable|in:male,female',
            'rabbits.*.etat' => 'required|in:vivant,mort,vendu',
            'rabbits.*.poids_naissance' => 'nullable|numeric|min:0|max:200',
            'rabbits.*.etat_sante' => 'nullable|in:Excellent,Bon,Moyen,Faible',
            'rabbits.*.code' => 'nullable|string|max:20',

            // ✅ Vaccins multiples (nouvelle structure)
            'rabbits.*.vaccins' => 'nullable|array',
            'rabbits.*.vaccins.*.type' => 'nullable|in:myxomatose,vhd,pasteurellose,coccidiose,autre',
            'rabbits.*.vaccins.*.nom_autre' => 'nullable|string|max:100|required_if:rabbits.*.vaccins.*.type,autre',
            'rabbits.*.vaccins.*.date' => 'nullable|date',
            'rabbits.*.vaccins.*.dose' => 'nullable|integer|min:1|max:10',
            'rabbits.*.vaccins.*.rappel' => 'nullable|date|after_or_equal:rabbits.*.vaccins.*.date',
            'rabbits.*.vaccins.*.notes' => 'nullable|string|max:500',

            // ✅ Rétrocompatibilité : champs vaccination simple
            'rabbits.*.vaccined' => 'nullable|in:1,0,on,off,true,false',
            'rabbits.*.vaccin_type' => 'nullable|in:myxomatose,vhd,pasteurellose,coccidiose,autre',
            'rabbits.*.vaccin_nom_autre' => 'nullable|string|max:100',
            'rabbits.*.vaccin_date' => 'nullable|date',
            'rabbits.*.vaccin_dose_numero' => 'nullable|integer|min:1|max:10',
        ];

        $validated = array_merge($validated, $request->validate($rabbitsRules));

        // ✅ Normalisation des valeurs de vaccination (simple + multiple)
        foreach ($validated['rabbits'] as &$rabbit) {
            // Normaliser vaccined (checkbox)
            if (isset($rabbit['vaccined'])) {
                $rabbit['vaccined'] = in_array($rabbit['vaccined'], [1, '1', 'on', 'true', true], true);
            }
            // Autre → nom requis
            if (($rabbit['vaccin_type'] ?? '') === 'autre' && empty($rabbit['vaccin_nom_autre'])) {
                $rabbit['vaccin_nom_autre'] = 'Vaccin personnalisé';
            }
            // Normaliser les vaccins multiples
            if (!empty($rabbit['vaccins']) && is_array($rabbit['vaccins'])) {
                foreach ($rabbit['vaccins'] as &$v) {
                    if (isset($v['type']) && $v['type'] === 'autre' && empty($v['nom_autre'])) {
                        $v['nom_autre'] = 'Vaccin personnalisé';
                    }
                }
                unset($v);
            }
        }
        unset($rabbit);

        // ✅ Validation manuelle
        $errors = [];
        foreach ($validated['rabbits'] ?? [] as $i => $r) {
            // Vaccin simple : date requise si coché
            if (!empty($r['vaccined']) && empty($r['vaccin_date'])) {
                $errors["rabbits.{$i}.vaccin_date"] = 'Date requise si vacciné.';
            }
            // Vaccin simple : nom requis si "autre"
            if (($r['vaccin_type'] ?? '') === 'autre' && empty($r['vaccin_nom_autre'])) {
                $errors["rabbits.{$i}.vaccin_nom_autre"] = 'Nom requis pour "Autre".';
            }
            // Code unique
            if (!empty($r['code']) && !Lapereau::isCodeUnique($r['code'])) {
                $errors["rabbits.{$i}.code"] = "Le code '{$r['code']}' existe déjà.";
            }
            // Vaccins multiples : validation
            if (!empty($r['vaccins']) && is_array($r['vaccins'])) {
                foreach ($r['vaccins'] as $j => $v) {
                    if (!empty($v['type']) && empty($v['date'])) {
                        $errors["rabbits.{$i}.vaccins.{$j}.date"] = 'Date requise pour ce vaccin.';
                    }
                    if (($v['type'] ?? '') === 'autre' && empty($v['nom_autre'])) {
                        $errors["rabbits.{$i}.vaccins.{$j}.nom_autre"] = 'Nom requis pour "Autre".';
                    }
                }
            }
        }
        // Max lapereaux
        if ($maxTotal > 0 && count($validated['rabbits']) > $maxTotal) {
            $errors[] = "Dépassement du maximum autorisé ({$maxTotal}).";
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        DB::beginTransaction();
        try {
            // ✅ Créer la naissance
            $naissance = Naissance::create(array_merge($validated, ['user_id' => auth()->id()]));

            // ✅ Créer les lapereaux
            foreach ($validated['rabbits'] as $rData) {
                $rData['naissance_id'] = $naissance->id;
                $rData['code'] = empty($rData['code']) ? Lapereau::generateUniqueCode() : $rData['code'];

                $lapin = Lapereau::create($rData);

                // ✅ Enregistrer vaccination SIMPLE (rétrocompatibilité)
                if (!empty($rData['vaccined']) && !empty($rData['vaccin_date'])) {
                    $lapin->update([
                        'vaccin_type' => $rData['vaccin_type'] ?? 'myxomatose',
                        'vaccin_nom_autre' => $rData['vaccin_nom_autre'] ?? null,
                        'vaccin_date' => $rData['vaccin_date'],
                        'vaccin_dose_numero' => $rData['vaccin_dose_numero'] ?? 1,
                        'vaccin_rappel_prevu' => $rData['vaccin_rappel_prevu'] ?? null,
                        'vaccin_notes' => $rData['vaccin_notes'] ?? null,
                    ]);
                }

                // ✅ Enregistrer vaccins MULTIPLES (nouvelle table)
                if (!empty($rData['vaccins']) && is_array($rData['vaccins'])) {
                    foreach ($rData['vaccins'] as $vData) {
                        if (!empty($vData['type']) && !empty($vData['date'])) {
                            // Skip si "autre" sans nom
                            if ($vData['type'] === 'autre' && empty($vData['nom_autre'])) {
                                continue;
                            }

                            $lapin->vaccinations()->create([
                                'type' => $vData['type'],
                                'nom_personnalise' => $vData['nom_autre'] ?? null,
                                'date_administration' => $vData['date'],
                                'dose_numero' => $vData['dose'] ?? 1,
                                'rappel_prevu' => $vData['rappel'] ?? null,
                                'notes' => $vData['notes'] ?? null,
                                'administered_by' => auth()->id(),
                            ]);
                        }
                    }
                }
            }

            // ✅ Mettre à jour la femelle
            if ($miseBas->femelle?->etat === 'Gestante') {
                $miseBas->femelle->update(['etat' => 'Allaitante']);
            }

            // ✅ Notification
            $this->notifyUser([
                'type' => 'success',
                'title' => '🐰 Naissance Enregistrée',
                'message' => "Portée de {$miseBas->femelle->nom} : " . count($validated['rabbits']) . " lapereaux",
                'action_url' => route('naissances.show', $naissance),
            ]);

            DB::commit();
            return redirect()->route('naissances.show', $naissance)
                ->with('success', 'Naissance et lapereaux enregistrés !');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()])->withInput();
        }
    }



    public function update(Request $request, Naissance $naissance)
    {
        // ✅ Sécurité
        if ($naissance->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé.');
        }
        if (!auth()->user()->firm_id) {
            return back()->withErrors(['error' => 'Aucune entreprise liée.'])->withInput();
        }

        // ✅ Règle dynamique pour le sexe
        $canVerifySex = $naissance->jours_depuis_naissance >= 10;
        $sexRule = $canVerifySex ? 'required|in:male,female' : 'nullable|in:male,female';

        // ✅ Validation
        $validated = $request->validate([
            'poids_moyen_naissance' => 'nullable|numeric|min:0|max:200',
            'etat_sante' => 'required|in:Excellent,Bon,Moyen,Faible',
            'observations' => 'nullable|string|max:1000',
            'date_sevrage_prevue' => 'required|date',
            'sex_verified' => 'nullable|boolean',

            'rabbits' => 'required|array|min:1',
            'rabbits.*.id' => 'nullable|exists:lapereaux,id',
            'rabbits.*.nom' => 'nullable|string|max:50',
            'rabbits.*.sex' => $sexRule,
            'rabbits.*.etat' => 'required|in:vivant,mort,vendu',
            'rabbits.*.poids_naissance' => 'nullable|numeric|min:0|max:200',
            'rabbits.*.etat_sante' => 'nullable|in:Excellent,Bon,Moyen,Faible',
            'rabbits.*.code' => 'nullable|string|max:20',

            // ✅ Vaccins multiples
            'rabbits.*.vaccins' => 'nullable|array',
            'rabbits.*.vaccins.*.type' => 'nullable|in:myxomatose,vhd,pasteurellose,coccidiose,autre',
            'rabbits.*.vaccins.*.nom_autre' => 'nullable|string|max:100|required_if:rabbits.*.vaccins.*.type,autre',
            'rabbits.*.vaccins.*.date' => 'nullable|date',
            'rabbits.*.vaccins.*.dose' => 'nullable|integer|min:1|max:10',
            'rabbits.*.vaccins.*.rappel' => 'nullable|date|after_or_equal:rabbits.*.vaccins.*.date',
            'rabbits.*.vaccins.*.notes' => 'nullable|string|max:500',

            // ✅ Rétrocompatibilité
            'rabbits.*.vaccined' => 'nullable|in:1,0,on,off,true,false',
            'rabbits.*.vaccin_type' => 'nullable|in:myxomatose,vhd,pasteurellose,coccidiose,autre',
            'rabbits.*.vaccin_nom_autre' => 'nullable|string|max:100',
            'rabbits.*.vaccin_date' => 'nullable|date',
            'rabbits.*.vaccin_dose_numero' => 'nullable|integer|min:1|max:10',
        ]);

        // ✅ Normalisation
        foreach ($validated['rabbits'] as &$rabbit) {
            if (isset($rabbit['vaccined'])) {
                $rabbit['vaccined'] = in_array($rabbit['vaccined'], [1, '1', 'on', 'true', true], true);
            }
            if (($rabbit['vaccin_type'] ?? '') === 'autre' && empty($rabbit['vaccin_nom_autre'])) {
                $rabbit['vaccin_nom_autre'] = 'Vaccin personnalisé';
            }
            if (!empty($rabbit['vaccins']) && is_array($rabbit['vaccins'])) {
                foreach ($rabbit['vaccins'] as &$v) {
                    if (isset($v['type']) && $v['type'] === 'autre' && empty($v['nom_autre'])) {
                        $v['nom_autre'] = 'Vaccin personnalisé';
                    }
                }
                unset($v);
            }
        }
        unset($rabbit);

        // ✅ Validation manuelle
        $errors = [];
        foreach ($validated['rabbits'] ?? [] as $i => $r) {
            if (!empty($r['vaccined']) && empty($r['vaccin_date'])) {
                $errors["rabbits.{$i}.vaccin_date"] = 'Date requise si vacciné.';
            }
            if (($r['vaccin_type'] ?? '') === 'autre' && empty($r['vaccin_nom_autre'])) {
                $errors["rabbits.{$i}.vaccin_nom_autre"] = 'Nom requis pour "Autre".';
            }
            if (!empty($r['code'])) {
                $excludeId = $r['id'] ?? null;
                if (!Lapereau::isCodeUnique($r['code'], $excludeId)) {
                    $errors["rabbits.{$i}.code"] = "Le code '{$r['code']}' existe déjà.";
                }
            }
            // Vaccins multiples
            if (!empty($r['vaccins']) && is_array($r['vaccins'])) {
                foreach ($r['vaccins'] as $j => $v) {
                    if (!empty($v['type']) && empty($v['date'])) {
                        $errors["rabbits.{$i}.vaccins.{$j}.date"] = 'Date requise.';
                    }
                    if (($v['type'] ?? '') === 'autre' && empty($v['nom_autre'])) {
                        $errors["rabbits.{$i}.vaccins.{$j}.nom_autre"] = 'Nom requis.';
                    }
                }
            }
        }
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        // ✅ Vérification sexe
        if (!$naissance->can_verify_sex && $request->has('sex_verified')) {
            return back()->withErrors(['sex_verified' => 'Vérification possible après 10 jours.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $wasUnverified = !$naissance->sex_verified;
            $naissance->update($validated);

            $existingIds = [];

            foreach ($request->input('rabbits', []) as $rData) {
                if (!empty($rData['id'])) {
                    // ✅ Mise à jour lapereau existant
                    $lap = Lapereau::find($rData['id']);
                    if ($lap && $lap->naissance_id === $naissance->id) {
                        $rData['code'] = empty($rData['code']) ? $lap->code : $rData['code'];
                        $lap->update($rData);

                        // Mise à jour vaccination SIMPLE
                        if (isset($rData['vaccined'])) {
                            $lap->update([
                                'vaccin_type' => $rData['vaccin_type'] ?? null,
                                'vaccin_nom_autre' => $rData['vaccin_nom_autre'] ?? null,
                                'vaccin_date' => $rData['vaccin_date'] ?? null,
                                'vaccin_dose_numero' => $rData['vaccin_dose_numero'] ?? 1,
                                'vaccin_rappel_prevu' => $rData['vaccin_rappel_prevu'] ?? null,
                                'vaccin_notes' => $rData['vaccin_notes'] ?? null,
                            ]);
                        }

                        // ✅ Gestion vaccins MULTIPLES
                        if (isset($rData['vaccins']) && is_array($rData['vaccins'])) {
                            $vaccinIds = [];
                            foreach ($rData['vaccins'] as $vData) {
                                if (!empty($vData['id'])) {
                                    $vaccinIds[] = $vData['id'];
                                }
                            }
                            $lap->vaccinations()->whereNotIn('id', $vaccinIds)->delete();

                            foreach ($rData['vaccins'] as $vData) {
                                if (!empty($vData['type']) && !empty($vData['date'])) {
                                    if ($vData['type'] === 'autre' && empty($vData['nom_autre']))
                                        continue;

                                    $vaccinData = [
                                        'type' => $vData['type'],
                                        'nom_personnalise' => $vData['nom_autre'] ?? null,
                                        'date_administration' => $vData['date'],
                                        'dose_numero' => $vData['dose'] ?? 1,
                                        'rappel_prevu' => $vData['rappel'] ?? null,
                                        'notes' => $vData['notes'] ?? null,
                                        'administered_by' => auth()->id(),
                                    ];

                                    if (!empty($vData['id'])) {
                                        $lap->vaccinations()->where('id', $vData['id'])->update($vaccinData);
                                    } else {
                                        $lap->vaccinations()->create($vaccinData);
                                    }
                                }
                            }
                        }
                        $existingIds[] = $lap->id;
                    }
                } else {
                    // ✅ Création nouveau lapereau
                    $rData['naissance_id'] = $naissance->id;
                    $rData['code'] = empty($rData['code']) ? Lapereau::generateUniqueCode() : $rData['code'];
                    $new = Lapereau::create($rData);

                    if (!empty($rData['vaccined']) && !empty($rData['vaccin_date'])) {
                        $new->update([
                            'vaccin_type' => $rData['vaccin_type'] ?? 'myxomatose',
                            'vaccin_nom_autre' => $rData['vaccin_nom_autre'] ?? null,
                            'vaccin_date' => $rData['vaccin_date'],
                            'vaccin_dose_numero' => $rData['vaccin_dose_numero'] ?? 1,
                        ]);
                    }

                    if (!empty($rData['vaccins']) && is_array($rData['vaccins'])) {
                        foreach ($rData['vaccins'] as $vData) {
                            if (!empty($vData['type']) && !empty($vData['date'])) {
                                if ($vData['type'] === 'autre' && empty($vData['nom_autre']))
                                    continue;
                                $new->vaccinations()->create([
                                    'type' => $vData['type'],
                                    'nom_personnalise' => $vData['nom_autre'] ?? null,
                                    'date_administration' => $vData['date'],
                                    'dose_numero' => $vData['dose'] ?? 1,
                                    'rappel_prevu' => $vData['rappel'] ?? null,
                                    'notes' => $vData['notes'] ?? null,
                                    'administered_by' => auth()->id(),
                                ]);
                            }
                        }
                    }
                    $existingIds[] = $new->id;
                }
            }

            // ✅ Supprimer lapereaux retirés
            Lapereau::where('naissance_id', $naissance->id)
                ->whereNotIn('id', $existingIds)
                ->delete();

            // ✅ Vérification sexe
            if ($wasUnverified && $naissance->sex_verified) {
                $naissance->markSexAsVerified();
            }

            DB::commit();
            return redirect()->route('naissances.show', $naissance)
                ->with('success', 'Naissance mise à jour !');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()])->withInput();
        }
    }

    // public function update(Request $request, Naissance $naissance)
    // {
    //     if ($naissance->user_id !== auth()->id() && !auth()->user()->isAdmin())
    //         abort(403);
    //     if (!auth()->user()->firm_id)
    //         return back()->withErrors(['error' => 'Aucune entreprise liée.'])->withInput();


    //     // ✅ Déterminer si la vérification du sexe est possible (10+ jours)
    //     $canVerifySex = $naissance->jours_depuis_naissance >= 10;

    //     // ✅ Règle pour le sexe : required seulement si vérification possible
    //     $sexRule = $canVerifySex ? 'required|in:male,female' : 'nullable|in:male,female';


    //     // ✅ VALIDATION
    //     $validated = $request->validate([
    //         'poids_moyen_naissance' => 'nullable|numeric|min:0|max:200',
    //         'etat_sante' => 'required|in:Excellent,Bon,Moyen,Faible',
    //         'observations' => 'nullable|string|max:1000',
    //         'date_sevrage_prevue' => 'required|date',
    //         'sex_verified' => 'nullable|boolean',
    //         'rabbits' => 'required|array|min:1',
    //         'rabbits.*.id' => 'nullable|exists:lapereaux,id',
    //         'rabbits.*.nom' => 'nullable|string|max:50',
    //         // ✅ Règle dynamique pour le sexe
    //         'rabbits.*.sex' => $sexRule,
    //         'rabbits.*.etat' => 'required|in:vivant,mort,vendu',
    //         'rabbits.*.poids_naissance' => 'nullable|numeric|min:0|max:200',
    //         'rabbits.*.etat_sante' => 'nullable|in:Excellent,Bon,Moyen,Faible',
    //         'rabbits.*.code' => 'nullable|string|max:20',

    //         // ✅ Règles vaccination
    //         'rabbits.*.vaccined' => 'nullable|in:1,0,on,off,true,false',
    //         'rabbits.*.vaccin_type' => 'nullable|in:myxomatose,vhd,pasteurellose,coccidiose,autre',
    //         'rabbits.*.vaccin_nom_autre' => 'nullable|string|max:100',
    //         'rabbits.*.vaccin_date' => 'nullable|date',
    //         'rabbits.*.vaccin_dose_numero' => 'nullable|integer|min:1|max:10',
    //     ]);


    //     // ✅ NORMALISATION DES VALEURS DE VACCINATION
    //     foreach ($validated['rabbits'] as &$rabbit) {
    //         if (isset($rabbit['vaccined'])) {
    //             $rabbit['vaccined'] = in_array($rabbit['vaccined'], [1, '1', 'on', 'true', true], true);
    //         }
    //         if (($rabbit['vaccin_type'] ?? '') === 'autre' && empty($rabbit['vaccin_nom_autre'])) {
    //             $rabbit['vaccin_nom_autre'] = 'Vaccin personnalisé';
    //         }
    //     }
    //     unset($rabbit);

    //     // ✅ Validation manuelle pour vaccination
    //     $errors = [];
    //     foreach ($validated['rabbits'] ?? [] as $i => $r) {
    //         if (!empty($r['vaccined']) && empty($r['vaccin_date'])) {
    //             $errors["rabbits.{$i}.vaccin_date"] = 'Date requise si vacciné.';
    //         }
    //         if (($r['vaccin_type'] ?? '') === 'autre' && empty($r['vaccin_nom_autre'])) {
    //             $errors["rabbits.{$i}.vaccin_nom_autre"] = 'Nom requis pour "Autre".';
    //         }
    //         if (!empty($r['code']) && !Lapereau::isCodeUnique($r['code'], $r['id'] ?? null)) {
    //             $errors["rabbits.{$i}.code"] = "Le code '{$r['code']}' existe déjà.";
    //         }
    //     }
    //     if (!empty($errors)) {
    //         return back()->withErrors($errors)->withInput();
    //     }


    //     if (!$naissance->can_verify_sex && $request->has('sex_verified')) {
    //         return back()->withErrors(['sex_verified' => 'Vérification possible après 10 jours.'])->withInput();
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $wasUnverified = !$naissance->sex_verified;
    //         $naissance->update($validated);

    //         $existingIds = [];
    //         foreach ($request->input('rabbits', []) as $rData) {
    //             if (!empty($rData['id'])) {
    //                 // Update existing
    //                 $lap = Lapereau::find($rData['id']);
    //                 if ($lap && $lap->naissance_id === $naissance->id) {
    //                     $rData['code'] = empty($rData['code']) ? $lap->code : $rData['code'];

    //                     // ✅ Mettre à jour les champs vaccination si présents
    //                     if (isset($rData['vaccined'])) {
    //                         $lap->update([
    //                             'vaccin_type' => $rData['vaccin_type'] ?? null,
    //                             'vaccin_nom_autre' => $rData['vaccin_nom_autre'] ?? null,
    //                             'vaccin_date' => $rData['vaccin_date'] ?? null,
    //                             'vaccin_dose_numero' => $rData['vaccin_dose_numero'] ?? 1,
    //                         ]);
    //                     } else {
    //                         $lap->update($rData);
    //                     }

    //                     $existingIds[] = $lap->id;
    //                 }
    //             } else {
    //                 // Create new
    //                 $rData['naissance_id'] = $naissance->id;
    //                 $rData['code'] = empty($rData['code']) ? Lapereau::generateUniqueCode() : $rData['code'];
    //                 $new = Lapereau::create($rData);

    //                 // ✅ Enregistrer vaccination si cochée pour nouveau lapereau
    //                 if (!empty($rData['vaccined'])) {
    //                     $new->update([
    //                         'vaccin_type' => $rData['vaccin_type'] ?? 'myxomatose',
    //                         'vaccin_nom_autre' => $rData['vaccin_nom_autre'] ?? null,
    //                         'vaccin_date' => $rData['vaccin_date'],
    //                         'vaccin_dose_numero' => $rData['vaccin_dose_numero'] ?? 1,
    //                     ]);
    //                 }

    //                 $existingIds[] = $new->id;
    //             }
    //         }

    //         // Delete removed rabbits
    //         Lapereau::where('naissance_id', $naissance->id)
    //             ->whereNotIn('id', $existingIds)
    //             ->delete();

    //         if ($wasUnverified && $naissance->sex_verified) {
    //             $naissance->markSexAsVerified();
    //         }

    //         DB::commit();
    //         return redirect()->route('naissances.show', $naissance)->with('success', 'Naissance mise à jour !');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()])->withInput();
    //     }
    // }


    // public function update(Request $request, Naissance $naissance)
    // {
    //     // ✅ Sécurité
    //     if ($naissance->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
    //         abort(403, 'Accès non autorisé.');
    //     }
    //     if (!auth()->user()->firm_id) {
    //         return back()->withErrors(['error' => 'Aucune entreprise liée.'])->withInput();
    //     }

    //     // ✅ Règle dynamique pour le sexe
    //     $canVerifySex = $naissance->jours_depuis_naissance >= 10;
    //     $sexRule = $canVerifySex ? 'required|in:male,female' : 'nullable|in:male,female';

    //     // ✅ Validation
    //     $validated = $request->validate([
    //         'poids_moyen_naissance' => 'nullable|numeric|min:0|max:200',
    //         'etat_sante' => 'required|in:Excellent,Bon,Moyen,Faible',
    //         'observations' => 'nullable|string|max:1000',
    //         'date_sevrage_prevue' => 'required|date',
    //         'sex_verified' => 'nullable|boolean',

    //         'rabbits' => 'required|array|min:1',
    //         'rabbits.*.id' => 'nullable|exists:lapereaux,id',
    //         'rabbits.*.nom' => 'nullable|string|max:50',
    //         'rabbits.*.sex' => $sexRule,
    //         'rabbits.*.etat' => 'required|in:vivant,mort,vendu',
    //         'rabbits.*.poids_naissance' => 'nullable|numeric|min:0|max:200',
    //         'rabbits.*.etat_sante' => 'nullable|in:Excellent,Bon,Moyen,Faible',
    //         'rabbits.*.code' => 'nullable|string|max:20',

    //         // ✅ Vaccins multiples
    //         'rabbits.*.vaccins' => 'nullable|array',
    //         'rabbits.*.vaccins.*.type' => 'nullable|in:myxomatose,vhd,pasteurellose,coccidiose,autre',
    //         'rabbits.*.vaccins.*.nom_autre' => 'nullable|string|max:100|required_if:rabbits.*.vaccins.*.type,autre',
    //         'rabbits.*.vaccins.*.date' => 'nullable|date',
    //         'rabbits.*.vaccins.*.dose' => 'nullable|integer|min:1|max:10',
    //         'rabbits.*.vaccins.*.rappel' => 'nullable|date|after_or_equal:rabbits.*.vaccins.*.date',
    //         'rabbits.*.vaccins.*.notes' => 'nullable|string|max:500',

    //         // ✅ Rétrocompatibilité
    //         'rabbits.*.vaccined' => 'nullable|in:1,0,on,off,true,false',
    //         'rabbits.*.vaccin_type' => 'nullable|in:myxomatose,vhd,pasteurellose,coccidiose,autre',
    //         'rabbits.*.vaccin_nom_autre' => 'nullable|string|max:100',
    //         'rabbits.*.vaccin_date' => 'nullable|date',
    //         'rabbits.*.vaccin_dose_numero' => 'nullable|integer|min:1|max:10',
    //     ]);

    //     // ✅ Normalisation
    //     foreach ($validated['rabbits'] as &$rabbit) {
    //         if (isset($rabbit['vaccined'])) {
    //             $rabbit['vaccined'] = in_array($rabbit['vaccined'], [1, '1', 'on', 'true', true], true);
    //         }
    //         if (($rabbit['vaccin_type'] ?? '') === 'autre' && empty($rabbit['vaccin_nom_autre'])) {
    //             $rabbit['vaccin_nom_autre'] = 'Vaccin personnalisé';
    //         }
    //         if (!empty($rabbit['vaccins']) && is_array($rabbit['vaccins'])) {
    //             foreach ($rabbit['vaccins'] as &$v) {
    //                 if (isset($v['type']) && $v['type'] === 'autre' && empty($v['nom_autre'])) {
    //                     $v['nom_autre'] = 'Vaccin personnalisé';
    //                 }
    //             }
    //             unset($v);
    //         }
    //     }
    //     unset($rabbit);

    //     // ✅ Validation manuelle
    //     $errors = [];
    //     foreach ($validated['rabbits'] ?? [] as $i => $r) {
    //         if (!empty($r['vaccined']) && empty($r['vaccin_date'])) {
    //             $errors["rabbits.{$i}.vaccin_date"] = 'Date requise si vacciné.';
    //         }
    //         if (($r['vaccin_type'] ?? '') === 'autre' && empty($r['vaccin_nom_autre'])) {
    //             $errors["rabbits.{$i}.vaccin_nom_autre"] = 'Nom requis pour "Autre".';
    //         }
    //         if (!empty($r['code'])) {
    //             $excludeId = $r['id'] ?? null;
    //             if (!Lapereau::isCodeUnique($r['code'], $excludeId)) {
    //                 $errors["rabbits.{$i}.code"] = "Le code '{$r['code']}' existe déjà.";
    //             }
    //         }
    //         // Vaccins multiples
    //         if (!empty($r['vaccins']) && is_array($r['vaccins'])) {
    //             foreach ($r['vaccins'] as $j => $v) {
    //                 if (!empty($v['type']) && empty($v['date'])) {
    //                     $errors["rabbits.{$i}.vaccins.{$j}.date"] = 'Date requise.';
    //                 }
    //                 if (($v['type'] ?? '') === 'autre' && empty($v['nom_autre'])) {
    //                     $errors["rabbits.{$i}.vaccins.{$j}.nom_autre"] = 'Nom requis.';
    //                 }
    //             }
    //         }
    //     }
    //     if (!empty($errors)) {
    //         return back()->withErrors($errors)->withInput();
    //     }

    //     // ✅ Vérification sexe
    //     if (!$naissance->can_verify_sex && $request->has('sex_verified')) {
    //         return back()->withErrors(['sex_verified' => 'Vérification possible après 10 jours.'])->withInput();
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $wasUnverified = !$naissance->sex_verified;
    //         $naissance->update($validated);

    //         $existingIds = [];

    //         foreach ($request->input('rabbits', []) as $rData) {
    //             if (!empty($rData['id'])) {
    //                 // ✅ Mise à jour lapereau existant
    //                 $lap = Lapereau::find($rData['id']);
    //                 if ($lap && $lap->naissance_id === $naissance->id) {
    //                     $rData['code'] = empty($rData['code']) ? $lap->code : $rData['code'];

    //                     // Mise à jour champs de base
    //                     $lap->update($rData);

    //                     // Mise à jour vaccination SIMPLE
    //                     if (isset($rData['vaccined'])) {
    //                         $lap->update([
    //                             'vaccin_type' => $rData['vaccin_type'] ?? null,
    //                             'vaccin_nom_autre' => $rData['vaccin_nom_autre'] ?? null,
    //                             'vaccin_date' => $rData['vaccin_date'] ?? null,
    //                             'vaccin_dose_numero' => $rData['vaccin_dose_numero'] ?? 1,
    //                             'vaccin_rappel_prevu' => $rData['vaccin_rappel_prevu'] ?? null,
    //                             'vaccin_notes' => $rData['vaccin_notes'] ?? null,
    //                         ]);
    //                     }

    //                     // ✅ Gestion vaccins MULTIPLES : synchronisation complète
    //                     if (isset($rData['vaccins']) && is_array($rData['vaccins'])) {
    //                         // Supprimer les vaccins non présents dans la requête
    //                         $vaccinIds = [];
    //                         foreach ($rData['vaccins'] as $vData) {
    //                             if (!empty($vData['id'])) {
    //                                 $vaccinIds[] = $vData['id'];
    //                             }
    //                         }
    //                         $lap->vaccinations()->whereNotIn('id', $vaccinIds)->delete();

    //                         // Créer/mettre à jour les vaccins
    //                         foreach ($rData['vaccins'] as $vData) {
    //                             if (!empty($vData['type']) && !empty($vData['date'])) {
    //                                 if ($vData['type'] === 'autre' && empty($vData['nom_autre'])) {
    //                                     continue;
    //                                 }

    //                                 $vaccinData = [
    //                                     'type' => $vData['type'],
    //                                     'nom_personnalise' => $vData['nom_autre'] ?? null,
    //                                     'date_administration' => $vData['date'],
    //                                     'dose_numero' => $vData['dose'] ?? 1,
    //                                     'rappel_prevu' => $vData['rappel'] ?? null,
    //                                     'notes' => $vData['notes'] ?? null,
    //                                     'administered_by' => auth()->id(),
    //                                 ];

    //                                 if (!empty($vData['id'])) {
    //                                     // Update existing
    //                                     $lap->vaccinations()->where('id', $vData['id'])->update($vaccinData);
    //                                 } else {
    //                                     // Create new
    //                                     $lap->vaccinations()->create($vaccinData);
    //                                 }
    //                             }
    //                         }
    //                     }

    //                     $existingIds[] = $lap->id;
    //                 }
    //             } else {
    //                 // ✅ Création nouveau lapereau
    //                 $rData['naissance_id'] = $naissance->id;
    //                 $rData['code'] = empty($rData['code']) ? Lapereau::generateUniqueCode() : $rData['code'];
    //                 $new = Lapereau::create($rData);

    //                 // Vaccination simple
    //                 if (!empty($rData['vaccined']) && !empty($rData['vaccin_date'])) {
    //                     $new->update([
    //                         'vaccin_type' => $rData['vaccin_type'] ?? 'myxomatose',
    //                         'vaccin_nom_autre' => $rData['vaccin_nom_autre'] ?? null,
    //                         'vaccin_date' => $rData['vaccin_date'],
    //                         'vaccin_dose_numero' => $rData['vaccin_dose_numero'] ?? 1,
    //                     ]);
    //                 }

    //                 // Vaccins multiples
    //                 if (!empty($rData['vaccins']) && is_array($rData['vaccins'])) {
    //                     foreach ($rData['vaccins'] as $vData) {
    //                         if (!empty($vData['type']) && !empty($vData['date'])) {
    //                             if ($vData['type'] === 'autre' && empty($vData['nom_autre'])) {
    //                                 continue;
    //                             }
    //                             $new->vaccinations()->create([
    //                                 'type' => $vData['type'],
    //                                 'nom_personnalise' => $vData['nom_autre'] ?? null,
    //                                 'date_administration' => $vData['date'],
    //                                 'dose_numero' => $vData['dose'] ?? 1,
    //                                 'rappel_prevu' => $vData['rappel'] ?? null,
    //                                 'notes' => $vData['notes'] ?? null,
    //                                 'administered_by' => auth()->id(),
    //                             ]);
    //                         }
    //                     }
    //                 }

    //                 $existingIds[] = $new->id;
    //             }
    //         }

    //         // ✅ Supprimer lapereaux retirés
    //         Lapereau::where('naissance_id', $naissance->id)
    //             ->whereNotIn('id', $existingIds)
    //             ->delete();

    //         // ✅ Vérification sexe
    //         if ($wasUnverified && $naissance->sex_verified) {
    //             $naissance->markSexAsVerified();
    //         }

    //         DB::commit();
    //         return redirect()->route('naissances.show', $naissance)
    //             ->with('success', 'Naissance mise à jour !');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()])->withInput();
    //     }
    // }



    // public function update(Request $request, Naissance $naissance)
    // {
    //     // ✅ Sécurité
    //     if ($naissance->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
    //         abort(403, 'Accès non autorisé.');
    //     }
    //     if (!auth()->user()->firm_id) {
    //         return back()->withErrors(['error' => 'Aucune entreprise liée.'])->withInput();
    //     }

    //     // ✅ Règle dynamique pour le sexe
    //     $canVerifySex = $naissance->jours_depuis_naissance >= 10;
    //     $sexRule = $canVerifySex ? 'required|in:male,female' : 'nullable|in:male,female';

    //     // ✅ Validation
    //     $validated = $request->validate([
    //         'poids_moyen_naissance' => 'nullable|numeric|min:0|max:200',
    //         'etat_sante' => 'required|in:Excellent,Bon,Moyen,Faible',
    //         'observations' => 'nullable|string|max:1000',
    //         'date_sevrage_prevue' => 'required|date',
    //         'sex_verified' => 'nullable|boolean',

    //         'rabbits' => 'required|array|min:1',
    //         'rabbits.*.id' => 'nullable|exists:lapereaux,id',
    //         'rabbits.*.nom' => 'nullable|string|max:50',
    //         'rabbits.*.sex' => $sexRule,
    //         'rabbits.*.etat' => 'required|in:vivant,mort,vendu',
    //         'rabbits.*.poids_naissance' => 'nullable|numeric|min:0|max:200',
    //         'rabbits.*.etat_sante' => 'nullable|in:Excellent,Bon,Moyen,Faible',
    //         'rabbits.*.code' => 'nullable|string|max:20',

    //         // ✅ Vaccins multiples
    //         'rabbits.*.vaccins' => 'nullable|array',
    //         'rabbits.*.vaccins.*.type' => 'nullable|in:myxomatose,vhd,pasteurellose,coccidiose,autre',
    //         'rabbits.*.vaccins.*.nom_autre' => 'nullable|string|max:100|required_if:rabbits.*.vaccins.*.type,autre',
    //         'rabbits.*.vaccins.*.date' => 'nullable|date',
    //         'rabbits.*.vaccins.*.dose' => 'nullable|integer|min:1|max:10',
    //         'rabbits.*.vaccins.*.rappel' => 'nullable|date|after_or_equal:rabbits.*.vaccins.*.date',
    //         'rabbits.*.vaccins.*.notes' => 'nullable|string|max:500',

    //         // ✅ Rétrocompatibilité
    //         'rabbits.*.vaccined' => 'nullable|in:1,0,on,off,true,false',
    //         'rabbits.*.vaccin_type' => 'nullable|in:myxomatose,vhd,pasteurellose,coccidiose,autre',
    //         'rabbits.*.vaccin_nom_autre' => 'nullable|string|max:100',
    //         'rabbits.*.vaccin_date' => 'nullable|date',
    //         'rabbits.*.vaccin_dose_numero' => 'nullable|integer|min:1|max:10',
    //     ]);

    //     // ✅ Normalisation
    //     foreach ($validated['rabbits'] as &$rabbit) {
    //         if (isset($rabbit['vaccined'])) {
    //             $rabbit['vaccined'] = in_array($rabbit['vaccined'], [1, '1', 'on', 'true', true], true);
    //         }
    //         if (($rabbit['vaccin_type'] ?? '') === 'autre' && empty($rabbit['vaccin_nom_autre'])) {
    //             $rabbit['vaccin_nom_autre'] = 'Vaccin personnalisé';
    //         }
    //         if (!empty($rabbit['vaccins']) && is_array($rabbit['vaccins'])) {
    //             foreach ($rabbit['vaccins'] as &$v) {
    //                 if (isset($v['type']) && $v['type'] === 'autre' && empty($v['nom_autre'])) {
    //                     $v['nom_autre'] = 'Vaccin personnalisé';
    //                 }
    //             }
    //             unset($v);
    //         }
    //     }
    //     unset($rabbit);

    //     // ✅ Validation manuelle
    //     $errors = [];
    //     foreach ($validated['rabbits'] ?? [] as $i => $r) {
    //         if (!empty($r['vaccined']) && empty($r['vaccin_date'])) {
    //             $errors["rabbits.{$i}.vaccin_date"] = 'Date requise si vacciné.';
    //         }
    //         if (($r['vaccin_type'] ?? '') === 'autre' && empty($r['vaccin_nom_autre'])) {
    //             $errors["rabbits.{$i}.vaccin_nom_autre"] = 'Nom requis pour "Autre".';
    //         }
    //         if (!empty($r['code'])) {
    //             $excludeId = $r['id'] ?? null;
    //             if (!Lapereau::isCodeUnique($r['code'], $excludeId)) {
    //                 $errors["rabbits.{$i}.code"] = "Le code '{$r['code']}' existe déjà.";
    //             }
    //         }
    //         // Vaccins multiples
    //         if (!empty($r['vaccins']) && is_array($r['vaccins'])) {
    //             foreach ($r['vaccins'] as $j => $v) {
    //                 if (!empty($v['type']) && empty($v['date'])) {
    //                     $errors["rabbits.{$i}.vaccins.{$j}.date"] = 'Date requise.';
    //                 }
    //                 if (($v['type'] ?? '') === 'autre' && empty($v['nom_autre'])) {
    //                     $errors["rabbits.{$i}.vaccins.{$j}.nom_autre"] = 'Nom requis.';
    //                 }
    //             }
    //         }
    //     }
    //     if (!empty($errors)) {
    //         return back()->withErrors($errors)->withInput();
    //     }

    //     // ✅ Vérification sexe
    //     if (!$naissance->can_verify_sex && $request->has('sex_verified')) {
    //         return back()->withErrors(['sex_verified' => 'Vérification possible après 10 jours.'])->withInput();
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $wasUnverified = !$naissance->sex_verified;
    //         $naissance->update($validated);

    //         $existingIds = [];

    //         foreach ($request->input('rabbits', []) as $rData) {
    //             if (!empty($rData['id'])) {
    //                 // ✅ Mise à jour lapereau existant
    //                 $lap = Lapereau::find($rData['id']);
    //                 if ($lap && $lap->naissance_id === $naissance->id) {
    //                     $rData['code'] = empty($rData['code']) ? $lap->code : $rData['code'];

    //                     // Mise à jour champs de base
    //                     $lap->update($rData);

    //                     // Mise à jour vaccination SIMPLE
    //                     if (isset($rData['vaccined'])) {
    //                         $lap->update([
    //                             'vaccin_type' => $rData['vaccin_type'] ?? null,
    //                             'vaccin_nom_autre' => $rData['vaccin_nom_autre'] ?? null,
    //                             'vaccin_date' => $rData['vaccin_date'] ?? null,
    //                             'vaccin_dose_numero' => $rData['vaccin_dose_numero'] ?? 1,
    //                             'vaccin_rappel_prevu' => $rData['vaccin_rappel_prevu'] ?? null,
    //                             'vaccin_notes' => $rData['vaccin_notes'] ?? null,
    //                         ]);
    //                     }

    //                     // ✅ Gestion vaccins MULTIPLES : synchronisation complète
    //                     if (isset($rData['vaccins']) && is_array($rData['vaccins'])) {
    //                         // Supprimer les vaccins non présents dans la requête
    //                         $vaccinIds = [];
    //                         foreach ($rData['vaccins'] as $vData) {
    //                             if (!empty($vData['id'])) {
    //                                 $vaccinIds[] = $vData['id'];
    //                             }
    //                         }
    //                         $lap->vaccinations()->whereNotIn('id', $vaccinIds)->delete();

    //                         // Créer/mettre à jour les vaccins
    //                         foreach ($rData['vaccins'] as $vData) {
    //                             if (!empty($vData['type']) && !empty($vData['date'])) {
    //                                 if ($vData['type'] === 'autre' && empty($vData['nom_autre'])) {
    //                                     continue;
    //                                 }

    //                                 $vaccinData = [
    //                                     'type' => $vData['type'],
    //                                     'nom_personnalise' => $vData['nom_autre'] ?? null,
    //                                     'date_administration' => $vData['date'],
    //                                     'dose_numero' => $vData['dose'] ?? 1,
    //                                     'rappel_prevu' => $vData['rappel'] ?? null,
    //                                     'notes' => $vData['notes'] ?? null,
    //                                     'administered_by' => auth()->id(),
    //                                 ];

    //                                 if (!empty($vData['id'])) {
    //                                     // Update existing
    //                                     $lap->vaccinations()->where('id', $vData['id'])->update($vaccinData);
    //                                 } else {
    //                                     // Create new
    //                                     $lap->vaccinations()->create($vaccinData);
    //                                 }
    //                             }
    //                         }
    //                     }

    //                     $existingIds[] = $lap->id;
    //                 }
    //             } else {
    //                 // ✅ Création nouveau lapereau
    //                 $rData['naissance_id'] = $naissance->id;
    //                 $rData['code'] = empty($rData['code']) ? Lapereau::generateUniqueCode() : $rData['code'];
    //                 $new = Lapereau::create($rData);

    //                 // Vaccination simple
    //                 if (!empty($rData['vaccined']) && !empty($rData['vaccin_date'])) {
    //                     $new->update([
    //                         'vaccin_type' => $rData['vaccin_type'] ?? 'myxomatose',
    //                         'vaccin_nom_autre' => $rData['vaccin_nom_autre'] ?? null,
    //                         'vaccin_date' => $rData['vaccin_date'],
    //                         'vaccin_dose_numero' => $rData['vaccin_dose_numero'] ?? 1,
    //                     ]);
    //                 }

    //                 // Vaccins multiples
    //                 if (!empty($rData['vaccins']) && is_array($rData['vaccins'])) {
    //                     foreach ($rData['vaccins'] as $vData) {
    //                         if (!empty($vData['type']) && !empty($vData['date'])) {
    //                             if ($vData['type'] === 'autre' && empty($vData['nom_autre'])) {
    //                                 continue;
    //                             }
    //                             $new->vaccinations()->create([
    //                                 'type' => $vData['type'],
    //                                 'nom_personnalise' => $vData['nom_autre'] ?? null,
    //                                 'date_administration' => $vData['date'],
    //                                 'dose_numero' => $vData['dose'] ?? 1,
    //                                 'rappel_prevu' => $vData['rappel'] ?? null,
    //                                 'notes' => $vData['notes'] ?? null,
    //                                 'administered_by' => auth()->id(),
    //                             ]);
    //                         }
    //                     }
    //                 }

    //                 $existingIds[] = $new->id;
    //             }
    //         }

    //         // ✅ Supprimer lapereaux retirés
    //         Lapereau::where('naissance_id', $naissance->id)
    //             ->whereNotIn('id', $existingIds)
    //             ->delete();

    //         // ✅ Vérification sexe
    //         if ($wasUnverified && $naissance->sex_verified) {
    //             $naissance->markSexAsVerified();
    //         }

    //         DB::commit();
    //         return redirect()->route('naissances.show', $naissance)
    //             ->with('success', 'Naissance mise à jour !');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()])->withInput();
    //     }
    // }

    // public function show(Naissance $naissance, Request $request)
    // {
    //     // ✅ SECURITY FIX: Explicit Ownership Check (todo.md Step 4)
    //     if ($naissance->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
    //         abort(403, 'Unauthorized access to this record.');
    //     }

    //     $naissance->load(['miseBas.femelle', 'miseBas.saillie.male', 'lapereaux']);
    //     $canVerifySex = $naissance->can_verify_sex;
    //     $daysUntilVerification = max(0, 10 - $naissance->jours_depuis_naissance);

    //     // ✅ Search lapereaux
    //     $lapereauxQuery = $naissance->lapereaux();
    //     if ($request->has('search_lapereau')) {
    //         $search = $request->search_lapereau;
    //         $lapereauxQuery->where(function ($q) use ($search) {
    //             $q->where('nom', 'LIKE', "%{$search}%")
    //                 ->orWhere('code', 'LIKE', "%{$search}%");
    //         });
    //     }

    //     // ✅ Filter by status
    //     if ($request->has('filter_etat')) {
    //         $lapereauxQuery->where('etat', $request->filter_etat);
    //     }

    //     // ✅ Filter by sex
    //     if ($request->has('filter_sex')) {
    //         $lapereauxQuery->where('sex', $request->filter_sex);
    //     }

    //     // ✅ Paginate lapereaux (10 per page)
    //     $lapereaux = $lapereauxQuery->paginate(10);

    //     return view('naissances.show', compact('naissance', 'canVerifySex', 'daysUntilVerification', 'lapereaux'));
    // }


    //     public function show(Naissance $naissance, Request $request)
// {
//     // ✅ Sécurité
//     if ($naissance->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
//         abort(403, 'Accès non autorisé.');
//     }

    //     // ✅ Charger les lapereaux AVEC leurs vaccins
//     $naissance->load([
//         'miseBas.femelle', 
//         'miseBas.saillie.male', 
//         'lapereaux.vaccinations' // ✅ IMPORTANT : charger les vaccins
//     ]);

    //     $canVerifySex = $naissance->can_verify_sex;
//     $daysUntilVerification = max(0, 10 - $naissance->jours_depuis_naissance);

    //     // ✅ Filtres et pagination
//     $lapereauxQuery = $naissance->lapereaux();
//     if ($request->has('search_lapereau')) {
//         $search = $request->search_lapereau;
//         $lapereauxQuery->where(function ($q) use ($search) {
//             $q->where('nom', 'LIKE', "%{$search}%")
//               ->orWhere('code', 'LIKE', "%{$search}%");
//         });
//     }
//     if ($request->has('filter_etat')) {
//         $lapereauxQuery->where('etat', $request->filter_etat);
//     }
//     if ($request->has('filter_sex')) {
//         $lapereauxQuery->where('sex', $request->filter_sex);
//     }
//     $lapereaux = $lapereauxQuery->paginate(10);

    //     return view('naissances.show', compact(
//         'naissance', 
//         'canVerifySex', 
//         'daysUntilVerification', 
//         'lapereaux'
//     ));
// }



    public function show(Naissance $naissance, Request $request)
    {
        // ✅ Sécurité
        if ($naissance->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé.');
        }

        // ✅ Charger les lapereaux AVEC leurs vaccins (1 seule requête)
        $naissance->load([
            'miseBas.femelle',
            'miseBas.saillie.male',
            'lapereaux.vaccinations'
        ]);

        $canVerifySex = $naissance->can_verify_sex;
        $daysUntilVerification = max(0, 10 - $naissance->jours_depuis_naissance);

        // ✅ Filtres et pagination
        $lapereauxQuery = $naissance->lapereaux();
        if ($request->has('search_lapereau')) {
            $search = $request->search_lapereau;
            $lapereauxQuery->where(function ($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }
        if ($request->has('filter_etat')) {
            $lapereauxQuery->where('etat', $request->filter_etat);
        }
        if ($request->has('filter_sex')) {
            $lapereauxQuery->where('sex', $request->filter_sex);
        }
        $lapereaux = $lapereauxQuery->paginate(10);

        return view('naissances.show', compact(
            'naissance',
            'canVerifySex',
            'daysUntilVerification',
            'lapereaux'
        ));
    }

    public function edit(Naissance $naissance)
    {
        // ✅ Sécurité
        if ($naissance->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé.');
        }

        if (!auth()->user()->firm_id) {
            return back()
                ->withErrors(['error' => 'Votre compte n\'est associé à aucune entreprise. Contactez le support.'])
                ->withInput();
        }

        // ✅ Charger les lapereaux AVEC leurs vaccins (IMPORTANT !)
        $naissance->load(['miseBas.femelle', 'lapereaux.vaccinations']);

        // ✅ Formater les données pour le frontend
        $lapereaux = $naissance->lapereaux->map(function ($lapin) {
            return [
                'id' => $lapin->id,
                'nom' => $lapin->nom,
                'code' => $lapin->code,
                'sex' => $lapin->sex,
                'etat' => $lapin->etat,
                'poids_naissance' => $lapin->poids_naissance,
                'etat_sante' => $lapin->etat_sante,

                // ✅ Rétrocompatibilité : champs vaccination simple
                'vaccin_type' => $lapin->vaccin_type,
                'vaccin_nom_autre' => $lapin->vaccin_nom_autre,
                'vaccin_date' => $lapin->vaccin_date?->format('Y-m-d'),
                'vaccin_rappel_prevu' => $lapin->vaccin_rappel_prevu?->format('Y-m-d'),
                'vaccin_dose_numero' => $lapin->vaccin_dose_numero,
                'vaccin_notes' => $lapin->vaccin_notes,

                // ✅ NOUVEAU : Vaccins multiples formatés
                'vaccinations' => $lapin->vaccinations->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'type' => $v->type,
                        'nom_personnalise' => $v->nom_personnalise,
                        'date_administration' => $v->date_administration?->format('Y-m-d'),
                        'dose_numero' => $v->dose_numero,
                        'rappel_prevu' => $v->rappel_prevu?->format('Y-m-d'),
                        'notes' => $v->notes,
                    ];
                })->values(), // Réindexer le tableau
            ];
        })->values(); // Réindexer le tableau principal

        $canVerifySex = $naissance->can_verify_sex;
        $daysUntilVerification = max(0, 10 - $naissance->jours_depuis_naissance);

        return view('naissances.edit', compact(
            'naissance',
            'canVerifySex',
            'daysUntilVerification',
            'lapereaux'
        ));
    }

    // public function update(Request $request, Naissance $naissance)
    // {
    //     // ✅ SECURITY FIX: Explicit Ownership Check (todo.md Step 4)
    //     if ($naissance->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
    //         abort(403, 'Unauthorized access to this record.');
    //     }

    //     // ✅ TODO.MD STEP 4: CRITICAL - Check if user has a firm
    //     if (!auth()->user()->firm_id) {
    //         return back()
    //             ->withErrors(['error' => 'Votre compte n\'est associé à aucune entreprise. Contactez le support.'])
    //             ->withInput();
    //     }

    //     $validated = $request->validate([
    //         'poids_moyen_naissance' => 'nullable|numeric|min:0|max:200',
    //         'etat_sante' => 'required|in:Excellent,Bon,Moyen,Faible',
    //         'observations' => 'nullable|string|max:1000',
    //         'date_sevrage_prevue' => 'required|date|after:date_mise_bas',
    //         'date_vaccination_prevue' => 'required|date|after:date_mise_bas',
    //         'sex_verified' => 'nullable|boolean',
    //         'rabbits' => 'required|array|min:1',
    //         'rabbits.*.id' => 'nullable|exists:lapereaux,id',
    //         'rabbits.*.nom' => 'nullable|string|max:50',
    //         'rabbits.*.sex' => 'required|in:male,female',
    //         'rabbits.*.etat' => 'required|in:vivant,mort,vendu',
    //         'rabbits.*.poids_naissance' => 'nullable|numeric|min:0|max:200',
    //         'rabbits.*.etat_sante' => 'nullable|in:Excellent,Bon,Moyen,Faible',
    //         'rabbits.*.observations' => 'nullable|string|max:500',
    //         'rabbits.*.code' => 'nullable|string|max:20',



    //         // ✅ Vaccination
    //         'rabbits.*.vaccin_type' => 'nullable|in:myxomatose,vhd,pasteurellose,coccidiose,autre',
    //         'rabbits.*.vaccin_nom_autre' => 'nullable|string|max:100|required_if:rabbits.*.vaccin_type,autre',
    //         // 'rabbits.*.vaccin_date' => 'nullable|date|before_or_equal:today',
    //         'rabbits.*.vaccin_dose_numero' => 'nullable|integer|min:1|max:10',
    //         'rabbits.*.vaccin_rappel_prevu' => 'nullable|date|after_or_equal:rabbits.*.vaccin_date',
    //         'rabbits.*.vaccin_notes' => 'nullable|string|max:500',
    //         // Dans $rabbitsRules
    //         'rabbits.*.vaccined' => 'nullable|boolean',
    //         'rabbits.*.vaccin_date' => 'nullable|date|required_if:rabbits.*.vaccined,true',

    //     ]);

    //     // ✅ Check if sex verification is allowed (10+ days)
    //     if (!$naissance->can_verify_sex && $request->has('sex_verified')) {
    //         return back()->withErrors([
    //             'sex_verified' => 'La vérification du sexe n\'est possible qu\'après 10 jours.'
    //         ])->withInput();
    //     }

    //     // ✅ VALIDATION: Count against mise_bas
    //     $maxAllowed = $naissance->max_allowed_lapereaux;
    //     $newCount = count($validated['rabbits']);
    //     if ($maxAllowed > 0 && $newCount > $maxAllowed) {
    //         return back()->withErrors([
    //             'rabbits' => "Vous ne pouvez pas créer plus de {$maxAllowed} lapereaux pour cette mise bas."
    //         ])->withInput();
    //     }

    //     // ✅ VALIDATION: Check unique codes
    //     foreach ($validated['rabbits'] as $index => $rabbit) {
    //         if (!empty($rabbit['code'])) {
    //             $excludeId = $rabbit['id'] ?? null;
    //             if (!Lapereau::isCodeUnique($rabbit['code'], $excludeId)) {
    //                 return back()->withErrors([
    //                     "rabbits.{$index}.code" => "Le code '{$rabbit['code']}' existe déjà."
    //                 ])->withInput();
    //             }
    //         }
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $wasUnverified = !$naissance->sex_verified;
    //         $naissance->update($validated);

    //         // ✅ Sync Lapereaux
    //         $incomingRabbits = $request->input('rabbits', []);
    //         $existingIds = [];

    //         foreach ($incomingRabbits as $rabbitData) {
    //             if (!empty($rabbitData['id'])) {
    //                 // Update existing
    //                 $lapereau = Lapereau::find($rabbitData['id']);
    //                 if ($lapereau && $lapereau->naissance_id === $naissance->id) {
    //                     if (empty($rabbitData['code'])) {
    //                         $rabbitData['code'] = $lapereau->code;
    //                     }
    //                     $lapereau->update($rabbitData);
    //                     $existingIds[] = $lapereau->id;
    //                 }
    //             } else {
    //                 // Create new (with auto-generated code)
    //                 $rabbitData['naissance_id'] = $naissance->id;
    //                 if (empty($rabbitData['code'])) {
    //                     $rabbitData['code'] = Lapereau::generateUniqueCode();
    //                 }
    //                 $newRabbit = Lapereau::create($rabbitData);
    //                 $existingIds[] = $newRabbit->id;
    //             }
    //         }

    //         // Delete removed rabbits
    //         Lapereau::where('naissance_id', $naissance->id)
    //             ->whereNotIn('id', $existingIds)
    //             ->delete();

    //         // Mark as verified if checkbox checked
    //         if ($wasUnverified && $naissance->sex_verified) {
    //             $naissance->markSexAsVerified();
    //             $this->notifyUser([
    //                 'type' => 'success',
    //                 'title' => '✅ Vérification de Portée Complétée',
    //                 'message' => "La portée de {$naissance->femelle->nom} a été vérifiée ({$naissance->total_lapereaux} lapereaux)",
    //                 'action_url' => route('naissances.show', $naissance),
    //             ]);
    //         }

    //         DB::commit();

    //         // ✅ TODO.MD STEP 4: Pass null for firm_id to let Model handle auto-detection
    //         FirmAuditLog::log(
    //             null,
    //             auth()->id(),
    //             'naissance_updated',
    //             'sex_verified',
    //             $wasUnverified ? 'false' : 'true',
    //             $naissance->sex_verified ? 'true' : 'false'
    //         );

    //         return redirect()->route('naissances.show', $naissance)
    //             ->with('success', 'Naissance mise à jour !');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()
    //             ->withErrors(['error' => 'Erreur: ' . $e->getMessage()])
    //             ->withInput();
    //     }
    // }

    public function checkCode(Request $request)
    {
        $exists = Lapereau::where('code', $request->code)->exists();
        return response()->json(['available' => !$exists]);
    }

    public function destroy(Naissance $naissance)
    {
        // ✅ SECURITY FIX: Explicit Ownership Check (todo.md Step 4)
        if ($naissance->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to this record.');
        }

        // ✅ TODO.MD STEP 4: CRITICAL - Check if user has a firm
        if (!auth()->user()->firm_id) {
            return back()
                ->withErrors(['error' => 'Votre compte n\'est associé à aucune entreprise. Contactez le support.'])
                ->withInput();
        }

        $femelleName = $naissance->femelle->nom ?? 'Inconnue';
        $totalLapereaux = $naissance->total_lapereaux;

        // ✅ TODO.MD STEP 4: Pass null for firm_id to let Model handle auto-detection
        FirmAuditLog::log(
            null,
            auth()->id(),
            'naissance_deleted',
            'id',
            $naissance->id,
            null
        );

        $naissance->delete(); // Cascade deletes lapereaux

        $this->notifyUser([
            'type' => 'warning',
            'title' => '🗑️ Naissance Supprimée',
            'message' => "Naissance de {$femelleName} ({$totalLapereaux} lapereaux) supprimée",
            'action_url' => route('naissances.index'),
        ]);

        return redirect()->route('naissances.index')
            ->with('success', 'Naissance supprimée !');
    }
}
