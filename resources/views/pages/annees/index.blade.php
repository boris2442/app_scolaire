@extends('layouts.admin.admin-layout')

@section('content')
    {{-- 1. Cartes de statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-card p-4 rounded-xl border border-border flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 bg-primary/10 text-primary rounded-lg flex items-center justify-center text-xl">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <p class="text-[10px] text-muted-foreground uppercase font-bold tracking-wider">Total Années</p>
                <h3 class="text-xl font-black text-foreground">{{ $totalAnnees }}</h3>
            </div>
        </div>

        <div class="bg-card p-4 rounded-xl border border-border flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 bg-success/10 text-success rounded-lg flex items-center justify-center text-xl">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <p class="text-[10px] text-muted-foreground uppercase font-bold tracking-wider">Année en cours</p>
                <h3 class="text-sm font-bold text-foreground">
                    {{ $anneeActive ? $anneeActive->libelle : 'Aucune' }}
                </h3>
            </div>
        </div>
    </div>

    {{-- 2. Grille principale --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Formulaire à gauche --}}
        <div class="bg-card p-6 rounded-xl border border-border h-fit">
            <h2 class="text-sm font-bold uppercase mb-4">Nouvelle Année</h2>
            <form action="{{ route('settings.annees.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-medium">Libellé (ex: 2025-2026)</label>
                    <input type="text" name="libelle" value="{{ old('libelle') }}"
                        class="w-full bg-secondary rounded-lg text-sm px-3 py-2 border @error('libelle') border-danger @else border-border @enderror">
                    @error('libelle')
                        <p class="text-[10px] text-danger mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-xs font-medium">Début</label>
                        <input type="date" name="date_debut" value="{{ old('date_debut') }}"
                            class="w-full bg-secondary rounded-lg text-sm px-3 py-2 border @error('date_debut') border-danger @else border-border @enderror">
                        @error('date_debut')
                            <p class="text-[10px] text-danger mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium">Fin</label>
                        <input type="date" name="date_fin" value="{{ old('date_fin') }}"
                            class="w-full bg-secondary rounded-lg text-sm px-3 py-2 border @error('date_fin') border-danger @else border-border @enderror">
                        @error('date_fin')
                            <p class="text-[10px] text-danger mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button class="w-full bg-primary text-white text-sm font-bold py-2 rounded-lg hover:opacity-90 transition-all">
                    <i class="fas fa-plus mr-1"></i> Créer l'année
                </button>
            </form>
        </div>

        {{-- Liste des années à droite --}}
        <div class="lg:col-span-2 space-y-3">
            @foreach ($annees as $annee)
                <div class="bg-card p-4 rounded-xl border {{ $annee->est_active ? 'border-primary' : 'border-border' }} shadow-sm">
                    {{-- Ligne principale --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full {{ $annee->est_active ? 'bg-primary text-white' : 'bg-secondary text-gray-400' }} flex items-center justify-center">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-foreground">{{ $annee->libelle }}</h3>
                                <p class="text-[10px] text-muted-foreground uppercase">
                                    Du {{ $annee->date_debut->format('d/m/Y') }} au {{ $annee->date_fin->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if (!$annee->est_active)
                                <form action="{{ route('settings.annees.active', $annee) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="text-[10px] font-bold uppercase px-3 py-1.5 rounded bg-success/10 text-success border border-success/20 hover:bg-success hover:text-white transition-all">
                                        Activer
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] font-bold uppercase px-3 py-1.5 rounded bg-primary text-white">Actuelle</span>
                            @endif

                            <a href="{{ route('settings.annees.edit', $annee) }}" class="p-2 text-gray-400 hover:text-primary transition-colors">
                                <i class="fas fa-edit"></i>
                            </a>

                            @if (!$annee->est_active)
                                <form action="{{ route('settings.annees.destroy', $annee) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-400 hover:text-danger p-2"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- STRUCTURE PÉDAGOGIQUE (DÉPLACÉ ICI À L'INTÉRIEUR DU FOREACH) --}}
                    <div class="pt-3 border-t border-border/50">
                        <p class="text-[9px] font-bold uppercase text-muted-foreground mb-2 tracking-tight">Périodes :</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($annee->trimestres as $trim)
                                <div class="group relative">
                                    <span class="px-2 py-0.5 bg-secondary text-primary rounded text-[9px] font-bold border border-border cursor-help">
                                        {{ $trim->nom }}
                                    </span>
                                    {{-- Tooltip sequences --}}
                                    <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block bg-card border border-border p-2 rounded shadow-2xl w-32 z-[60]">
                                        @foreach ($trim->sequences as $seq)
                                            <p class="text-[9px] text-foreground border-b border-border/50 last:border-0 py-1 italic">
                                                <i class="fas fa-caret-right mr-1 text-primary"></i>{{ $seq->nom }}
                                            </p>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection
