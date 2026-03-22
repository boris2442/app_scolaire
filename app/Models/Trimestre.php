<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trimestre extends Model
{
protected $fillable = ['nom', 'annee_scolaire_id'];

public function sequences() {
    return $this->hasMany(Sequence::class);
}
}
