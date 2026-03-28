<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enseignant extends Model
{

    // C'est ici que ça se passe !
    protected $fillable = [
        'user_id',
        'matricule',
        'departement_id' // <--- IL DOIT ÊTRE ICI
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getFullNameAttribute()
    {
        return $this->user->name; // Ou nom + prenom selon tes colonnes users
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }
}
