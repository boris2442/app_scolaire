@extends('layouts.admin.admin-layout')

@section('content')
    <div class="mb-8">
        <h1 class="text-xl font-black  text-foreground tracking-tight">Affectations Pédagogiques</h1>
        <p class="text-xs text-primary font-bold  tracking-widest">
            Année Scolaire : {{ $anneeActive->libelle }}
        </p>
    </div>

    <p class="text-sm text-gray-500 mb-4">Gérez les affectations des enseignants aux matières pour chaque classe.
        Sélectionnez une classe pour voir ou modifier les affectations existantes.</p>
    <div class="bg-card p-6 rounded-2xl border border-border shadow-sm mb-8">
        <form action="{{ route('admin.affectations.index') }}" method="GET" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="text-[10px] font-black text-muted-foreground ml-1">Sélectionner une salle / classe</label>
                <select name="classe_id" onchange="this.form.submit()"
                    class="w-full px-4 py-2 rounded border border-border bg-secondary">
                    <option value="" class="text-muted-foreground dark:text-gray-900">-- Choisir une classe --
                    </option>
                    @foreach ($classes as $item)
                        <option value="{{ $item->id }}" {{ ($classeId ?? null) == $item->id ? 'selected' : '' }}>
                            {{ optional($item->cycle)->nom }} {{ $item->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- <div class="hidden md:block">
                <button type="submit"
                    class="bg-primary text-white px-6 py-3 rounded-xl font-black  text-[10px] tracking-widest hover:scale-105 transition-all">
                    Charger la liste
                </button>
            </div> --}}
        </form>
    </div>

    @if ($classeId)
        <div class="bg-card rounded-2xl border border-border shadow-sm overflow-auto">
            {{-- UN SEUL FORMULAIRE QUI ENVELOPPE TOUT --}}
            <form action="{{ route('admin.affectations.bulk-store') }}" method="POST">
                @csrf
                <input type="hidden" name="classe_id" value="{{ $classeId }}">

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-secondary/50 border-b border-border">
                            <th class="p-4 text-[10px] font-black  text-muted-foreground">Matière</th>
                            <th class="p-4 text-[10px] font-black  text-muted-foreground text-center">Code</th>
                            <th class="p-4 text-[10px] font-black  text-muted-foreground">Enseignant Responsable
                            </th>
                            <th class="p-4 text-[10px] font-black  text-muted-foreground text-right">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($matieresDuNiveau as $matiere)
                            @php $affectation = $affectationsExistantes->get($matiere->id); @endphp
                            <tr class="hover:bg-secondary/10 transition-colors">
                                <td class="p-4">
                                    <p class="text-sm font-black ">{{ $matiere->nom }}</p>
                                    <span
                                        class="text-[9px] bg-primary/10 text-primary px-2 py-0.5 rounded-full font-medium">
                                        Coeff: {{ $matiere->pivot->coefficient ?? '1' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <code
                                        class="text-[10px] font-bold bg-secondary px-2 py-1 rounded text-muted-foreground ">
                                        {{ $matiere->code }}
                                    </code>
                                </td>
                                <td class="p-4">
                                    {{-- LE NOM DU SELECT EST CRUCIAL --}}
                                    <select name="affectations[{{ $matiere->id }}]"
                                        class="bg-card p-2 rounded border border-border shadow-sm mb-2">
                                        <option value="">-- Non affecté --</option>
                                        @foreach ($enseignants as $enseignant)
                                            <option value="{{ $enseignant->id }}"
                                                {{ $affectation && $affectation->enseignant_id == $enseignant->id ? 'selected' : '' }}>
                                                {{ $enseignant->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-4 text-right">
                                    @if ($affectation)
                                        <span class="text-green-500 text-[10px] font-black  italic">Assigné</span>
                                    @else
                                        <span class="text-muted-foreground text-[10px] ">En attente</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            {{-- Ton code @empty ici --}}
                        @endforelse
                    </tbody>
                </table>

                {{-- BOUTON DE SAUVEGARDE --}}
                <div class="p-6 bg-secondary/10 border-t flex justify-end">
                    <button type="submit"
                        class="bg-primary text-white px-8 py-4 rounded font-black  text-[12px] tracking-widest hover:scale-105 transition-all shadow-xl shadow-primary/20">
          Enregistrer tout le tableau
                    </button>
                </div>
            </form>
        </div>
    @endif
@endsection
