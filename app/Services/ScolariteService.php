<?php

namespace App\Services;

use App\Exceptions\IncoherentScolariteException;
use App\Models\AnneeScolaire;
use App\Models\Inscription;
use App\Models\Sequence;
use App\Models\Trimestre;
use Illuminate\Support\Facades\Cache;

class ScolariteService
{
    /**
   
     */
    public function getAnneeActive()
    {
        return AnneeScolaire::where('est_active', true)->first()
            ?? abort(500, "Aucune année scolaire active n'est définie dans le système.");
    }

    /**
     * Récupère la classe actuelle d'un élève pour l'année active.
     */
    public function getClasseActuelle($eleveId)
    {
        $annee = $this->getAnneeActive();

        return Inscription::where('eleve_id', $eleveId)
            ->where('annee_scolaire_id', $annee->id)
            ->first();
    }




    /**
     * Vérifie qu'un trimestre appartient bien à l'année scolaire active (ou spécifiée)
     */
    public function validateTrimestre(Trimestre $trimestre, ?AnneeScolaire $anneeActive = null): void
    {
        $anneeActive = $anneeActive ?? $this->getAnneeActive();

        if ((int) $trimestre->annee_scolaire_id !== (int) $anneeActive->id) {
            throw new IncoherentScolariteException("Le trimestre '{$trimestre->nom}' n'appartient pas à l'année scolaire en cours.");
        }
    }

    /**
     * Vérifie qu'une séquence appartient bien au trimestre spécifié
     */
  /**
 * Vérifie qu'une séquence appartient au trimestre spécifié (ou à son trimestre parent)
 */
public function validateSequence(Sequence $sequence, ?Trimestre $trimestre = null): void
{
    // Si aucun trimestre n'est fourni, on prend le trimestre associé à la séquence
    $trimestre = $trimestre ?? $sequence->trimestre;

    if (!$trimestre) {
        throw new IncoherentScolariteException("Aucun trimestre valide n'est associé à cette séquence.");
    }

    // 1. Valider le trimestre par rapport à l'année active
    $this->validateTrimestre($trimestre);

    // 2. Valider l'appartenance de la séquence au trimestre
    if ((int) $sequence->trimestre_id !== (int) $trimestre->id) {
        throw new IncoherentScolariteException("La séquence '{$sequence->nom}' n'appartient pas au trimestre '{$trimestre->nom}'.");
    }
}
}
