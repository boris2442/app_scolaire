<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
  use Illuminate\Database\Eloquent\Relations\HasMany;
class Eleve extends Model
{
    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'lieu_naissance',
        'telephone_parent',
        'adresse'
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
}
