<?php

namespace App\Providers;

use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Partager l'objet 'ecole' avec toutes les vues
        view()->composer('*', function ($view) {
            $ecole = Etablissement::first() ?? new \App\Models\Etablissement();
            $view->with('ecole', $ecole);
        });

        // Définition de la Gate pour l'administrateur
        Gate::define('access-admin', function (User $user) {
            return $user->role->value === 'admin';
            // Ou $user->is_admin == 1; selon ta structure
        });
    }
}
