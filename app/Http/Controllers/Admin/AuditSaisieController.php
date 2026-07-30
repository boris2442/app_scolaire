<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe; // <-- On importe Classe au lieu de Niveau
use App\Models\Evaluation;
use App\Models\Inscription;
use App\Models\Note;
use App\Models\Sequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ScolariteService;

class AuditSaisieController extends Controller
{
    protected $scolarite;

    public function __construct(ScolariteService $scolarite)
    {
        $this->scolarite = $scolarite;
    }

    public function index(Request $request)
    {
        $anneeActive = $this->scolarite->getAnneeActive();

        // On récupère directement toutes les classes (plus de niveaux)
        $classes = Classe::all();

        // On récupère les séquences de l'année en cours (sans doublons)
        $sequences = Sequence::whereHas('trimestre', function ($q) use ($anneeActive) {
            $q->where('annee_scolaire_id', $anneeActive->id);
        })->get();

        $classeId = $request->get('classe_id');
        $sequenceId = $request->get('sequence_id');
        $auditData = [];

        if ($classeId && $sequenceId) {
            $effectif = Inscription::where('classe_id', $classeId)->count();

            // Récupération des matières + l'enseignant affecté
            $matieres = DB::table('classe_matiere')
                ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
                ->leftJoin('affectations', function ($join) use ($classeId) {
                    $join->on('matieres.id', '=', 'affectations.matiere_id')
                        ->where('affectations.classe_id', '=', $classeId);
                })
                ->leftJoin('users', 'affectations.enseignant_id', '=', 'users.id')
                ->where('classe_matiere.classe_id', $classeId)
                ->select('matieres.id', 'matieres.nom', 'users.name as enseignant', 'users.phone')
                ->get();

            $matiereIds = $matieres->pluck('id');

            // 1. On récupère TOUTES les évaluations d'un coup pour cette classe et cette séquence
            $evaluations = Evaluation::where('classe_id', $classeId)
                ->where('sequence_id', $sequenceId)
                ->whereIn('matiere_id', $matiereIds)
                ->get()
                ->keyBy('matiere_id');

            $evaluationsIds = $evaluations->pluck('id');

            // 2. On compte TOUTES les notes d'un coup groupées par evaluation_id
            $notesCounts = Note::whereIn('evaluation_id', $evaluationsIds)
                ->select('evaluation_id', DB::raw('count(*) as total'))
                ->groupBy('evaluation_id')
                ->pluck('total', 'evaluation_id');

            foreach ($matieres as $matiere) {
                $evaluation = $evaluations->get($matiere->id);

                $nbNotes = $evaluation ? ($notesCounts[$evaluation->id] ?? 0) : 0;

                $pourcentage = 0;
                if ($effectif > 0) {
                    $pourcentage = min(100, round(($nbNotes / $effectif) * 100));
                }

                $auditData[] = [
                    'matiere' => $matiere->nom,
                    'enseignant' => $matiere->enseignant ?? 'Non affecté',
                    'phone' => $matiere->phone ?? 'Non disponible',
                    'saisies' => $nbNotes,
                    'attendu' => $effectif,
                    'pourcentage' => $pourcentage
                ];
            }
        }

        // On passe $classes à la vue au lieu de $niveaux
        return view('pages.admin.audit-saisie', compact('classes', 'sequences', 'auditData'));
    }
}
