<?php

namespace App\Models;

use App\Models\Trimestre;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sequence extends Model
{
protected $fillable = ['nom', 'trimestre_id'];

/**
     * Une séquence appartient à un trimestre
     */
    public function trimestre(): BelongsTo
    {
        return $this->belongsTo(Trimestre::class, 'trimestre_id');
    }
}
