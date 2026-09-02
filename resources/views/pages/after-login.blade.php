@extends('layouts.admin.admin-layout')

@section('content')
    <div class="space-y-6">
        <!-- Carte de Bienvenue Massante -->
        <div class="relative overflow-hidden rounded-2xl bg-card border border-border p-8 md:p-12 shadow-sm">
            <!-- Effet décoratif subtil -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-foreground"><i
                        class='text-2xl '>
                        {{-- <div class='flex gap-2 items-center'> --}}
                            <span> Hey {{ auth()->user()->name }}</span>👋🙂...
                            ;
                        {{-- </div> --}}
                    </i>
                    <br />
                    Bienvenue au <br>
                    <span class="text-primary">{{ $etablissement->nom ?? 'votre établissement' }}</span>
                </h1>

                <p class="mt-6 text-lg md:text-xl text-muted-foreground max-w-2xl">
                    Système de gestion académique.
                    Vous travaillez actuellement sur l'année scolaire <span
                        class="font-bold text-foreground">{{ $anneeActive->libelle ?? 'Non définie' }}</span>.
                </p>
            </div>
        </div>


    </div>
@endsection
