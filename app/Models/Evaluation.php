<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\Lecon;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Sequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    protected $fillable = ['titre', 'sequence_id', 'classe_id', 'matiere_id', 'enseignant_id', 'date_evaluation', 'annee_scolaire_id'];
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

    // Dans app/Models/Evaluation.php

    // public function enseignant()
    // {
    //     return $this->belongsTo(Enseignant::class);
    // }


    // Dans app/Models/Evaluation.php

    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class);
    }

    // Ajoute aussi l'annee_scolaire pour ton PDF
    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class, 'annee_scolaire_id');
    }


    public function lecons()
    {
        return $this->belongsToMany(Lecon::class, 'evaluation_lesson', 'evaluation_id', 'lecon_id');
    }
}
