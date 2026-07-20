<?php

namespace App\Models;

use App\Models\Seance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Creneau extends Model
{
   protected $fillable = ['heure_debut', 'heure_fin', 'libelle'];
protected $table='creneaus';
    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }
}
