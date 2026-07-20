@extends('layouts.admin.admin-layout')

@section('content')
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-foreground">Emploi du temps - {{ $classe->nom }}</h1>
            <a 
            {{-- href="{{ route('emplois.imprimer', $classe->id) }}" --}}
             class="bg-primary text-primary-foreground px-4 py-2 rounded-lg hover:opacity-90">
                Imprimer PDF
            </a>
        </div>

        <!-- Grille de l'emploi du temps -->
        <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-secondary text-secondary-foreground">
                    <tr>
                        <th class="p-4">Heures</th>
                        @foreach($jours as $jour)
                            <th class="p-4">{{ $jour->nom }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($creneaux as $creneau)
                        <tr>
                            <td class="p-4 font-semibold text-primary">{{ $creneau->heure_debut }} - {{ $creneau->heure_fin }}</td>
                            @foreach($jours as $jour)
                                <td class="p-4 border-l border-border">
                                    <!-- Ici on affiche la séance si elle existe -->
                                    @php $seance = $seances->where('jour_id', $jour->id)->where('creneau_id', $creneau->id)->first(); @endphp
                                    @if($seance)
                                        <div class="bg-primary/10 p-2 rounded border border-primary/20">
                                            <p class="font-bold text-primary">{{ $seance->matiere->nom }}</p>
                                            <p class="text-xs text-foreground/70">{{ $seance->enseignant->user->name }}</p>
                                        </div>
                                    @else
                                        <span class="text-muted-foreground text-xs">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endection
