<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Creneau;
use App\Models\Jour;
use App\Models\Matiere;
use App\Models\School;
use App\Models\Seance;
use App\Models\User;
use App\Models\Year;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SessionCourseController extends Controller
{
    // Afficher l'emploi du temps d'une classe spécifique
    public function showByClasse($classeId)
    {
        $classe = Classe::findOrFail($classeId);
        $actifYear = Year::where('est_active', true)->first();

        $seances = Seance::with(['matiere', 'enseignant', 'jour', 'creneau'])
            ->where('classe_id', $classeId)
            ->where('annee_scolaire_id', $actifYear?->id)
            ->get();

        $jours = Jour::orderBy('ordre')->get();
        $creneaux = Creneau::orderBy('heure_debut')->get();

        $matieresIds = DB::table('classe_matiere')
            ->where('classe_id', $classeId)
            ->pluck('matiere_id');

        $matieres = Matiere::whereIn('id', $matieresIds)
            ->select('id', 'nom')
            ->orderBy('nom')
            ->get();

        $enseignants = User::select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('pages.emplois.classe', compact('classe', 'seances', 'jours', 'creneaux', 'matieres', 'enseignants'));
    }

    // Enregistrer une nouvelle séance de cours
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:users,id',
            'jour_id' => 'required|exists:jours,id',
            'creneau_id' => 'required|exists:creneaus,id',
        ]);

        $actifYear = Year::where('est_active', true)->first();

        if (! $actifYear) {
            return redirect()->back()->withErrors(['msg' => 'Aucune année scolaire active trouvée.']);
        }

        $validated['annee_scolaire_id'] = $actifYear->id;

        $conflitTeacher = Seance::where('annee_scolaire_id', $actifYear->id)
            ->where('enseignant_id', $validated['enseignant_id'])
            ->where('jour_id', $validated['jour_id'])
            ->where('creneau_id', $validated['creneau_id'])
            ->exists();

        if ($conflitTeacher) {
            return redirect()->back()->withErrors(['conflit' => 'Cet enseignant a déjà un cours prévu à ce créneau !']);
        }

        Seance::create($validated);

        return redirect()->back()->with('success', 'Séance planifiée avec succès.');
    }

    public function telechargerPdfClasse($classeId)
    {
        $classe = Classe::findOrFail($classeId);
        $actifYear = Year::where('est_active', true)->first();
        $school = School::first();

        $seances = Seance::with(['matiere', 'enseignant', 'jour', 'creneau'])
            ->where('classe_id', $classeId)
            ->where('annee_scolaire_id', $actifYear?->id)
            ->get();

        $jours = Jour::orderBy('ordre')->get();
        $creneaux = Creneau::orderBy('heure_debut')->get();

        $pdf = Pdf::loadView('pages.emplois.pdf.classe-pdf', compact('classe', 'seances', 'jours', 'creneaux', 'actifYear', 'school'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download(str("emploi-du-temps-{$classe->nom}")->slug().'.pdf');
    }

    public function indexClasses()
    {
        $classes = Classe::orderBy('nom')->get();

        return view('pages.emplois.choice-class', compact('classes'));
    }

    // Afficher l'emploi du temps d'un enseignant spécifique
    public function showByEnseignant($userId)
    {
        $teacher = User::findOrFail($userId);
        $actifYear = Year::where('est_active', true)->first();

        $seances = Seance::with(['matiere', 'classe', 'jour', 'creneau'])
            ->where('enseignant_id', $userId)
            ->where('annee_scolaire_id', $actifYear?->id)
            ->get();

        $days = Jour::orderBy('ordre')->get();
        $creneaux = Creneau::orderBy('heure_debut')->get();

        return view('pages.emplois.enseignant', compact('teacher', 'seances', 'days', 'creneaux'));
    }

    // Télécharger le PDF de l'emploi du temps d'un enseignant
    public function telechargerPdfEnseignant($userId)
    {
        $enseignant = User::findOrFail($userId);
        $actifYear = Year::where('est_active', true)->first();

        $seances = Seance::with(['matiere', 'classe', 'jour', 'creneau'])
            ->where('enseignant_id', $userId)
            ->where('annee_scolaire_id', $actifYear?->id)
            ->get();

        $jours = Jour::orderBy('ordre')->get();
        $creneaux = Creneau::orderBy('heure_debut')->get();

        $pdf = Pdf::loadView('pages.emplois.pdf.enseignant', compact('enseignant', 'seances', 'jours', 'creneaux'))
            ->setPaper('a4', 'landscape');

        return $pdf->download(str('emploi-du-temps-'.$enseignant->name)->slug('_').'.pdf');
    }
}
