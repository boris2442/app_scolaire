<?php

namespace App\Models;

use App\Models\Affectation;
use App\Models\Departement;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enseignant extends Model
{

    // C'est ici que ça se passe !
    protected $fillable = [
        'user_id',

        'departement_id', // <--- IL DOIT ÊTRE ICI
        'enseignant_id',

        'matricule',
        'grade',
        'birth_date',
        'birth_place',
        'marital_status',
        'previous_position',
        'previous_school',
        'appointment_document_number',
        'appointment_date',
        'service_assumption_date',
        'quality',
        'diploma',
        'matiere_id',
        'public_service_first_date',
        'school_first_date',
        'interruption_reason',
        'interruption_start_date',
        'interruption_end_date',
        'secondary_phone',
        'address',
        'number_of_children', // <--- IL DOIT ÊTRE ICI
    ];





    public static function generateMatricule(): string
    {
        $prefix = 'ENS';
        $year = date('Y');

        // Compte le nombre d'enseignants créés l'année en cours
        $count = self::whereYear('created_at', $year)->count() + 1;

        // Formate le numéro séquentiel sur 4 chiffres (ex: 1 -> 0001)
        $sequence = str_pad($count, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}-{$sequence}";
    }







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

    public function affectations(): HasMany
    {
        // Assure-toi que la clé étrangère dans la table affectations est bien 'enseignant_id'
        return $this->hasMany(Affectation::class, 'enseignant_id');
    }


    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }
}
