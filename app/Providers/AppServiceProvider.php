<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
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
        // view()->composer('*', function ($view) {
        //     $ecole = Etablissement::first()
        //      ?? new Etablissement()
        //      ;
        //     $view->with('ecole', $ecole);
        // });


  $ecole = Cache::rememberForever('ecole', function () {
        return Etablissement::first() ?? new Etablissement();
    });

    View::share('ecole', $ecole);





        Gate::define('access-admin', function (User $user) {
            return $user->role === UserRole::ADMIN;
        });

        Gate::define('access-sg', function (User $user) {
            return $user->role === UserRole::SG;
        });
        Gate::define('access-enseignant', function (User $user) {
            return $user->role === UserRole::ENSEIGNANT;
        });
    }
}
