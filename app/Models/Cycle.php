<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cycle extends Model
{
    protected $fillable = ['nom'];
    public function classes()
    {
        return $this->hasMany(Classe::class);
    }
}
