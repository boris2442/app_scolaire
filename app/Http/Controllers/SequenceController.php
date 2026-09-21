<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Sequence;
use App\Services\ScolariteService;
use App\Services\SequenceCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SequenceController extends Controller
{
    protected $scolarite;

    protected $calculationService;

    public function __construct(
        ScolariteService $scolarite,
        SequenceCalculationService $calculationService
    ) {
        $this->scolarite = $scolarite;
        $this->calculationService = $calculationService;
    }

    public function index()
    {
        $actifYear = $this->scolarite->getactifYear();

        $sequences = Sequence::whereHas('trimestre', function ($query) use ($actifYear) {
            $query->where('annee_scolaire_id', $actifYear->id);
        })->with('trimestre')->get();

        return view('pages.admin.sequences.index', compact('sequences', 'actifYear'));
    }

    // public function index()
    // {
    //     $actifYear = $this->scolarite->getactifYear();

    //     $sequences = Sequence::whereHas('trimestre', function ($query) use ($actifYear) {
    //         $query->where('annee_scolaire_id', $actifYear->id);
    //     })->with('trimestre')->get();

    //     // dd([
    //     //     'ANNEE_ACTIVE' => [
    //     //         'id' => $actifYear->id,
    //     //         'libelle' => $actifYear->libelle,
    //     //     ],

    //     //     'SEQUENCES' => $sequences->map(function ($sequence) {
    //     //         return [
    //     //             'sequence_id' => $sequence->id,
    //     //             'sequence_nom' => $sequence->nom,
    //     //             'trimestre_id' => $sequence->trimestre_id,
    //     //             'trimestre_nom' => $sequence->trimestre?->nom,
    //     //             'annee_scolaire_id' => $sequence->trimestre?->annee_scolaire_id,
    //     //         ];
    //     //     })->toArray(),
    //     // ]);
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'submission_deadline' => 'nullable|date',
    //         'is_closed' => 'nullable|boolean',
    //     ]);

    //     $sequence = Sequence::findOrFail($id);

    //     $wasClosed = (bool) $sequence->is_closed;
    //     // Utilisation de $request->boolean() pour capturer correctement '1', '0', true, false
    //     $isNowClosed = $request->boolean('is_closed');

    //     $sequence->update([
    //         'submission_deadline' => $request->submission_deadline,
    //         'is_closed' => $isNowClosed,
    //     ]);

    //     // Déclenchement du calcul : si la séquence passe de ouverte à fermée
    //     // OU si elle est déjà fermée et qu'on soumet à nouveau le formulaire
    //     if ($isNowClosed) {
    //         // Récupère uniquement les IDs des classes qui possèdent des évaluations sur cette séquence
    //         $classeIds = DB::table('evaluations')
    //             ->where('sequence_id', $sequence->id)
    //             ->pluck('classe_id')
    //             ->unique();

    //         foreach ($classeIds as $classeId) {
    //             $this->calculationService->processSequenceAverages($sequence->id, $classeId);
    //         }

    //         return back()->with('success', 'Séquence fermée et moyennes calculées avec succès pour toutes les classes !');
    //     }

    //     return back()->with('success', 'Paramètres de la séquence mis à jour !');
    // }

    public function update(Request $request, $sequence)
    {
        $request->validate([
            'submission_deadline' => 'nullable|date',
            'is_closed' => 'nullable|boolean',
        ]);

        $actifYear = $this->scolarite->getactifYear();

        $sequence = Sequence::with('trimestre')->findOrFail($sequence);

        // Vérifier que la séquence appartient bien à l'année active
        $this->scolarite->validateSequence($sequence);

        $isNowClosed = $request->boolean('is_closed');

        $sequence->update([
            'submission_deadline' => $request->submission_deadline,
            'is_closed' => $isNowClosed,
        ]);

        if ($isNowClosed) {

            $classeIds = DB::table('evaluations')
                ->where('sequence_id', $sequence->id)
                ->pluck('classe_id')
                ->unique();

            foreach ($classeIds as $classeId) {
                $this->calculationService->processSequenceAverages(
                    $sequence->id,
                    $classeId
                );
            }

            return back()->with(
                'success',
                'Séquence fermée et moyennes calculées avec succès pour toutes les classes !'
            );
        }

        return back()->with(
            'success',
            'Paramètres de la séquence mis à jour !'
        );
    }

    /**
     * Action manuelle pour déclencher le calcul d'une classe spécifique
     */
    public function calculateClassAverages(Request $request, $sequenceId, $classeId)
    {
        $this->calculationService->processSequenceAverages((int) $sequenceId, (int) $classeId);

        return back()->with('success', 'Les moyennes et rangs de la classe ont été recalculés.');
    }
}
