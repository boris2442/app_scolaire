<?php

namespace App\Models;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table='departements';

protected $fillable = ['nom'];
    public function enseignants() {
    return $this->hasMany(Teacher::class);
}
}
