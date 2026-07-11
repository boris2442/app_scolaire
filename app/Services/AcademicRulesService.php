<?php

namespace App\Services;

use App\Models\ParametreAcademique;
use Illuminate\Support\Facades\Log;

class AcademicRulesService
{
    protected $scolariteService;

    public function __construct(ScolariteService $scolariteService)
    {
        $this->scolariteService = $scolariteService;
    }
    public function getMoyenneMin($classeId)
    {
        // On utilise ton service pour être certain d'avoir la bonne année
        $annee = $this->scolariteService->getAnneeActive();

        $regle = ParametreAcademique::where('classe_id', $classeId)
            ->where('annee_scolaire_id', $annee->id) // Verrouillage par année active
            ->where('cle', 'moyenne_min')
            ->first();
     // Au lieu de dd(), on écrit dans le log
    Log::info('DEBUG MoyenneMin:', [
        'annee_id' => $annee->id,
        'classe_id' => $classeId,
        'regle_trouvee' => $regle ? $regle->valeur : 'AUCUNE'
    ]);
        return $regle ? $regle->valeur : 10.00; // 10 par défaut
    }
}
