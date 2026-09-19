<?php

namespace App\Models;

use App\Models\GroupeSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Matiere extends Model
{
    use SoftDeletes;
protected $fillable = ['nom', 'code', 'groupe_matiere_id'];

// Relation : Une matière peut être dans plusieurs classes
public function classes()
{
    return $this->belongsToMany(Classe::class)->withPivot('coefficient', 'ordre');
}

public function groupeMatiere() {
    return $this->belongsTo(GroupeSubject::class, 'groupe_matiere_id');
}
}
