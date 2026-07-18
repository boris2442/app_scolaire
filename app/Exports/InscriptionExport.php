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
        // On récupère les inscriptions avec les relations élèves et classes
        // 1. On récupère l'année active via ton service
        $annee = $this->scolariteService->getAnneeActive();
        return Inscription::with(['eleve', 'classe', 'classe.niveau'])
            ->where('annee_scolaire_id', $annee->id)
            ->cursor()
            ->map(function ($inscription) {
                return [
                    $inscription->eleve->matricule,
                    $inscription->eleve->nom . ' ' . $inscription->eleve->prenom,
                    $inscription->eleve->sexe,
                    $inscription->eleve->date_naissance,
                    $inscription->eleve->lieu_naissance,


                    $inscription->classe->niveau->nom, // Nom du niveau
                    $inscription->classe->nom, // Nom de la classe
                    $inscription->date_inscription,
                    $inscription->statut,
                    $inscription->numero_recu,
                ];
            });
    }

    public function headings(): array
    {
        return ['Matricule', 'Nom complet', 'Sexe', 'Date de naissance', 'Lieu de naissance',  'Niveau', 'Classe', 'Date Inscription', 'Statut', 'N° Reçu'];
    }
}
