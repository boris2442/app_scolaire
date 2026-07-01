@extends('layouts.admin.admin-layout')

@section('content')
    <div class="p-4 md:p-8 mb-10">
        {{-- Bouton Retour --}}
        <div class="mb-6">
            <a href="{{ route('admin.evaluations.index') }}"
                class="inline-flex items-center text-[10px] tracking-widest font-black hover:text-primary transition-all text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
                Retour aux evaluations
            </a>
        </div>

        {{-- En-tête --}}
        <div class="mb-8 border-l-4 border-primary pl-4 py-2 bg-secondary/5 rounded-r-2xl">
            <h2 class="text-[10px] font-bold  text-primary tracking-widest opacity-80">Saisie des notes</h2>
            <h1 class="text-lg md:text-xl font-black leading-tight uppercase">{{ $evaluation->titre }}</h1>
            <div class="mt-2 flex gap-4 text-[10px] text-white/60 font-medium italic">
                <span>Classe: {{ $evaluation->classe->nom }}</span>
                <span>Matière: {{ $evaluation->matiere->nom }}</span>
            </div>
        </div>

        <form action="{{ route('admin.evaluations.bulk-store', $evaluation->id) }}" method="POST">
            @csrf

            {{-- CONTENEUR MAGIQUE POUR LE SCROLL MOBILE --}}
            <div class="bg-secondary/10 border border-white/5 rounded-3xl overflow-hidden">
                <div class="overflow-x-auto"> {{-- Permet le défilement horizontal --}}
                    <table class="w-full text-left border-collapse min-w-[700px]"> {{-- Force la largeur minimum --}}
                        <thead>
                            <tr class="text-primary  text-[10px] tracking-widest border-b border-white/10 bg-white/5">
                                <th class="p-4 text-center w-20">Matricule</th>
                                <th class="p-4">Nom de l'élève</th>
                                <th class="p-4 w-32 text-center">Note / 20</th>
                                <th class="p-4 w-64">Observation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach ($inscriptions as $index => $inscription)
                                @php
                                    $maNote = $notesExistantes->get((int) $inscription->id);
                                @endphp
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="p-4 text-center font-mono text-[10px] ">
                                        {{ $inscription->eleve->matricule }}
                                    </td>
                                    <td class="p-4 font-bold text-xs uppercase ">
                                        {{ $inscription->eleve->nom }} {{ $inscription->eleve->prenom }}
                                    </td>
                                    <td class="p-4">
                                        <input type="number" step="0.25" name="notes[{{ $inscription->id }}][valeur]"
                                            value="{{ $maNote->valeur ?? '' }}"
                                            class="w-full bg-secondary border {{ isset($maNote) ? 'border-primary/50' : 'border-white/10' }} rounded-xl px-2 py-2 text-center font-black text-primary text-sm outline-none focus:border-primary transition-all"
                                            placeholder="--">
                                    </td>
                                    {{-- <td class="p-4 text-right">
                                      
                                        <select name="notes[{{ $inscription->id }}][observation]" ...>
                                            <option value="">-- Appréciation --</option>
                                            @foreach (\App\Models\Note::APPRECIATIONS as $code => $libelle)
                                                <option value="{{ $code }}"
                                                    {{ ($maNote->observation ?? '') == $code ? 'selected' : '' }}>
                                                    {{ $libelle }} ({{ $code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Bouton d'enregistrement --}}
            <div class="mt-8 flex justify-center">
                <button type="submit"
                    class="bg-primary hover:scale-105 active:scale-95 text-secondary font-black px-12 py-4 rounded-2xl uppercase text-xs transition-all shadow-xl shadow-primary/20 flex items-center gap-2">
                    <span>💾</span> Enregistrer les notes
                </button>
            </div>
        </form>

        <div class="">
            <a href="{{ route('admin.evaluations.telecharger-stats', $evaluation->id) }}" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Télécharger les stats
            </a>
        </div>
    </div>
    <script>
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                // Cibler uniquement nos champs de notes
                if (event.target.name && event.target.name.includes('[valeur]')) {

                    event.preventDefault();

                    const inputs = Array.from(document.querySelectorAll('input[name*="[valeur]"]'));
                    const index = inputs.indexOf(event.target);

                    // Cas 1 : Il reste des élèves après
                    if (index > -1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                        inputs[index + 1].select();
                    }
                    // Cas 2 : C'est le dernier élève de la liste
                    else if (index === inputs.length - 1) {
                        const confirmer = confirm(
                            "Vous êtes au dernier élève. Voulez-vous enregistrer toutes les notes maintenant ?");
                        if (confirmer) {
                            // On soumet le formulaire automatiquement
                            event.target.closest('form').submit();
                        }
                    }
                }
            }
        });
    </script>
@endsection
