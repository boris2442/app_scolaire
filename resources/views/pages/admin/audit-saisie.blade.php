@extends('layouts.admin.admin-layout')

@section('content')
    <div class="p-4 max-w-5xl mx-auto bg-background text-foreground min-h-screen">
        <h1 class="text-xl font-bold mb-4 text-foreground">Audit de Remplissage</h1>
        {{-- petite expliation de la page --}}
        <p class="text-sm italic opacity-70 mb-6 text-foreground">
            Cet audit vous permet de vérifier l'état d'avancement de la saisie des notes par les enseignants pour une classe
            et une période données. Vous pouvez ainsi identifier rapidement les matières pour lesquelles la saisie est
            complète ou encore en cours.
        </p>
        <form method="GET"
            class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6 bg-card p-3 rounded-lg border border-border shadow-sm">
            <select name="classe_id" class="text-sm bg-background border-input text-foreground rounded-md focus:ring-ring"
                required>
                <option value="">Sélectionner la Classe</option>
                @foreach ($classes as $classe)
                    <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                        {{ $classe->nom }}

                    </option>
                @endforeach


            </select>

            <select name="sequence_id" class="text-sm bg-background border-input text-foreground rounded-md focus:ring-ring"
                required>
                <option value="">Période (Séquence)</option>
                @foreach ($sequences as $s)
                    <option value="{{ $s->id }}" {{ request('sequence_id') == $s->id ? 'selected' : '' }}>
                        {{ $s->nom }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="bg-primary text-primary-foreground text-sm font-medium py-2 rounded-md hover:opacity-90 transition shadow-sm">


                <x-lucide-search class="w-4 h-4 inline-block mr-1" />
                Vérifier l'état
            </button>
        </form>

       @if (!empty($auditData))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach ($auditData as $data)
            <div
                class="bg-card text-card-foreground rounded-xl border border-border shadow-sm p-4
                       hover:shadow-md hover:border-primary/30 transition-all duration-200">

                {{-- Matière + enseignant --}}
                <div class="mb-4">
                    <div class="font-bold text-foreground text-sm">
                        {{ $data['matiere'] }}
                    </div>

                    <div class="text-xs text-muted-foreground italic mt-1">
                        Par : Mr/ Mme {{ $data['enseignant'] }}
                    </div>

                    <div class="text-xs text-muted-foreground italic mt-0.5">
                        Téléphone : {{ $data['phone'] }}
                    </div>
                </div>

                {{-- Progression --}}
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-semibold  tracking-wider text-muted-foreground">
                            Progression
                        </span>

                        <span class="text-[11px] font-mono font-bold text-foreground">
                            {{ $data['pourcentage'] }}%
                        </span>
                    </div>

                    <div class="w-full bg-secondary rounded-full h-2 overflow-hidden">
                        <div
                            class="h-2 rounded-full bg-primary transition-all duration-500"
                            style="width: {{ $data['pourcentage'] }}%">
                        </div>
                    </div>
                </div>

                {{-- Statut --}}
                <div>
                    @if ($data['pourcentage'] == 100)
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1
                                   bg-success/10 text-success rounded-full
                                   text-[10px] font-bold border border-success/20">

                            <x-lucide-check-circle class="w-3 h-3" />

                            Pret
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1
                                   bg-warning/10 text-warning rounded-full
                                   text-[10px] font-bold border border-warning/20">

                            <x-lucide-clock class="w-3 h-3" />

                            Incomplet
                        </span>
                    @endif
                </div>

            </div>
        @endforeach
    </div>
@endif
    </div>
@endsection
