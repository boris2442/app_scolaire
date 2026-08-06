@extends('layouts.admin.admin-layout')

@section('content')
    <div class="min-h-screen bg-background py-8 px-4">
        <div class="max-w-3xl mx-auto">

            <!-- En-tête -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-foreground">
                    Configuration de la saisie disciplinaire
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Sélectionnez la classe et le trimestre pour accéder à la grille de saisie.
                </p>
            </div>

            <!-- Carte -->
            <div class="bg-card text-card-foreground rounded-2xl border border-border shadow-lg overflow-hidden">

                <!-- Header -->
                <div class="bg-gradient-to-r from-primary to-blue-500 px-6 py-5">
                    <h2 class="text-xl font-semibold text-primary-foreground">
                        Paramètres de la saisie
                    </h2>
                </div>

                <!-- Body -->
                <div class="p-6">
                    <form action="{{ route('discipline.saisie') }}" method="GET" class="space-y-6">

                        <!-- Classe -->
                        {{-- <div>
                            <label class="block text-xs font-semibold text-foreground mb-2">
                                Classe
                            </label>

                            <select name="classe_id"
                                class="w-full rounded-xl border border-input bg-background
                                       px-4 py-3 text-foreground
                                       focus:outline-none focus:ring-2
                                       focus:ring-primary transition duration-200"
                                required>

                                <option value="">
                                    -- Choisir une classe --
                                </option>

                                @foreach ($niveaux as $niveau)
                                    <optgroup label="Niveau : {{ $niveau->nom }}">
                                        @foreach ($niveau->classes as $classe)
                                            <option value="{{ $classe->id }}">
                                                {{ $classe->nom }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div> --}}
<div>
        <label class="block text-xs font-semibold text-foreground mb-2">
            Classe
        </label>

        <select name="classe_id"
            class="w-full rounded-xl border border-input bg-background
                   px-4 py-3 text-foreground
                   focus:outline-none focus:ring-2
                   focus:ring-primary transition duration-200"
            required>

            <option value="">
                -- Choisir une classe --
            </option>

            {{-- On boucle directement sur les classes (en supposant que $classes est passé depuis le contrôleur) --}}
            @foreach ($classes as $classe)
                <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                    {{ $classe->nom }}
                </option>
            @endforeach
        </select>
    </div>
                        <!-- Trimestre -->
                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-2">
                                Trimestre
                            </label>

                            <select name="trimestre_id"
                                class="w-full rounded-xl border border-input bg-background
                                       px-4 py-3 text-foreground
                                       focus:outline-none focus:ring-2
                                       focus:ring-primary transition duration-200"
                                required>

                                <option value="">
                                    -- Choisir un trimestre --
                                </option>

                                @foreach ($trimestres as $trimestre)
                                    <option value="{{ $trimestre->id }}">
                                        {{ $trimestre->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Bouton -->
                        <div class="pt-4">
                            <button type="submit"
                                class="inline-flex items-center gap-2
                                       rounded bg-primary
                                       px-6 py-3
                                       text-sm font-semibold
                                       text-primary-foreground
                                       shadow-md
                                       hover:opacity-90
                                       active:scale-95
                                       transition-all duration-200">

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0
                                                    002 2h10a2 2 0 002-2V9a2 2 0
                                                    00-2-2h-2M9 5a2 2 0 002 2h2a2 2
                                                    0 002-2M9 5a2 2 0 012-2h2a2 2 0
                                                    012 2" />
                                </svg>

                                Accéder à la grille de saisie
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
