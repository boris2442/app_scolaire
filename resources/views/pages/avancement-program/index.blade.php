@extends('layouts.admin.admin-layout')

@section('content')
<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- En-tête & Sélection de la classe --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-card border border-border p-6 rounded-2xl">
        <div>
            <h1 class="text-2xl font-bold text-card-foreground">Suivi de l'Avancement des Programmes</h1>
            <p class="text-sm text-card-foreground/60">Consultez le pourcentage de couverture des chapitres par matière.</p>
        </div>

        <form method="GET" action="{{ route('avancement.index') }}" class="flex items-center gap-3">
            <select name="classe_id" onchange="this.form.submit()" class="rounded-xl border border-border bg-background px-4 py-2 text-sm focus:ring-2 focus:ring-primary">
                <option value="">-- Sélectionner une classe --</option>
                @foreach ($classes as $classe)
                    <option value="{{ $classe->id }}" {{ $selectedClasseId == $classe->id ? 'selected' : '' }}>
                        {{ $classe->nom }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if (!$selectedClasseId)
        <div class="p-12 text-center bg-card border border-border rounded-2xl">
            <p class="text-card-foreground/60">Veuillez sélectionner une classe pour afficher l'état d'avancement des cours.</p>
        </div>
    @elseif (empty($progressionData))
        <div class="p-12 text-center bg-card border border-border rounded-2xl">
            <p class="text-card-foreground/60">Aucun programme/leçon renseigné pour cette classe.</p>
        </div>
    @else
        {{-- Grille des matières --}}
        <div class="grid md:grid-cols-2 gap-6">
            @foreach ($progressionData as $item)
                <div class="bg-card border border-border rounded-2xl p-5 space-y-4 shadow-sm">
                    
                    {{-- Infos Matière & Enseignant --}}
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg text-card-foreground">Matiere: {{ $item['matiere']->nom ?? 'Matière' }}</h3>
                            <p class="text-xs text-card-foreground/60">
                                Enseignant : <span class="font-medium text-card-foreground">{{ $item['enseignant']?->prenom }} {{ $item['enseignant']?->name }}</span>
                            </p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                            {{ $item['pourcentage'] >= 75 ? 'bg-emerald-500/10 text-emerald-600' : ($item['pourcentage'] >= 40 ? 'bg-amber-500/10 text-amber-600' : 'bg-rose-500/10 text-rose-600') }}">
                            {{ $item['pourcentage'] }}% Fait
                        </span>
                    </div>

                    {{-- Barre de progression --}}
                    <div class="space-y-1">
                        <div class="flex justify-between text-xs text-card-foreground/70">
                            <span>Progression</span>
                            <span>{{ $item['lecons_faites'] }} / {{ $item['total_lecons'] }} chapitres évalués</span>
                        </div>
                        <div class="w-full bg-muted rounded-full h-2.5 overflow-hidden">
                            <div class="bg-primary h-2.5 rounded-full transition-all duration-300" style="width: {{ $item['pourcentage'] }}%"></div>
                        </div>
                    </div>

                    {{-- Accordéon Détails des Leçons --}}
                    <details class="group border-t border-border pt-3">
                        <summary class="flex justify-between items-center cursor-pointer text-xs font-semibold text-primary">
                            <span>Voir le détail des chapitres</span>
                            <x-lucide-chevron-down class="w-4 h-4 transition-transform group-open:rotate-180" />
                        </summary>

                        <ul class="mt-3 space-y-2">
                            @foreach ($item['lecons'] as $lecon)
                                <li class="flex items-center gap-2 text-xs p-2 rounded-lg {{ $lecon->evaluations_exists ? 'bg-emerald-500/5 text-emerald-700' : 'bg-muted/40 text-card-foreground/60' }}">
                                    @if ($lecon->evaluations_exists)
                                        <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600 shrink-0" />
                                    @else
                                        <x-lucide-circle class="w-4 h-4 text-card-foreground/40 shrink-0" />
                                    @endif
                                    <span>
                                        <strong class="font-semibold">{{ $lecon->ordre }}.</strong> {{ $lecon->titre }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </details>

                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
