<?php

namespace App\Models;

use App\Models\AnneeScolaire;
use App\Models\Enseignant;
use App\Models\Matiere;
use Illuminate\Database\Eloquent\Model;

class Affectation extends Model
{
    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class);
    }
    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }
    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }
    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }
}
