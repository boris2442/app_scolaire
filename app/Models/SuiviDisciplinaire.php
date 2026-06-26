<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuiviDisciplinaire extends Model
{
    protected $fillable = [
        'inscription_id',
        'trimestre_id',
        'retards',
        'absences',
        'suspensions',
        'avertissements',
        'blames',
        'exclusions',
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    public function trimestre()
    {
        return $this->belongsTo(Trimestre::class);
    }
}
