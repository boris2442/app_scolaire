@extends('layouts.admin.admin-layout')

@section('content')
    <div class="container mx-auto bg-background text-foreground min-h-screen p-6">

        <!-- Bouton Retour -->
        <div class="mb-4">
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center gap-2 text-xs font-bold text-primary hover:opacity-85 transition-opacity bg-white/5 border border-white/10 px-3 py-2 rounded-full shadow">
                <span>←</span> Retour
            </a>
        </div>

        <!-- Titre -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-primary">
                Gestion des Leçons / Chapitres
            </h1>
            <p class="text-sm text-foreground/70 mt-1">
                Définissez la progression pédagogique pour cette matière et cette classe.
            </p>
        </div>

        <!-- Messages Flash -->
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-success bg-success/10 px-4 py-3 text-success shadow">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-danger bg-danger/10 px-4 py-3 text-danger shadow">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire d'ajout d'une leçon -->
        <div class="bg-card text-card-foreground border border-border rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-primary mb-4">
                Ajouter une nouvelle leçon
            </h2>

            <form action="{{ route('lessons.store') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf

                <!-- Champs cachés corrigés pour envoyer les bons IDs -->
                <input type="hidden" name="matiere_id" value="{{ $subjectId }}">
                <input type="hidden" name="classe_id" value="{{ $classRoomId }}">

                <!-- Ordre (Chapitre n°) -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">N° / Ordre</label>
                    <input type="number" name="ordre" value="{{ old('ordre', 1) }}" required
                        class="w-full rounded-lg border-border bg-background text-foreground focus:border-primary focus:ring-primary p-2 border">
                </div>

                <!-- Titre de la leçon -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-foreground mb-1">Titre de la leçon / Chapitre</label>
                    <input type="text" name="titre" placeholder="Ex: Chapitre 1 - Les suites numériques" required
                        class="w-full rounded-lg border-border bg-background text-foreground focus:border-primary focus:ring-primary p-2 border">
                </div>

                <!-- Bouton Ajouter -->
                <div>
                    <button type="submit"
                        class="w-full rounded-lg bg-primary text-primary-foreground py-2.5 font-semibold shadow transition hover:opacity-90">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des leçons enregistrées -->
        <div class="bg-card border border-border rounded-xl shadow-lg overflow-hidden p-6">
            <h2 class="text-xl font-semibold text-primary mb-4">
                Programme établi: ({{ $lessons->count() }} leçons)
            </h2>

            @if ($lessons->isEmpty())
                <p class="text-sm italic text-foreground/50 py-4">Aucune leçon enregistrée pour le moment pour cette
                    matière.</p>
            @else
                <div class="space-y-3">
                    @foreach ($lessons as $lesson)
                        <div class="flex items-center justify-between p-4 rounded-lg border border-border bg-secondary/30">
                            <div class="flex items-center gap-4">
                                <span
                                    class="w-2 h-2 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs italic">
                                    Ordre: {{ $lesson->ordre }}
                                </span>
                                <div>
                                    <h3 class="font-semibold text-foreground">{{ $lesson->titre }}</h3>
                                    @if ($lesson->description)
                                        <p class="text-xs text-foreground/70">{{ $lesson->description }}</p>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <!-- Statut ou actions (suppression, modification...) -->
                                <span
                                    class="text-xs px-2.5 py-1 rounded-full {{ $lesson->is_completed ? 'bg-success/20 text-success' : 'bg-warning/20 text-warning' }}">
                                    {{ $lesson->is_completed ? 'Dispensé' : 'Non dispensé' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
@endsection
