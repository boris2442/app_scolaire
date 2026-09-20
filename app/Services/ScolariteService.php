<?php

namespace App\Services;

use App\Exceptions\IncoherentScolariteException;
use App\Models\Year;
use App\Models\Inscription;
use App\Models\Sequence;
use App\Models\Trimestre;
use Illuminate\Support\Facades\Cache;

class ScolariteService
{
    /**
   
     */
    public function getactifYear()
    {
        return Year::where('est_active', true)->first()
            ?? abort(500, "Aucune année scolaire active n'est définie dans le système.");
    }

    /**
     * Récupère la classe actuelle d'un élève pour l'année active.
     */
    public function getClasseActuelle($studentId)
    {
        $annee = $this->getactifYear();

        return Inscription::where('eleve_id', $studentId)
            ->where('annee_scolaire_id', $annee->id)
            ->first();
    }




    /**
     * Vérifie qu'un trimestre appartient bien à l'année scolaire active (ou spécifiée)
     */
    public function validateTrimestre(Trimestre $trimester, ?AnneeScolaire $actifYear = null): void
    {
        $actifYear = $actifYear ?? $this->getactifYear();

        if ((int) $trimester->annee_scolaire_id !== (int) $actifYear->id) {
            throw new IncoherentScolariteException("Le trimestre '{$trimester->nom}' n'appartient pas à l'année scolaire en cours.");
        }
    }

    /**
     * Vérifie qu'une séquence appartient bien au trimestre spécifié
     */
  /**
 * Vérifie qu'une séquence appartient au trimestre spécifié (ou à son trimestre parent)
 */
public function validateSequence(Sequence $sequence, ?Trimestre $trimester = null): void
{
    // Si aucun trimestre n'est fourni, on prend le trimestre associé à la séquence
    $trimester = $trimester ?? $sequence->trimestre;

    if (!$trimester) {
        throw new IncoherentScolariteException("Aucun trimestre valide n'est associé à cette séquence.");
    }

    // 1. Valider le trimestre par rapport à l'année active
    $this->validateTrimestre($trimester);

    // 2. Valider l'appartenance de la séquence au trimestre
    if ((int) $sequence->trimestre_id !== (int) $trimester->id) {
        throw new IncoherentScolariteException("La séquence '{$sequence->nom}' n'appartient pas au trimestre '{$trimester->nom}'.");
    }
}
}
