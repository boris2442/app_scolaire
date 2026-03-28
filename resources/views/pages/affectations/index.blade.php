@extends('layouts.admin.admin-layout')

@section('content')
    <div class="mb-8">
        <h1 class="text-xl font-black uppercase text-foreground tracking-tight">Affectations Pédagogiques</h1>
        <p class="text-xs text-primary font-bold uppercase tracking-widest">
            Année Scolaire : {{ $anneeActive->libelle }}
        </p>
    </div>

    <div class="bg-card p-6 rounded-2xl border border-border shadow-sm mb-8">
        <form action="{{ route('admin.affectations.index') }}" method="GET" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="text-[10px] font-black uppercase text-muted-foreground ml-1">Sélectionner une salle /
                    classe</label>
                <select name="classe_id" onchange="this.form.submit()" class="...">
                    <option value="">-- Choisir une classe --</option>
                    @foreach ($classes as $item)
                        <option value="{{ $item->id }}" {{ $classeId == $item->id ? 'selected' : '' }}>
                            {{ $item->niveau->nom }} {{ $item->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="hidden md:block">
                <button type="submit"
                    class="bg-primary text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest hover:scale-105 transition-all">
                    Charger la liste
                </button>
            </div>
        </form>
    </div>

    @if ($classeId)
        <div class="bg-card rounded-2xl border border-border shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-secondary/50 border-b border-border">
                        <th class="p-4 text-[10px] font-black uppercase text-muted-foreground">Matière</th>
                        <th class="p-4 text-[10px] font-black uppercase text-muted-foreground text-center">Code</th>
                        <th class="p-4 text-[10px] font-black uppercase text-muted-foreground">Enseignant Responsable</th>
                        <th class="p-4 text-[10px] font-black uppercase text-muted-foreground text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($matieresDuNiveau as $matiere)
                        @php
                            $affectation = $affectationsExistantes->get($matiere->id);
                        @endphp
                        <tr class="hover:bg-secondary/10 transition-colors">
                            <td class="p-4">
                                <p class="text-sm font-black uppercase">{{ $matiere->nom }}</p>
                                <span class="text-[9px] bg-primary/10 text-primary px-2 py-0.5 rounded-full font-bold">
                                    Coeff: {{ $matiere->pivot->coefficient ?? '1' }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <code
                                    class="text-[10px] font-bold bg-secondary px-2 py-1 rounded text-muted-foreground uppercase">
                                    {{ $matiere->code }}
                                </code>
                            </td>
                            <form action="{{ route('admin.affectations.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="classe_id" value="{{ $classeId }}">
                                <input type="hidden" name="matiere_id" value="{{ $matiere->id }}">

                                <td class="p-4">
                                    <select name="enseignant_id" required
                                        class="w-full bg-secondary/50 border-transparent rounded-lg py-2 px-3 text-xs font-bold uppercase focus:bg-white focus:ring-1 focus:ring-primary transition-all">
                                        <option value="">-- Non affecté --</option>
                                        @foreach ($enseignants as $enseignant)
                                            <option value="{{ $enseignant->id }}"
                                                {{ $affectation && $affectation->enseignant_id == $enseignant->id ? 'selected' : '' }}>
                                                {{ $enseignant->user->name }}
                                                ({{ $enseignant->specialite ?? 'Généraliste' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-4 text-right">
                                    <button type="submit"
                                        class="p-2 {{ $affectation ? 'text-green-500' : 'text-primary' }} hover:scale-110 transition-all"
                                        title="Mettre à jour">
                                        <i class="fas {{ $affectation ? 'fa-check-double' : 'fa-save' }}"></i>
                                    </button>
                                </td>
                            </form>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-12 text-center text-muted-foreground">
                                <i class="fas fa-book-open text-4xl mb-4 opacity-20"></i>
                                <p class="text-[10px] font-black uppercase tracking-widest">Aucune matière définie pour ce
                                    niveau</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div
            class="flex flex-col items-center justify-center p-20 border-2 border-dashed border-border rounded-3xl bg-card/50">
            <div class="w-16 h-16 bg-secondary rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-chalkboard-teacher text-muted-foreground"></i>
            </div>
            <p class="text-[10px] font-black uppercase text-muted-foreground tracking-widest">Veuillez sélectionner une
                classe pour gérer les affectations</p>
        </div>
    @endif

@endsection
