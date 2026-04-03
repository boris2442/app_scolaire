<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Evaluation;
use App\Models\Inscription;
use App\Models\Niveau;
use App\Models\Note;
use App\Models\Sequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ScolariteService;

class AuditSaisieController extends Controller
{
    // public function index(Request $request)
    //     {
    //         $classes = Classe::all();
    //         $sequences = Sequence::all();

    //         $classeId = $request->get('classe_id');
    //         $sequenceId = $request->get('sequence_id');
    //         $auditData = [];

    //         if ($classeId && $sequenceId) {
    //             $effectif = Inscription::where('classe_id', $classeId)->count();

    //             // On récupère les matières de la classe
    //             $matieres = DB::table('classe_matiere')
    //                 ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
    //                 ->where('classe_id', $classeId)
    //                 ->select('matieres.id', 'matieres.nom')
    //                 ->get();

    //             foreach ($matieres as $matiere) {
    //                 // On cherche l'évaluation correspondante
    //                 $evaluation = Evaluation::where([
    //                     'classe_id' => $classeId,
    //                     'matiere_id' => $matiere->id,
    //                     'sequence_id' => $sequenceId
    //                 ])->first();

    //                 $nbNotes = $evaluation ? Note::where('evaluation_id', $evaluation->id)->count() : 0;

    //                 $auditData[] = [
    //                     'matiere' => $matiere->nom,
    //                     'saisies' => $nbNotes,
    //                     'attendu' => $effectif,
    //                     'pourcentage' => $effectif > 0 ? round(($nbNotes / $effectif) * 100) : 0
    //                 ];
    //             }
    //         }

    //         return view('pages.admin.audit-saisie', compact('classes', 'sequences', 'auditData'));
    //     }

    // 1. Déclare la propriété
    protected $scolarite;

    // 2. Injecte le service via le constructeur
    public function __construct(ScolariteService $scolarite)
    {
        $this->scolarite = $scolarite;
    }


    public function index(Request $request)
    {
        $anneeActive = $this->scolarite->getAnneeActive();

        // Groupement par niveau pour la clarté
        $niveaux = Niveau::with('classes')->get();

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
            // $matieres = DB::table('classe_matiere')
            //     ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
            //     ->leftJoin('affectations', function($join) use ($classeId) {
            //         $join->on('matieres.id', '=', 'affectations.matiere_id')
            //              ->where('affectations.classe_id', '=', $classeId);
            //     })
            //     ->leftJoin('users', 'affectations.user_id', '=', 'users.id')
            //     ->where('classe_matiere.classe_id', $classeId)
            //     ->select('matieres.id', 'matieres.nom', 'users.name as enseignant')
            //     ->get();

            // Récupération des matières + l'enseignant affecté (Corrigé avec enseignant_id)
            $matieres = DB::table('classe_matiere')
                ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
                ->leftJoin('affectations', function ($join) use ($classeId) {
                    $join->on('matieres.id', '=', 'affectations.matiere_id')
                        ->where('affectations.classe_id', '=', $classeId);
                })
                // Ici on utilise le bon nom de colonne : enseignant_id
                ->leftJoin('users', 'affectations.enseignant_id', '=', 'users.id')
                ->where('classe_matiere.classe_id', $classeId)
                ->select('matieres.id', 'matieres.nom', 'users.name as enseignant')
                ->get();


            foreach ($matieres as $matiere) {
                $evaluation = Evaluation::where([
                    'classe_id' => $classeId,
                    'matiere_id' => $matiere->id,
                    'sequence_id' => $sequenceId
                ])->first();

                $nbNotes = $evaluation ? Note::where('evaluation_id', $evaluation->id)->count() : 0;

                $auditData[] = [
                    'matiere' => $matiere->nom,
                    'enseignant' => $matiere->enseignant ?? 'Non affecté',
                    'saisies' => $nbNotes,
                    'attendu' => $effectif,
                    'pourcentage' => $effectif > 0 ? round(($nbNotes / $effectif) * 100) : 0
                ];
            }
        }

        return view('pages.admin.audit-saisie', compact('niveaux', 'sequences', 'auditData'));
    }
}
