<?php

namespace App\Models;


use App\Models\Classe;

use App\Models\Matiere;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Teacher;

class Affectation extends Model
{
    protected $fillable = [
        'enseignant_id',
        'matiere_id',
        'classe_id',
       
        'annee_scolaire_id'
    ];
    public function enseignant()
    {
        return $this->belongsTo(Teacher::class);
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
        return $this->belongsTo(Year::class, 'annee_scolaire_id');
    }

}
