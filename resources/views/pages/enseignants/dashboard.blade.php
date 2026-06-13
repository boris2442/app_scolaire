@extends('layouts.admin.admin-layout')

@section('content')
    <div class="p-4 md:p-6 space-y-4 bg-background min-h-screen text-foreground">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-foreground">Tableau de Bord Enseignant</h1>
                <p class="text-sm italic opacity-70 text-foreground">
                    Suivez l'avancement de vos saisies de notes par classe et matière. Identifiez rapidement les classes
                    nécessitant une attention particulière pour assurer une saisie complète et à jour.
                </p>
            </div>

        </div>

        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-card p-4 rounded-2xl border border-border shadow-sm">
            <div>
                <h1 class="text-lg font-black tracking-tight">Dashboard Enseignant</h1>
                <p class="text-[10px] text-foreground/50 uppercase tracking-widest font-bold">Session Active:
                    {{-- <p class="text-sm font-medium"> --}}
                    {{ \Carbon\Carbon::parse($anneeActive->date_debut)->format('d/m/Y') }}
                    -
                    {{ \Carbon\Carbon::parse($anneeActive->date_fin)->format('d/m/Y') }}
                    {{-- </p> --}}
            </div>
          
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-card border border-border p-3 rounded-2xl">
                <p class="text-[9px] uppercase font-bold text-foreground/40 tracking-wider">Cours</p>
                <h3 class="text-xl font-black text-primary">{{ $statsSaisie->count() }}</h3>
            </div>
            <div class="bg-card border border-border p-3 rounded-2xl">
                <p class="text-[9px] uppercase font-bold text-foreground/40 tracking-wider">Progression Moy.</p>
                <h3 class="text-xl font-black text-success">{{ round($statsSaisie->avg('pourcentage')) }}%</h3>
            </div>
        </div>

        <div class="space-y-3">
            <h2 class="text-[10px] font-black uppercase tracking-widest text-foreground/30 px-1">Suivi des saisies par
                classe</h2>

            <div class="grid grid-cols-1 gap-2">
                @foreach ($statsSaisie as $stat)
                    <div
                        class="bg-card border border-border hover:border-primary/50 rounded-2xl p-3 transition-all group shadow-sm">
                        <div class="flex flex-col md:flex-row md:items-center gap-4">

                            <div class="flex items-center gap-3 min-w-[200px]">
                                <div
                                    class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-black text-xs">
                                    {{ substr($stat['classe'], 0, 2) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <h4 class="text-sm font-bold tracking-tight">{{ $stat['classe'] }}</h4>
                                        <span
                                            class="text-[8px] px-1.5 py-0.5 rounded-md bg-secondary text-secondary-foreground border border-border font-bold uppercase">
                                            {{ $stat['niveau'] }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-foreground/60 font-medium uppercase">{{ $stat['matiere'] }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-[8px] font-bold text-foreground/30 uppercase">Progression</span>
                                    <span class="text-[10px] font-black text-foreground/80">{{ $stat['saisies'] }} /
                                        {{ $stat['total'] }}</span>
                                </div>
                                <div class="h-1 w-full bg-secondary rounded-full overflow-hidden">
                                    <div class="h-full bg-primary transition-all duration-700"
                                        style="width: {{ $stat['pourcentage'] }}%"></div>
                                </div>
                                @if ($stat['derniere_date'])
                                    <p class="text-[8px] text-foreground/30 mt-1 italic">
                                        Dernière saisie : {{ $stat['derniere_date'] }}
                                    </p>
                                @endif
                            </div>

                            {{-- <div class="flex items-center justify-end">
                        <a href="{{ $stat['evaluation_id'] ? route('admin.evaluations.saisie', $stat['evaluation_id']) : '#' }}" 
                           class="w-full md:w-auto px-4 py-2 rounded-xl bg-primary text-primary-foreground font-black text-[9px] uppercase tracking-tighter hover:opacity-90 transition-all text-center">
                            Saisir
                        </a>
                    </div> --}}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
