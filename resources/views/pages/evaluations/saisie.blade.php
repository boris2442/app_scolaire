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
                {{-- <span>Session: {{ $evaluation->classe->niveau->nom }}</span> --}}
                <span>Classe: {{ $evaluation->classe->nom }}</span>
                <span>Matière: {{ $evaluation->matiere->nom }}</span>
            </div>
            <div>
                <a href="{{ route('admin.evaluations.telecharger-stats', $evaluation->id) }}"
                    class="bg-primary hover:scale-105 active:scale-95 text-secondary font-black  p-4 rounded  text-xs transition-all shadow-xl shadow-primary/20 gap-2 mb-2 flex justify-center items-center  overflow-hidden group-hover:max-w-[200px] group-hover:px-4 group-hover:py-2 max-w-[400px]">
                    {{-- <i class="fas fa-file-pdf"></i> --}}
                    <x-lucide-file-text class='w-4 h-4' />
                    <span>
                        Télécharger les stats
                    </span>
                </a>
            </div>
        </div>
        {{-- Section de sélection des leçons évaluées --}}

        <form action="{{ route('admin.evaluations.bulk-store', $evaluation->id) }}" method="POST">
            @csrf




            <div class="bg-card border border-border rounded-2xl mb-6">

                {{-- En-tête --}}
                <button type="button" class="w-full flex items-center justify-between p-4"
                    onclick="document.getElementById('lessons').classList.toggle('hidden')">

                    <div class="text-left">
                        <h3 class="font-semibold text-card-foreground">
                            📚 Chapitres évalués
                        </h3>

                        <p class="text-xs text-card-foreground/60">
                            {{ count($leconsEvalueesIds ?? []) }} chapitre(s) sélectionné(s)
                        </p>
                    </div>

                    <x-lucide-chevron-down class="w-5 h-5 text-primary" />

                </button>

                {{-- Contenu --}}
                <div id="lessons" class="hidden border-t border-border p-4">

                    @if ($lecons->isEmpty())
                        <p class="text-sm text-card-foreground/60">
                            Aucune leçon disponible.
                        </p>
                    @else
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-2">

                            @foreach ($lecons as $lecon)
                                <label class="flex items-center gap-2 rounded-lg p-2 cursor-pointer hover:bg-primary/5">

                                    <input type="checkbox" name="lesson_ids[]" value="{{ $lecon->id }}"
                                        {{ in_array($lecon->id, $leconsEvalueesIds ?? []) ? 'checked' : '' }}
                                        class="rounded border-border text-primary">

                                    <span class="text-sm text-card-foreground">
                                        <span class="font-semibold text-primary">
                                            {{ $lecon->ordre }}.
                                        </span>

                                        {{ $lecon->titre }}
                                    </span>

                                </label>
                            @endforeach

                        </div>
                    @endif

                </div>

            </div>
















            {{-- CONTENEUR MAGIQUE POUR LE SCROLL MOBILE --}}
            <div class="bg-secondary/10 border border-white/5 rounded-3xl overflow-hidden">
                <div class="overflow-x-auto"> {{-- Permet le défilement horizontal --}}
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-primary text-[10px] tracking-widest border-b border-white/10 bg-white/5">
                                {{-- On fixe une petite largeur pour le numéro --}}
                                <th class="py-3 px-4 text-center w-16">Numero</th>

                                {{-- Le nom prend tout l'espace nécessaire mais sans s'étirer à l'infini --}}
                                <th class="py-3 px-4">Nom de l'élève</th>

                                {{-- On donne une largeur fixe raisonnable à la colonne de la note (ex: w-32 ou w-40) --}}
                                <th class="py-3 px-4 text-center w-36">Note / 20</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach ($inscriptions as $index => $inscription)
                                @php
                                    $maNote = $notesExistantes->get((int) $inscription->id);
                                @endphp
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-3 px-4 text-center font-mono text-[10px]">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="py-3 px-4 font-bold text-xs uppercase">
                                        {{ $inscription->eleve->nom }} {{ $inscription->eleve->prenom }}
                                    </td>

                                    <td class="py-3 px-4 text-center">
                                        <input type="number" step="0.25" name="notes[{{ $inscription->id }}][valeur]"
                                            value="{{ $maNote->valeur ?? '' }}"
                                            class="w-24 mx-auto bg-secondary border {{ isset($maNote) ? 'border-primary/50' : 'border-white/10' }} rounded px-2 py-1.5 text-center font-black text-primary text-sm outline-none focus:border-primary transition-all block"
                                            placeholder="--">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Bouton d'enregistrement --}}
            {{-- Barre d'actions fixe en bas de l'écran --}}
            <div
                class="sticky bottom-4 z-20 mt-8 flex justify-center bg-secondary/80 backdrop-blur-md p-4 rounded-2xl border border-white/10 shadow-2xl">
                <button type="submit"
                    class="bg-primary hover:scale-105 active:scale-95 text-secondary font-black px-12 py-4 rounded  text-xs transition-all shadow-xl shadow-primary/20 flex items-center gap-2">
                    <span>💾</span> Enregistrer les notes
                </button>
            </div>
        </form>

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
