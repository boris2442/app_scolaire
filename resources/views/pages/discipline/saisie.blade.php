@extends('layouts.admin.admin-layout')

@section('content')
    <div class="container mx-auto p-6">
        <!-- Header avec style professionnel -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-foreground">
                <a href="{{ route('discipline.index') }}" title="Retour à la liste des classes"
                    aria-label="Retour à la liste des classes" class="text-primary hover:underline"><svg
                        class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg></a>
                Saisie Disciplinaire
            </h2>
            <p class="text-sm text-muted-foreground mt-1">
                Classe : <span class="font-semibold text-primary">{{ $classe->nom }}</span> |
                Trimestre : <span class="font-semibold text-primary">{{ $trimestre->nom }}</span>
            </p>
        </div>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('discipline.store') }}" method="POST">
            @csrf
            <input type="hidden" name="trimestre_id" value="{{ $trimestre->id }}">
            <input type="hidden" name="classe_id" value="{{ $classe->id }}">

            <!-- Card Container -->
            <div class="bg-card text-card-foreground border border-border rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-secondary text-secondary-foreground  text-xs font-medium">
                            <tr>
                                <th class="px-4 py-4 w-12 text-center">N°</th>
                                <th class="px-6 py-4">Élève</th>
                                <th class="px-4 py-4 text-center">Retards (h)</th>
                                <th class="px-4 py-4 text-center">Absences</th>
                                <th class="px-4 py-4 text-center">Suspensions</th>
                                <th class="px-4 py-4 text-center">Avert.</th>
                                <th class="px-4 py-4 text-center">Blâmes</th>
                                <th class="px-4 py-4 text-center">Excl. (j)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($inscriptions as $inscription)
                                <tr class="hover:bg-secondary/50 transition-colors">
                                    <td class="px-4 py-3 text-center text-muted-foreground font-mono">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-6 py-3 font-medium">{{ $inscription->eleve->nom }}
                                        {{ $inscription->eleve->prenom }}</td>

                                    @foreach (['retards', 'absences', 'suspensions', 'avertissements', 'blames', 'exclusions'] as $field)
                                        <td class="px-2 py-2 bg-secondary/20">
                                            <input type="number" name="data[{{ $inscription->id }}][{{ $field }}]"
                                                value="{{ $inscription->suivi->$field ?? 0 }}"
                                                class="w-full bg-secondary border  border-primary/50 border-white/10 rounded px-2 py-2 text-center font-black text-primary text-sm outline-none focus:border-primary transition-all"
                                                min="0">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit"
                    class="bg-primary text-primary-foreground px-6 py-2 rounded-md hover:opacity-90 transition-opacity font-semibold shadow-md">
                    Enregistrer les données disciplinaires
                </button>
            </div>
        </form>
    </div>


    <script>
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                const inputs = Array.from(document.querySelectorAll('input[type="number"]'));
                const index = inputs.indexOf(event.target);

                // On définit le nombre de champs par ligne (ici 6 : retards, absences, etc.)
                const nbColonnes = 6;

                // Si l'index est valide et qu'il existe une ligne suivante
                if (index > -1 && (index + nbColonnes) < inputs.length) {
                    event.preventDefault();
                    const nextInput = inputs[index + nbColonnes];
                    nextInput.focus();
                    nextInput.select();
                }
            }
        });
    </script>
@endsection
