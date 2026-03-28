<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cycle;
use App\Models\Classe;

class Niveau extends Model
{
    protected $fillable = ['nom', 'cycle_id'];

    public function cycle()
    {
        return $this->belongsTo(Cycle::class);
    }

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }


    public function matieres()
{
    // On précise : Modèle lié, Table pivot, Clé du modèle actuel, Clé du modèle lié
    return $this->belongsToMany(Matiere::class, 'classe_matiere', 'classe_id', 'matiere_id')
                ->withPivot('coefficient', 'ordre')
                ->withTimestamps();
}
}
