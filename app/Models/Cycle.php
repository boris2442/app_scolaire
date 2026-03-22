<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Niveau;

class Cycle extends Model
{
    protected $fillable = ['nom'];
public function niveaux() {
    return $this->hasMany(Niveau::class);
}
}
