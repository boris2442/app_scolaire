<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    protected $fillable = [
        'nom',
        'adresse',
        'email',
        'telephone',
        'logo',
        'code_ecole',
        'slogan',
        'english_slogan',
        'english_name'
    ];
}
