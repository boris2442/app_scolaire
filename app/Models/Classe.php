<?php

namespace App\Models;

use App\Models\AnneeScolaire;
use App\Models\Inscription;
use App\Models\Matiere;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Classe extends Model
{
    protected $fillable = ['nom','cycle_id', 'annee_scolaire_id'];

public function cycle()
{
    return $this->belongsTo(Cycle::class);
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


   public function getNomCompletAttribute()
{
    return $this->nom;
}
    /**
     * Récupérer toutes les inscriptions pour cette classe.
     */
    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    // public function classes()
    // {
    //     return $this->hasMany(Classe::class);
    // }






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
