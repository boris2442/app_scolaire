<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecon extends Model
{
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
        return $this->belongsToMany(Evaluation::class, 'evaluation_lesson', 'lecon_id', 'evaluation_id');
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
