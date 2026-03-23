<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    protected $fillable = ['eleve_id', 'classe_id', 'annee_scolaire_id', 'date_inscription', 'statut'];
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    // app/Models/Inscription.php
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }
}
