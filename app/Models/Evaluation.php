<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Sequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    protected $fillable = ['titre', 'sequence_id', 'classe_id', 'matiere_id', 'enseignant_id', 'date_evaluation'];
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
    /**
     * Une évaluation possède plusieurs notes.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
