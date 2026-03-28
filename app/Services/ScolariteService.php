<?php

namespace App\Services;

use App\Models\AnneeScolaire;
use App\Models\Inscription;
use Illuminate\Support\Facades\Cache;

class ScolariteService
{
    /**
     * Récupère l'année scolaire active.
     * On utilise le cache (optionnel) pour ne pas harceler la base de données.
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
}
