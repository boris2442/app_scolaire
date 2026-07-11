<?php

namespace App\Models;

use App\Models\AnneeScolaire;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Niveau;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Classe extends Model
{
    protected $fillable = ['nom', 'niveau_id', 'annee_scolaire_id'];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }


    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'classe_matiere')
            ->withPivot('coefficient', 'ordre')
            ->withTimestamps();
    }


    // Petit "Accessor" pratique pour afficher le nom complet partout
    public function getNomCompletAttribute()
    {
        return $this->niveau->nom . ' ' . $this->nom;
    }

    /**
     * Récupérer toutes les inscriptions pour cette classe.
     */
    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }







    // Dans App\Models\Classe.php

    public function parametres()
    {
        return $this->hasMany(ParametreAcademique::class);
    }

    // Accessor pour simplifier l'affichage dans la vue
    public function getMoyenneMinAttribute()
    {
        $regle = $this->parametres->where('cle', 'moyenne_min')->first();
        return $regle ? $regle->valeur : 10; // 10 par défaut
    }
}
