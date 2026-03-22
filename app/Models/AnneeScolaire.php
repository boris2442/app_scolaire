<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnneeScolaire extends Model
{
    protected $fillable = ['libelle', 'date_debut', 'date_fin', 'est_active'];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'est_active' => 'boolean',
    ];

    // Méthode pour activer cette année et désactiver les autres
    public function activer()
    {
        self::where('id', '!=', $this->id)->update(['est_active' => false]);
        $this->update(['est_active' => true]);
    }
}
