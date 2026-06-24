<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
protected $fillable = ['nom', 'code', 'groupe_matiere_id'];

// Relation : Une matière peut être dans plusieurs classes
public function classes()
{
    return $this->belongsToMany(Classe::class)->withPivot('coefficient', 'ordre');
}

public function groupeMatiere() {
    return $this->belongsTo(GroupeMatiere::class, 'groupe_matiere_id');
}
}
