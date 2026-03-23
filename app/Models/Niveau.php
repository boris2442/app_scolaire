<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cycle;
use App\Models\Classe;

class Niveau extends Model
{
    protected $fillable = ['nom', 'cycle_id'];

    public function cycle()
    {
        return $this->belongsTo(Cycle::class);
    }

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }
}
