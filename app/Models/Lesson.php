<?php

namespace App\Models;

use App\Models\Assessment;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table='lecons';
    protected $fillable = [
        'titre',
        'description',
        'matiere_id',
        'classe_id',
        'enseignant_id',
        'ordre',
    ];


    public function evaluations()
    {
        return $this->belongsToMany(Assessment::class, 'evaluation_lesson', 'lecon_id', 'evaluation_id');
    }


    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function enseignant()
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }
}
