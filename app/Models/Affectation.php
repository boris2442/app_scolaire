<?php

namespace App\Models;

use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Matiere;
use App\Models\Niveau;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Affectation extends Model
{
    protected $fillable = [
        'enseignant_id',
        'matiere_id',
        'classe_id',
        'niveau_id',
        'annee_scolaire_id'
    ];
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
    /**
     * Une affectation appartient à un niveau (6e, 5e, etc.)
     */
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class, 'niveau_id');
    }
}
