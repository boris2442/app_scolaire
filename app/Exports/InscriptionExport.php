<?php

namespace App\Exports;

use App\Models\Inscription;
use App\Services\ScolariteService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InscriptionExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $scolariteService;
    public function __construct(ScolariteService $scolariteService)
    {
        // Vous pouvez initialiser des paramètres ici si nécessaire
        $this->scolariteService = $scolariteService;
    }
    public function collection()
    {
        // On récupère l'année active via ton service
        $annee = $this->scolariteService->getAnneeActive();

        return Inscription::with(['eleve', 'classe', 'classe.cycle'])
            ->where('inscriptions.annee_scolaire_id', $annee->id)
            // Jointure avec la table eleves pour pouvoir trier proprement par nom et prénom
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->orderBy('eleves.nom', 'asc')
            ->orderBy('eleves.prenom', 'asc')
            ->select('inscriptions.*') // Évite les conflits de colonnes avec l'id
            ->cursor()
            ->map(function ($inscription) {
                return [
                    $inscription->eleve->matricule,
                    $inscription->eleve->nom . ' ' . $inscription->eleve->prenom,
                    $inscription->eleve->sexe,
                    $inscription->eleve->date_naissance,
                    $inscription->eleve->lieu_naissance,

                 
                    $inscription->classe->nom,                  // Nom de la classe
                    $inscription->date_inscription,
                    $inscription->statut,
           
                ];
            });
    }
    public function headings(): array
    {
        return ['Matricule', 'Nom complet', 'Sexe', 'Date de naissance', 'Lieu de naissance',  'Classe', 'Date Inscription', 'Statut', ];
    }
}
