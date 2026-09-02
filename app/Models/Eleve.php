<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- DOIT ÊTRE LÀ

class Eleve extends Model
{
    use SoftDeletes; // <--- DOIT ÊTRE LÀ
    protected $fillable = [
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'lieu_naissance',
        'telephone_parent',
        'adresse',
        'photo', // <--- Ajoute ceci
        'matricule',
        'name_father',
        'name_mother',
    ];


    /**
     * Un élève peut avoir plusieurs inscriptions (historique scolaire)
     */
    // app/Models/Eleve.php
    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }
    /**
     * Récupère les valeurs possibles de l'énumération 'sexe'
     */
    public static function getSexeOptions()
    {
        // 1. Récupérer le résultat (c'est un tableau d'objets)
        $columns = DB::select("SHOW COLUMNS FROM eleves WHERE Field = 'sexe'");

        if (empty($columns)) {
            return ['M', 'F'];
        }

        // 2. Extraire la chaîne "enum('M','F')" depuis le premier objet
        $typeStr = $columns[0]->Type;

        // 3. Analyser la chaîne avec preg_match
        preg_match('/^enum\((.*)\)$/', $typeStr, $matches);

        $values = [];
        if (isset($matches[1])) {
            foreach (explode(',', $matches[1]) as $value) {
                $values[] = trim($value, "'");
            }
        }

        return $values ?: ['M', 'F'];
    }

    // Dans app/Models/Eleve.php






    // app/Models/Eleve.php

    // Calcul de l'âge : Année Actuelle - Date de Naissance
    public function getAgeAttribute()
    {
        return \Carbon\Carbon::parse($this->date_naissance)->age;
    }

    // Récupérer la dernière inscription (Niveau + Classe/Salle)
    public function getDerniereInscriptionAttribute()
    {
        return $this->inscriptions()->latest()->first();
    }




    public static function genererEtAttribuerMatricule(self $eleve, $anneeScolaireId)
    {
        // Si l'élève a déjà un matricule (fourni par Excel par exemple), on ne touche à rien
        if (!empty($eleve->matricule)) {
            return $eleve->matricule;
        }

        // Récupérer l'année scolaire concernée
        $anneeScolaire = AnneeScolaire::find($anneeScolaireId);

        if ($anneeScolaire) {
            $debut = Carbon::parse($anneeScolaire->date_debut)->format('y');
            $fin = Carbon::parse($anneeScolaire->date_fin)->format('y');

            // Génération : 2 chiffres début + 2 chiffres fin + ID sur 5 chiffres (ex: 262600012)
            $matricule = $debut . $fin . str_pad($eleve->id, 5, '0', STR_PAD_LEFT);

            $eleve->update(['matricule' => $matricule]);

            return $matricule;
        }

        return null;
    }
}
