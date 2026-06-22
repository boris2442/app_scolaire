<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{

protected $fillable = ['nom'];
    public function enseignants() {
    return $this->hasMany(Enseignant::class);
}
}
