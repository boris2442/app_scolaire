@extends('layouts.admin.admin-layout')

@section('content')
    {{-- Formulaire de Création --}}
    <div class="bg-card border border-border rounded-3xl p-8 mb-10 shadow-sm">

        {{-- En-tête --}}
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-card-foreground">
                Initialiser une session de notes
            </h2>

            <p class="mt-2 text-sm text-card-foreground/60">
                Sélectionnez la période, le cours et les chapitres concernés par cette évaluation.
            </p>
        </div>


        <form action="{{ route('admin.evaluations.store') }}" method="POST" class="space-y-6">

            @csrf


            {{-- Informations principales --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">


                {{-- Séquence --}}
                <div>
                    <label class="block text-sm font-semibold text-card-foreground mb-2">
                        Période d'évaluation
                    </label>

                    <select name="sequence_id"
                        class="w-full bg-background border border-border rounded-xl p-3 text-sm text-card-foreground outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                        required>

                        <option value="">
                            -- Choisir une séquence --
                        </option>

                        @foreach ($sequences->groupBy('trimestre_id') as $trimestreId => $group)
                            <optgroup label="Trimestre {{ $loop->iteration }}">

                                @foreach ($group as $seq)
                                    <option value="{{ $seq->id }}">
                                        {{ $seq->nom }}
                                    </option>
                                @endforeach

                            </optgroup>
                        @endforeach

                    </select>

                </div>



                {{-- Cours --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-card-foreground mb-2">
                        Cours à évaluer
                    </label>

                    <select name="affectation_id"
                        class="w-full bg-background border border-border rounded-xl p-3 text-sm text-card-foreground outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                        required>


                        <option value="">
                            -- Choisir le cours --
                        </option>


                        @foreach ($affectations as $aff)
                            <option value="{{ $aff->id }}">

                                {{ $aff->matiere->nom }}
                                —
                                {{ $aff->classe->niveau->nom ?? 'Niveau' }}
                                {{ $aff->classe->nom }}

                            </option>
                        @endforeach


                    </select>

                </div>


            </div>



            {{-- Leçons --}}
            {{-- <div class="border border-border rounded-2xl p-5 bg-background">


                <div class="mb-4">

                    <h3 class="font-semibold text-card-foreground">
                        Chapitres évalués
                    </h3>

                    <p class="text-xs text-card-foreground/50 mt-1">
                        Cochez les leçons incluses dans cette évaluation.
                    </p>

                </div>



                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-64 overflow-y-auto pr-2">


                    @forelse ($lessons ?? [] as $lesson)
                        <label
                            class="flex items-center gap-3 p-3 rounded-xl border border-border bg-card cursor-pointer transition-all
                        hover:border-primary hover:bg-primary/5
                        has-[:checked]:border-primary
                        has-[:checked]:bg-primary/10">


                            <input type="checkbox" name="lesson_ids[]" value="{{ $lesson->id }}"
                                class="h-5 w-5 rounded-md border-border text-primary focus:ring-primary">


                            <div>

                                <span class="text-xs font-bold text-primary">
                                    Chapitre {{ $lesson->ordre }}
                                </span>


                                <p class="text-sm font-medium text-card-foreground">
                                    {{ $lesson->titre }}
                                </p>

                            </div>


                        </label>


                    @empty


                        <div class="col-span-full text-center py-8">

                            <p class="text-sm text-card-foreground/60">
                                Aucune leçon disponible.
                            </p>

                            <p class="text-xs text-card-foreground/40 mt-1">
                                Ajoutez d'abord des chapitres pour cette matière.
                            </p>

                        </div>
                    @endforelse


                </div>


            </div>
 --}}



            {{-- Action --}}
            <div class="flex justify-end">


                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-primary text-primary-foreground font-semibold text-sm
                hover:opacity-90 transition-all shadow-lg shadow-primary/20">

                    Créer & saisir les notes

                </button>


            </div>



            <input type="hidden" name="titre" value="Évaluation - {{ $anneeActive->libelle }}">


        </form>


    </div>
@endsection
