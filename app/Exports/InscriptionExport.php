<?php

namespace App\Exports;

use App\Models\Inscription;
use App\Services\ScolariteService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InscriptionExport implements FromCollection, WithHeadings
{
    /**
     * @return Collection
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
        $annee = $this->scolariteService->getactifYear();

        return Inscription::with(['eleve', 'classe', 'classe.cycle'])
            ->where('inscriptions.annee_scolaire_id', $annee->id)
            // Jointure avec la table eleves pour trier proprement par nom et prénom
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->orderBy('eleves.nom', 'asc')
            ->orderBy('eleves.prenom', 'asc')
            ->select('inscriptions.*') // Évite les conflits d'IDs lors des requêtes
            ->cursor()
            ->map(function ($inscription) {
                return [
                    $inscription->eleve?->matricule ?? 'N/A',
                    trim(($inscription->eleve?->nom ?? '').' '.($inscription->eleve?->prenom ?? '')),
                    $inscription->eleve?->sexe ?? 'N/A',
                    $inscription->eleve?->date_naissance ?? 'N/A',
                    $inscription->eleve?->lieu_naissance ?? 'N/A',
                    $inscription->eleve?->name_father ?? 'N/A',
                    $inscription->eleve?->name_mother ?? 'N/A',
                    $inscription->eleve?->telephone_parent ?? 'N/A',

                    // Utilisation du Nullsafe operator pour éviter le crash sur classe
                    $inscription->classe?->nom ?? 'Classe non assignée',
                    $inscription->date_inscription,
                    $inscription->statut,
                    ];
                  
            });
    }

    public function headings(): array
    {
        return ['Matricule', 'Nom complet', 'Sexe', 'Date de naissance', 'Lieu de naissance',
            'Nom du pere', 'Nom de la mere', 'Contact', 'Classe', 'Date Inscription', 'Statut', ];
    }
}
