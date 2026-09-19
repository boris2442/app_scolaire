<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\Lesson;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Sequence;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{

protected $table='evaluations';
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
        return $this->hasMany(Note::class, 'evaluation_id');
    }

    // Dans app/Models/Evaluation.php

    // public function enseignant()
    // {
    //     return $this->belongsTo(Enseignant::class);
    // }


    // Dans app/Models/Evaluation.php

    public function enseignant()
    {
        return $this->belongsTo(Teacher::class);
    }

    // Ajoute aussi l'annee_scolaire pour ton PDF
    public function anneeScolaire()
    {
        return $this->belongsTo(Year::class, 'annee_scolaire_id');
    }


    public function lecons()
    {
        return $this->belongsToMany(Lesson::class, 'evaluation_lesson', 'evaluation_id', 'lecon_id');
    }
}
