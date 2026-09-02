<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class, // Magique : convertit le string en Enum
    ];


    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
               'must_change_password' => 'boolean',
        ];
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // INDISPENSABLE
        'phone',
        'avatar',
           'must_change_password',
    ];

    public function enseignant()
    {
        return $this->hasOne(Enseignant::class, 'user_id');
    }



    public function dashboardRoute(): string
    {
        return match ($this->role) {
            UserRole::ADMIN => route('admin.statistiques.index'),
            UserRole::SG => route('discipline.index'),
            UserRole::ENSEIGNANT => route('admin.evaluations.index'),
            UserRole::SECRETAIRE => route('admin.bulletins.index'),
            default => route('dashboard'),
        };
    }
}
