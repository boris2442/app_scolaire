<?php

namespace App\Models;

use App\Models\Matiere;
use Illuminate\Database\Eloquent\Model;

class GroupeMatiere extends Model
{

    // Ajoute cette ligne pour forcer Laravel à utiliser le bon nom de table
    protected $table = 'groupes_matieres';

    // ... le reste de ton code (fillable, relations, etc.)
    protected $fillable = ['nom', 'ordre'];
    public function matieres()
    {
        return $this->hasMany(Matiere::class, 'groupe_matiere_id');
    }
}
