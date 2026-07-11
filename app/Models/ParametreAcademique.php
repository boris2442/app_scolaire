<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametreAcademique extends Model
{
    protected $table = 'parametres_academiques';
    protected $fillable = [
        'classe_id',
        'niveau_id',
        'annee_scolaire_id',
        'cle',
        'valeur'
    ];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }
    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }
}
