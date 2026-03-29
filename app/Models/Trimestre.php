<?php

namespace App\Models;

use App\Models\AnneeScolaire;
use App\Models\Sequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trimestre extends Model
{
    protected $fillable = ['nom', 'annee_scolaire_id'];

    public function sequences()
    {
        return $this->hasMany(Sequence::class);
    }

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class, 'annee_scolaire_id');
    }
}
