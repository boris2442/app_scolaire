@extends('layouts.admin.admin-layout')

@section('content')
    <div class="container mx-auto p-6 bg-background text-foreground min-h-screen">
        <div class="flex items-center justify-between mb-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-foreground">Gestion des Résultats</h1>

                <p class="text-xs italic opacity-70 text-foreground mt-1">
                    Le moteur de calcul traite les notes brutes pour générer les moyennes pondérées,
                    établit le classement automatique des élèves et prépare les statistiques de performance par classe.
                </p>
            </div>
            <div class="bg-primary/10 text-primary px-4 py-2 rounded-full border border-primary/20 text-sm font-medium">
                Année  : 2025-2026
            </div>
        </div>

        @if (session('success'))
            <div
                class="bg-success/10 border border-success/20 text-success px-4 py-3 rounded-lg mb-6 flex items-center gap-3">
            
                <x-lucide-check-circle class='w-4 h-4' />
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($classes as $classe)
                <div
                    class="bg-card text-card-foreground border border-border rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="p-5 border-b border-border flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-lg">{{ $classe->nom }}</h3>
                            <span class="text-xs uppercase tracking-wider text-secondary-foreground opacity-60">
                                {{ $classe->niveau->nom ?? 'Niveau Standard' }}
                            </span>
                        </div>
                        <div class="h-10 w-10 rounded-lg bg-secondary flex items-center justify-center text-primary">
                            {{-- <i class="fas fa-graduation-cap"></i> --}}
                            <x-lucide-graduation-cap class='w-4 h-4' />
                        </div>
                    </div>

            

                    <form action="{{ route('admin.resultats.calculer') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="classe_id" value="{{ $classe->id }}">

                        <div>
                            <label class="block text-xs font-semibold mb-1 uppercase opacity-70">Période de calcul</label>
                            <select name="type_periode" id="periode_select_{{ $classe->id }}"
                                onchange="updateHiddenInputs(this, {{ $classe->id }})"
                                class="w-full bg-background border border-input text-foreground rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-ring transition-all"
                                required>
                                <option value="">Choisir la période...</option>

                                <optgroup label="Séquences (Évaluations continues)">
                                    @foreach ($sequences as $sequence)
                                        <option value="seq_{{ $sequence->id }}">Calculer {{ $sequence->nom }}</option>
                                    @endforeach
                                </optgroup>

                                <optgroup label="Trimestres (Bilans globaux)">
                                    @foreach ($trimestres as $trimestre)
                                        <option value="trim_{{ $trimestre->id }}">Calculer {{ $trimestre->nom }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>

                        <input type="hidden" name="sequence_id" id="seq_input_{{ $classe->id }}">
                        <input type="hidden" name="trimestre_id" id="trim_input_{{ $classe->id }}">

                        <button type="submit"
                            class="w-full bg-primary text-primary-foreground hover:opacity-90 font-bold py-2.5 rounded-lg shadow-sm transition flex items-center justify-center gap-2">
                            {{-- <i class="fas fa-calculator text-sm"></i> --}}
                            <x-lucide-calculator class='w-4 h-4' />
                            Lancer le calcul
                        </button>
                    </form>



                </div>
            @endforeach
        </div>
    </div>

    <script>
        function updateHiddenInputs(select, classeId) {
            const val = select.value;
            const seqInput = document.getElementById('seq_input_' + classeId);
            const trimInput = document.getElementById('trim_input_' + classeId);

            // Reset
            seqInput.value = '';
            trimInput.value = '';

            if (val.startsWith('seq_')) {
                seqInput.value = val.replace('seq_', '');
            } else if (val.startsWith('trim_')) {
                trimInput.value = val.replace('trim_', '');
            }
        }
    </script>
@endsection
