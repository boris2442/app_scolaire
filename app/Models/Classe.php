<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Classe extends Model
{
    protected $fillable = ['nom', 'niveau_id', 'annee_scolaire_id'];

    public function niveau() {
        return $this->belongsTo(Niveau::class);
    }

    public function anneeScolaire() {
        return $this->belongsTo(AnneeScolaire::class);
    }


public function matieres()
{
    return $this->belongsToMany(Matiere::class, 'classe_matiere')
                ->withPivot('coefficient', 'ordre')
                ->withTimestamps();
}

    
    // Petit "Accessor" pratique pour afficher le nom complet partout
    public function getNomCompletAttribute() {
        return $this->niveau->nom . ' ' . $this->nom;
    }
}

