<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'matiere_id',
        'sequence_id',
        'classe_id',
        'date_evaluation',
        'type_evaluation',
        'affectation_id',
        'titre',
    ];
   // C'est cette fonction que Laravel cherchait et n'a pas trouvée
    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    public function sequence()
    {
        return $this->belongsTo(Sequence::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }
}
