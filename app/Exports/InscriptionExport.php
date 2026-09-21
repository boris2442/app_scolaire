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
            ->map(function ($enrollment) {
                return [
                    $enrollment->eleve?->matricule ?? 'N/A',
                    trim(($enrollment->eleve?->nom ?? '').' '.($enrollment->eleve?->prenom ?? '')),
                    $enrollment->eleve?->sexe ?? 'N/A',
                    $enrollment->eleve?->date_naissance ?? 'N/A',
                    $enrollment->eleve?->lieu_naissance ?? 'N/A',
                    $enrollment->eleve?->name_father ?? 'N/A',
                    $enrollment->eleve?->name_mother ?? 'N/A',
                    $enrollment->eleve?->telephone_parent ?? 'N/A',

                    // Utilisation du Nullsafe operator pour éviter le crash sur classe
                    $enrollment->classe?->nom ?? 'Classe non assignée',
                    $enrollment->date_inscription,
                    $enrollment->statut,
                    ];
                  
            });
    }

    public function headings(): array
    {
        return ['Matricule', 'Nom complet', 'Sexe', 'Date de naissance', 'Lieu de naissance',
            'Nom du pere', 'Nom de la mere', 'Contact', 'Classe', 'Date Inscription', 'Statut', ];
    }
}
