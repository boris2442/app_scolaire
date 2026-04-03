<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bilan extends Model
{
    protected $fillable = [
        'inscription_id',
        'sequence_id',
        'annee_scolaire_id',
        'total_points',
        'total_coefs',
        'moyenne', // <--- ASSURE-TOI QUE CECI EST BIEN ICI
        'effectif_classe',
        'mention',
        'rang',
        'moyenne_premier',
        'moyenne_dernier',
        'moyenne_classe',
    ];
}
