<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $table='etablissements';
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
