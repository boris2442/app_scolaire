@extends('layouts.admin.admin-layout')

@section('content')
    {{-- Formulaire de Création --}}
    <div class="bg-secondary/10 border border-white/5 p-8 rounded-3xl mb-10">
        <h2 class="text-sm   mb-6 text-primary tracking-widest">Initialiser une Session de Notes</h2>

        <form action="{{ route('admin.evaluations.store') }}" method="POST"
            class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white/5 p-6 rounded-3xl border border-white/10">
            @csrf

            {{-- 1. Sélection de la Séquence --}}
            <select name="sequence_id"
                class="bg-background border-white/10 rounded-xl p-3 text-sm  outline-none focus:border-primary" required>
                <option value="">-- Période (Evaluation) --</option>
                @foreach ($sequences->groupBy('trimestre_id') as $trimestreId => $group)
                    <optgroup label="Trimestre {{ $loop->iteration }}">
                        @foreach ($group as $seq)
                            <option value="{{ $seq->id }}">{{ $seq->nom }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>

            {{-- 2. Sélection du Cours (Matière + Classe) --}}
            <select name="affectation_id"
                class="md:col-span-2 bg-background border-white/10 rounded-xl p-3 text-sm  outline-none focus:border-primary"
                required>
                <option value="">-- Choisir le cours à évaluer --</option>
                @foreach ($affectations as $aff)
                    <option value="{{ $aff->id }}">
                        {{ $aff->matiere->nom }} —
                        {{-- On passe par la classe pour atteindre le niveau --}}
                        {{ $aff->classe->niveau->nom ?? 'Niveau' }}
                        {{ $aff->classe->nom }}
                    </option>
                @endforeach
            </select>

            {{-- 3. Bouton de validation --}}
            <button type="submit"
                class="bg-primary hover:bg-primary/80 font-black rounded-xl p-3  text-[10px] transition-all">
                Créer & Saisir Notes
            </button>

            {{-- Libellé caché par défaut pour aller vite --}}
            <input type="hidden" name="titre" value="Évaluation - {{ $anneeActive->libelle }}">
        </form>
    </div>
@endsection
