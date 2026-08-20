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
            <div class="bg-card rounded-xl border border-border shadow-sm overflow-auto text-card-foreground">
                <table class="w-full text-sm min-w-[680px]">
                    <thead class="bg-secondary/50 text-secondary-foreground uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-left">Matière & Enseignant</th>
                            <th class="px-4 py-3 font-semibold text-center w-1/3">Progression</th>
                            <th class="px-4 py-3 font-semibold text-right">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($auditData as $data)
                            <tr class="hover:bg-secondary/30 transition">
                                <td class="px-4 py-4">
                                    <div class="font-bold text-foreground">{{ $data['matiere'] }}</div>
                                    <div class="text-xs opacity-60 italic">Par : Mr/ Mme {{ $data['enseignant'] }}</div>
                                    <div class="text-xs opacity-60 italic">Téléphone : {{ $data['phone'] }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 bg-secondary rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full bg-primary transition-all duration-500"
                                                style="width: {{ $data['pourcentage'] }}%">
                                            </div>
                                        </div>

                                        <span class="text-[11px] font-mono font-bold w-10 text-right">
                                            {{ $data['pourcentage'] }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    @if ($data['pourcentage'] == 100)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-success/10 text-success rounded-full text-[10px] font-bold border border-success/20">

                                            <x-lucide-check-circle class="w-3 h-3" />
                                            PRÊT
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-warning/10 text-warning rounded-full text-[10px] font-bold border border-warning/20">

                                            <x-lucide-clock class="w-3 h-3" />
                                            INCOMPLET
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
