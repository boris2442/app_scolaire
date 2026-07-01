<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $fillable = ['evaluation_id', 'inscription_id', 'valeur', 'observation'];
    // Ajoute cette fonction :
    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class, 'inscription_id');
    }
    const APPRECIATIONS = [
        'A'   => 'Acquis',
        'ECA' => 'En Cours d\'Acquisition',
        'NA'  => 'Non Acquis',
        'A+'  => 'Expert / Excellent'
    ];
}
