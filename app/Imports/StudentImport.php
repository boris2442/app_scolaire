<?php

namespace App\Imports;

use App\Models\Eleve;
use App\Models\Inscription;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    // public function collection(Collection $collection)
    // {
    //     //
    // }


    protected $classeId;
    protected $anneeScolaireId;

    public function __construct($classeId, $anneeScolaireId)
    {
        $this->classeId = $classeId;
        $this->anneeScolaireId = $anneeScolaireId;
    }

    // public function collection(Collection $rows)
    // {

    //     // 1. DUMP & DIE pour voir si les lignes arrivent et à quoi elles ressemblent
    //     // (Décommente la ligne suivante pour tester)
    //    // dd($rows->toArray());
    //     $successCount = 0;
    //     $errors = [];

    //     foreach ($rows as $index => $row) {
    //         // Ignorer la ligne si le nom est vide
    //         if (!isset($row['nom']) || empty(trim($row['nom']))) {
    //             continue;
    //         }

    //         try {
    //             DB::beginTransaction();

    //             $nom = trim($row['nom']);
    //             $prenom = isset($row['prenom']) ? trim($row['prenom']) : null;

    //             // Sécurisation de la date
    //             $dateNaissance = null;
    //             if (!empty($row['date_naissance'])) {
    //                 try {
    //                     // Gérer les formats de date Excel potentiels
    //                     $dateNaissance = is_numeric($row['date_naissance'])
    //                         ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_naissance'])
    //                         : Carbon::parse($row['date_naissance']);
    //                 } catch (\Exception $e) {
    //                     throw new \Exception("Format de date invalide pour l'élève $nom $prenom.");
    //                 }
    //             }

    //             $matricule = isset($row['matricule']) && !empty(trim($row['matricule'])) ? trim($row['matricule']) : null;

    //             // 1. Recherche de l'élève
    //             $eleve = null;
    //             if ($matricule) {
    //                 $eleve = Eleve::where('matricule', $matricule)->first();
    //             } else {
    //                 $eleve = Eleve::where('nom', strtoupper($nom))
    //                     ->when($prenom, fn($q) => $q->where('prenom', $prenom))
    //                     ->when($dateNaissance, fn($q) => $q->where('date_naissance', $dateNaissance))
    //                     ->first();
    //             }

    //             // 2. Création ou mise à jour de la fiche élève
    //             if (!$eleve) {
    //                 $eleveData = [
    //                     'nom'              => strtoupper($nom),
    //                     'prenom'           => $prenom,
    //                     'sexe'             => $row['sexe'] ?? 'M',
    //                     'date_naissance'   => $dateNaissance,
    //                     'lieu_naissance'   => $row['lieu_naissance'] ?? 'Non renseigné',
    //                     'telephone_parent' => $row['telephone_parent'] ?? 'Non renseigné',
    //                     'adresse'          => $row['adresse'] ?? 'Non renseigné',
    //                     'est_actif'        => true,
    //                 ];

    //                 if ($matricule) {
    //                     $eleveData['matricule'] = $matricule;
    //                 }

    //                 $eleve = Eleve::create($eleveData);

    //                 // Génération automatique si pas de matricule
    //                 if (!$matricule && method_exists(Eleve::class, 'genererEtAttribuerMatricule')) {
    //                     Eleve::genererEtAttribuerMatricule($eleve, $this->anneeScolaireId);
    //                 }
    //             } else {
    //                 // Mettre à jour les infos manquantes si besoin
    //                 $eleve->update([
    //                     'telephone_parent' => $row['telephone_parent'] ?? $eleve->telephone_parent,
    //                 ]);
    //             }

    //             // 3. Gestion de l'inscription
    //             Inscription::updateOrCreate(
    //                 [
    //                     'eleve_id'          => $eleve->id,
    //                     'annee_scolaire_id' => $this->anneeScolaireId,
    //                 ],
    //                 [
    //                     'classe_id'         => $this->classeId,
    //                     'date_inscription'  => now(),
    //                     'statut'            => 'inscrit',
    //                     'est_redoublant'    => filter_var($row['est_redoublant'] ?? false, FILTER_VALIDATE_BOOLEAN),
    //                 ]
    //             );

    //             DB::commit();
    //             $successCount++;
    //         } catch (\Exception $e) {
    //             DB::rollBack();
    //             // On enregistre l'erreur exacte avec la ligne concernée (index + 2 pour correspondre au fichier Excel)
    //             $excelRow = $index + 2;
    //             $errors[] = "Ligne $excelRow ({$row['nom']}): " . $e->getMessage();
    //             Log::error("Erreur Import Excel - Ligne $excelRow: " . $e->getMessage());
    //         }
    //     }

    //     // Retourner ou stocker les erreurs en session pour les afficher à l'utilisateur
    //     if (count($errors) > 0) {
    //         session()->flash('import_errors', $errors);
    //     }
    // }




    public function collection(Collection $rows)
    {
        $successCount = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            // Ignorer la ligne si le nom est vide
            if (!isset($row['nom']) || empty(trim($row['nom']))) {
                continue;
            }

            try {
                DB::beginTransaction();

                $nom = trim($row['nom']);
                $prenom = isset($row['prenom']) ? trim($row['prenom']) : null;

                // Sécurisation de la date
                $dateNaissance = null;
                if (!empty($row['date_naissance'])) {
                    try {
                        $dateNaissance = is_numeric($row['date_naissance'])
                            ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_naissance'])
                            : Carbon::parse($row['date_naissance']);
                    } catch (\Exception $e) {
                        throw new \Exception("Format de date invalide pour l'élève $nom $prenom.");
                    }
                }

                // Récupération sécurisée du matricule (gère aussi la casse ou les espaces)
                $rawMatricule = $row['matricule'] ?? $row['Matricule'] ?? null;
                $matricule = !empty(trim($rawMatricule)) ? trim($rawMatricule) : null;

                // 1. Recherche de l'élève
                $eleve = null;
                if ($matricule) {
                    $eleve = Eleve::where('matricule', $matricule)->first();
                } else {
                    $eleve = Eleve::where('nom', strtoupper($nom))
                        ->when($prenom, fn($q) => $q->where('prenom', $prenom))
                        ->when($dateNaissance, fn($q) => $q->where('date_naissance', $dateNaissance))
                        ->first();
                }

                // 2. Création ou mise à jour de la fiche élève
                if (!$eleve) {
                    $eleveData = [
                        'nom'              => strtoupper($nom),
                        'prenom'           => $prenom,
                        'sexe'             => $row['sexe'] ?? 'M',
                        'date_naissance'   => $dateNaissance,
                        'lieu_naissance'   => $row['lieu_naissance'] ?? 'Non renseigné',
                        'telephone_parent' => $row['telephone_parent'] ?? 'Non renseigné',
                        'adresse'          => $row['adresse'] ?? 'Non renseigné',
                        'est_actif'        => true,
                    ];

                    // Si un matricule est présent dans Excel, on l'ajoute
                    if ($matricule) {
                        $eleveData['matricule'] = $matricule;
                    }

                    $eleve = Eleve::create($eleveData);

                    // 🔥 CORRECTION ICI : Utilisation de empty(trim(...)) pour forcer la génération si vide
                    if (empty(trim($eleve->matricule)) && method_exists(Eleve::class, 'genererEtAttribuerMatricule')) {
                        Eleve::genererEtAttribuerMatricule($eleve, $this->anneeScolaireId);
                    }
                } else {
                    // Mettre à jour les infos manquantes si besoin
                    $eleve->update([
                        'telephone_parent' => $row['telephone_parent'] ?? $eleve->telephone_parent,
                    ]);
                }

                // 3. Gestion de l'inscription
                Inscription::updateOrCreate(
                    [
                        'eleve_id'          => $eleve->id,
                        'annee_scolaire_id' => $this->anneeScolaireId,
                    ],
                    [
                        'classe_id'         => $this->classeId,
                        'date_inscription'  => now(),
                        'statut'            => 'inscrit',
                        'est_redoublant'    => filter_var($row['est_redoublant'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    ]
                );

                DB::commit();
                $successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $excelRow = $index + 2;
                $errors[] = "Ligne $excelRow ({$row['nom']}): " . $e->getMessage();
                Log::error("Erreur Import Excel - Ligne $excelRow: " . $e->getMessage());
            }
        }

        // Stocker les erreurs en session pour affichage éventuel
        if (count($errors) > 0) {
            session()->flash('import_errors', $errors);
        }
    }
}
