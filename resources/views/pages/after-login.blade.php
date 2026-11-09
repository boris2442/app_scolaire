@extends('layouts.admin.admin-layout')

@section('content')
    <div class="space-y-6">
        <!-- Carte de Bienvenue Massante -->
        <div class="relative overflow-hidden rounded-2xl bg-card border border-border p-8 md:p-12 shadow-sm">
    <!-- Effet décoratif subtil -->
    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>

    <div class="relative z-10">
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-foreground">
            <i class='text-2xl not-italic'>
                <span>Hey {{ auth()->user()->name }}</span>👋🙂...
            </i>
            <br />
            Bienvenue au <br>
            <span class="text-primary">{{ $school->nom ?? 'votre établissement' }}</span>
        </h1>

        <p class="mt-6 text-lg md:text-xl text-muted-foreground max-w-2xl">
            Système de gestion académique. 
            Vous travaillez actuellement sur l'année scolaire <span class="font-bold text-foreground">{{ $year->libelle ?? 'Non définie' }}</span>.
        </p>

        {{-- Actions rapides / Liens principaux --}}
        <div class="mt-8 flex flex-wrap items-center gap-4">
            {{-- Lien 1 : Évaluer les élèves --}}
            <a href="{{ route('admin.evaluations.index') }}" 
               class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-primary text-primary-foreground font-semibold text-sm shadow-sm hover:opacity-90 transition-all">
                <x-lucide-award class="w-4 h-4" />
                Évaluez vos élèves
            </a>

            {{-- Lien 2 : Vérifier la progression --}}
            <a href="{{ route('enseignant.dashboard') }}"
               class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-secondary text-secondary-foreground font-semibold text-sm border border-border hover:bg-muted transition-all">
                <x-lucide-trending-up class="w-4 h-4" />
                Vérifier votre progression
            </a>
        </div>
    </div>
</div>


    </div>
@endsection
