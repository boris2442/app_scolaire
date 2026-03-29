<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = ['evaluation_id', 'inscription_id', 'valeur', 'observation'];

    const APPRECIATIONS = [
        'A'   => 'Acquis',
        'ECA' => 'En Cours d\'Acquisition',
        'NA'  => 'Non Acquis',
        'A+'  => 'Expert / Excellent'
    ];
}
