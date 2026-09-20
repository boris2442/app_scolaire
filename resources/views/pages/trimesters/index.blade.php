@extends('layouts.admin.admin-layout')

@section('content')
    <div class="p-6 space-y-8">
        <h1 class="text-2xl font-bold  uppercase tracking-wider">Configuration des Périodes</h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- FORMULAIRE DE CRÉATION --}}
            <div class="bg-secondary/10 border border-white/5 p-6 rounded-2xl backdrop-blur-sm">
                <h2 class="text-lg text-primary mb-6 flex items-center gap-2">
                    <x-lucide-plus-circle class='w-4 h-4'/>
                    Nouveau Trimestre
                </h2>

                <form action="{{ route('admin.trimestres.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="text-gray-400 block mb-2 text-sm">Nom du Trimestre</label>
                        <select name="nom"
                            class="w-full bg-secondary/20 border border-white/10 rounded-xl p-3  outline-none focus:border-primary transition-all"
                            required>
                            @foreach (App\Models\Trimestre::NOMS_VALIDES as $nom)
                                <option value="{{ $nom }}">{{ $nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-gray-400 block mb-2 text-sm">Année Scolaire (Sans trimestres)</label>
                        <select name="annee_scolaire_id"
                            class="w-full bg-secondary/20 border border-white/10 rounded-xl p-3  outline-none focus:border-primary transition-all"
                            required>
                            @forelse($anneesSansTrimestres as $annee)
                                {{-- On utilise 'libelle' au lieu de 'nom' ici --}}
                                <option value="{{ $annee->id }}" {{ $annee->est_active ? 'selected' : '' }}>
                                    {{ $annee->libelle }}
                                </option>
                            @empty
                                <option disabled>Aucune année disponible pour configuration</option>
                            @endforelse
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary hover:bg-primary/80  font-bold py-2 rounded-xl shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2 px-4">
                        <x-lucide-save class='w-4 h-4' /> INITIALISER LE TRIMESTRE
                    </button>
                </form>
            </div>

            {{-- RÉCAPITULATIF ANNÉE ACTIVE --}}
            <div class="bg-secondary/10 border border-white/5 p-6 rounded-2xl">
                <h2 class="text-lg text-white mb-6">Structure : {{ $anneeActive->nom ?? 'Aucune année active' }}</h2>

                @if ($anneeActive && $anneeActive->trimestres->count() > 0)
                    <div class="space-y-4">
                        @foreach ($anneeActive->trimestres as $trimestre)
                            <div class="bg-white/5 rounded-xl p-4 border-l-4 border-primary">
                                <h3 class="font-bold text-white">{{ $trimestre->nom }}</h3>
                                <div class="flex gap-2 mt-2">
                                    @forelse($trimestre->sequences as $seq)
                                        <span
                                            class="text-xs bg-primary/10 text-primary px-2 py-1 rounded-md border border-primary/20">
                                            {{ $seq->nom }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-500 italic">Aucune séquence liée</span>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <p class="text-gray-500 italic">L'année active n'a pas encore de structure définie.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
