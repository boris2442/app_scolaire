<?php

namespace App\Models;

use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Sequence;
use App\Models\Trimestre;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Moyenne extends Model
{
  protected $fillable = [
        'inscription_id',
        'matiere_id',
        'sequence_id',
        'trimestre_id',
        'valeur',
        'coefficient',
        'total_points',
        'rang',
        'moyenne_classe',
        'min_classe',
        'max_classe',
        'appreciation'
    ];

    // Relations essentielles
    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(Sequence::class);
    }

    public function trimestre(): BelongsTo
    {
        return $this->belongsTo(Trimestre::class);
    }

    /**
     * Boot method : On calcule le total des points automatiquement 
     * à chaque création ou mise à jour.
     */
    protected static function booted()
    {
        static::saving(function ($moyenne) {
            $moyenne->total_points = $moyenne->valeur * $moyenne->coefficient;
        });
    }
}
