<!-- Fichier : resources/views/admin/parametres/index.blade.php -->
@extends('layouts.admin.admin-layout')

@section('content')
    <div class="p-6">
        <!-- En-tête et Description de la fonctionnalité -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-foreground">Paramètres de Réussite Académique</h1>
            <p class="text-muted-foreground mt-2">
                Définissez ici la moyenne minimale requise pour le passage en classe supérieure.
                Ces règles seront automatiquement appliquées lors des conseils de classe pour déterminer 
                l'admissibilité des élèves par entité pédagogique.
            </p>
        </div>
        <form action="{{ route('admin.parametres-classes.store') }}" method="POST">
            @csrf
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-secondary text-secondary-foreground">
                        <th class="p-3 text-left">Niveau / Classe</th>
                        <th class="p-3 text-left">Moyenne Minimale</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($niveaux as $niveau)
                        <!-- Ligne de titre du Niveau - Respecte ta charte -->
                        <tr class="bg-muted">
                            <td colspan="2" class="p-3 font-bold">
                                Niveau : {{ $niveau->nom }}
                            </td>
                        </tr>

                        <!-- Boucle des classes -->
                        @foreach ($niveau->classes as $classe)
                            <tr class="border-b">
                                <td class="p-3 pl-8">{{ $classe->nom }}</td>
                                <td class="p-3">
                                    <input type="number" step="0.5" name="moyennes[{{ $classe->id }}]"
                                        value="{{ $classe->moyenne_min ?? 10 }}" class="border p-2 rounded bg-background">
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                <button type="submit" class="bg-primary text-primary-foreground px-6 py-2 rounded hover:opacity-90">
                    Enregistrer les règles
                </button>
            </div>
        </form>
    </div>
    @endsection
