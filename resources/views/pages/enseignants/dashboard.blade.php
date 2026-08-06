@extends('layouts.admin.admin-layout')

@section('content')
    <div class="p-4 md:p-6 space-y-4 bg-background min-h-screen text-foreground">
        
        <!-- En-tête -->
       <!-- En-tête -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-card p-5 rounded-2xl border border-border shadow-md">
    <div>
        <h1 class="text-xl tracking-tight font-bold">Dashboard Enseignant</h1>
        <p class="text-xs text-foreground/60 tracking-wider font-medium mt-0.5">
            Session Active : {{ \Carbon\Carbon::parse($anneeActive->date_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($anneeActive->date_fin)->format('d/m/Y') }}
        </p>
    </div>

    <!-- Sélecteur de Séquence bien visible -->
    <form action="{{ route('enseignant.dashboard') }}" method="GET" class="flex items-center gap-3 bg-primary/10 border-2 border-primary/30 px-4 py-2.5 rounded-2xl shadow-sm hover:border-primary/50 transition">
        <div class="flex items-center gap-2">
            <!-- Petite icône calendrier/filtre pour accentuer la visibilité -->
            <x-lucide-filter class="w-4 h-4 text-primary" />
            <label for="sequence_id" class="text-xs uppercase font-extrabold text-primary tracking-wide cursor-pointer">
                Evaluation :
            </label>
        </div>

        <select name="sequence_id" id="sequence_id" onchange="this.form.submit()" 
            class="bg-background border border-border rounded-xl px-3 py-1.5 text-xs font-bold text-foreground focus:ring-2 focus:ring-primary focus:border-primary outline-none cursor-pointer shadow-inner transition">
            @foreach($sequences as $seq)
                <option value="{{ $seq->id }}" {{ $sequenceId == $seq->id ? 'selected' : '' }}>
                    {{ $seq->nom }}
                </option>
            @endforeach
        </select>
    </form>
</div>
        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-card border border-border p-3 rounded-2xl">
                <p class="text-[9px] text-foreground/40 tracking-wider">Cours</p>
                <h3 class="text-xl font-black text-primary">{{ $statsSaisie->count() }}</h3>
            </div>
            <div class="bg-card border border-border p-3 rounded-2xl">
                <p class="text-[9px] text-foreground/40 tracking-wider">Progression Moy.</p>
                <h3 class="text-xl font-black text-success">{{ round($statsSaisie->avg('pourcentage')) }}%</h3>
            </div>
        </div>

        <!-- Liste / Suivi des saisies -->
        <div class="space-y-3">
            <h2 class="text-[10px] tracking-widest text-foreground/30 px-1">Suivi des saisies par classe</h2>

            <div class="grid grid-cols-1 gap-2">
                @foreach ($statsSaisie as $stat)
                    <div class="bg-card border border-border hover:border-primary/50 rounded-2xl p-3 transition-all group shadow-sm">
                        <div class="flex flex-col md:flex-row md:items-center gap-4">

                            <div class="flex items-center gap-3 min-w-[200px]">
                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-black text-xs">
                                    {{ substr($stat['classe'], 0, 2) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <h4 class="text-sm tracking-tight">{{ $stat['classe'] }}</h4>
                                    </div>
                                    <p class="text-[10px] text-foreground/60 font-medium">{{ $stat['matiere'] }}</p>
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-[8px] font-bold text-foreground/30">Progression</span>
                                    <span class="text-[10px] font-black text-foreground/80">{{ $stat['saisies'] }} / {{ $stat['total'] }}</span>
                                </div>
                                <div class="h-1 w-full bg-secondary rounded-full overflow-hidden">
                                    <div class="h-full bg-primary transition-all duration-700" style="width: {{ $stat['pourcentage'] }}%"></div>
                                </div>
                                @if ($stat['derniere_date'])
                                    <p class="text-[8px] text-foreground/30 mt-1 italic">
                                        Dernière saisie : {{ $stat['derniere_date'] }}
                                    </p>
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
