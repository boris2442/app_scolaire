<?php

namespace App\Models;

use App\Models\Seance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jour extends Model
{
  protected $fillable = ['nom', 'ordre'];

    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }
}
