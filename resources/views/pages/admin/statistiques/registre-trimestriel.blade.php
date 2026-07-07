@extends('layouts.admin.admin-layout')

@section('content')

    <h2 class="font-semibold text-xl text-foreground leading-tight">
        {{ __('Registre Général Trimestriel') }}
    </h2>


    <div class="py-12 bg-background min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-card text-card-foreground p-6 rounded-lg shadow-sm border border-border mb-6">
                <form method="GET" action="{{ route('admin.statistiques.registre') }}"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-foreground/80 mb-1">Trimestre</label>
                        <select name="trimestre_id" required
                            class="w-full rounded-md border-border bg-background text-foreground focus:border-primary focus:ring-primary">
                            <option value="">-- Sélectionner --</option>
                            @foreach ($trimestres as $t)
                                <option value="{{ $t->id }}" {{ $trimestreId == $t->id ? 'selected' : '' }}>
                                    {{ $t->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground/80 mb-1">Classe</label>
                        <select name="classe_id" required
                            class="w-full rounded-md border-border bg-background text-foreground focus:border-primary focus:ring-primary">
                            <option value="">-- Sélectionner --</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}" {{ $classeId == $c->id ? 'selected' : '' }}>
                                    {{ $c->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full px-4 py-2 bg-primary text-primary-foreground rounded-md hover:opacity-90 font-semibold uppercase text-xs tracking-wider shadow-md transition">
                            Afficher le Registre
                        </button>
                    </div>
                </form>
            </div>

            @if ($registre)
              



                <div class="bg-card text-card-foreground rounded-lg shadow-sm border border-border overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm">
                        <thead>
                            <tr class="bg-secondary/50 text-secondary-foreground border-b border-border">
                                <th class="p-4 font-semibold sticky left-0 bg-card z-10">Élèves</th>
                                @foreach ($registre['matieres'] as $matiere)
                                    @php
                                        $coef = $registre['coefficients'][$matiere->id] ?? 1;
                                    @endphp
                                    <th class="p-4 border-l border-border text-center min-w-[150px]">
                                        <span class="block font-bold text-foreground">{{ $matiere->matiere_nom }}</span>
                                        <span class="block text-[10px] text-foreground/60 font-normal">Coef:
                                            {{ $coef }}</span>
                                        <span
                                            class="block text-[10px] text-primary font-medium italic truncate max-w-[140px]"
                                            title="{{ $matiere->prof_nom }}">
                                            {{ $matiere->prof_nom }}
                                        </span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($registre['eleves'] as $eleve)
                                <tr class="hover:bg-secondary/20 transition">
                                    <td
                                        class="p-4 font-medium sticky left-0 bg-card shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                        {{ $eleve->nom }} {{ $eleve->prenom }}
                                    </td>

                                    @foreach ($registre['matieres'] as $matiere)
                                        <td class="p-4 border-l border-border text-center font-mono text-xs">
                                            <div class="flex items-center justify-center space-x-2">
                                                @foreach ($registre['sequences'] as $index => $seq)
                                                    @php
                                                        $note =
                                                            $registre['grille'][$eleve->inscription_id][$matiere->id][
                                                                $seq->id
                                                            ] ?? null;
                                                        $numSeq = $index + 1; // Permet d'afficher S1, S2 dynamiquement
                                                    @endphp

                                                    <span class="px-1">
                                                        <span
                                                            class="text-foreground/40 text-[10px]">S{{ $numSeq }}:</span>
                                                        @if ($note !== null)
                                                            <span
                                                                class="{{ $note < 10 ? 'text-danger font-semibold' : 'text-foreground' }}">
                                                                {{ number_format($note, 2) }}
                                                            </span>
                                                        @else
                                                            <span class="text-foreground/30">--</span>
                                                        @endif
                                                    </span>

                                                    @if (!$loop->last)
                                                        <span class="text-border">|</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif($classeId && $trimestreId)
                <div class="text-center p-8 bg-card text-foreground/60 border border-border rounded-lg">
                    Aucune donnée disponible pour cette sélection.
                </div>
            @endif

        </div>
    </div>
@endsection
