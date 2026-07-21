@extends('layouts.admin.admin-layout')

@section('content')
    <div class="container mx-auto p-6 bg-background text-foreground min-h-screen">

        <!-- En-tête -->
        {{-- <div class="mb-8">
            <h1 class="text-3xl font-bold text-primary">
                Emploi du temps de l'enseignant
            </h1>

            <p class="mt-2 text-sm text-foreground/70">
                Enseignant :
                <span class="font-semibold">{{ $enseignant->name }}</span>
            </p>
        </div> --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Emploi du temps de l'enseignant : {{ $enseignant->name }}</h1>

            <a href="{{ route('emplois.enseignant.pdf', $enseignant->id) }}"
                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center space-x-2">
                <span>Télécharger en PDF</span>
            </a>
        </div>




        <!-- Tableau -->
        <div class="bg-card text-card-foreground border border-border rounded-xl shadow-lg overflow-hidden">

            <div class="overflow-x-auto">

                <table class="min-w-full border-collapse text-center">

                    <thead>

                        <tr class="bg-secondary text-secondary-foreground">

                            <th class="border border-border p-4">
                                Créneaux / Jours
                            </th>

                            @foreach ($jours as $jour)
                                <th class="border border-border p-4">
                                    {{ $jour->nom }}
                                </th>
                            @endforeach

                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($creneaux as $creneau)
                            <tr class="hover:bg-secondary/40 transition">

                                <!-- Horaire -->

                                <td class="border border-border bg-secondary p-4 font-semibold">

                                    {{ \Carbon\Carbon::parse($creneau->heure_debut)->format('H:i') }}

                                    -

                                    {{ \Carbon\Carbon::parse($creneau->heure_fin)->format('H:i') }}

                                </td>

                                @foreach ($jours as $jour)
                                    <td class="border border-border p-3 align-top">

                                        @php

                                            $seance = $seances
                                                ->where('jour_id', $jour->id)
                                                ->where('creneau_id', $creneau->id)
                                                ->first();

                                        @endphp

                                        @if ($seance)
                                            <div class="rounded-lg border border-primary/20 bg-primary/10 p-3 shadow-sm">
                                                <div class="font-semibold text-primary">
                                                    {{ $seance->matiere->nom ?? 'Matière' }}
                                                </div>

                                                <div class="mt-2 text-xs text-foreground/70 space-y-0.5">
                                                    <div>
                                                        Niveau : <span
                                                            class="font-semibold">{{ $seance->classe->niveau->nom ?? ($seance->classe->niveau ?? '-') }}</span>
                                                    </div>
                                                    <div>
                                                        Classe / Groupe : <span
                                                            class="font-semibold">{{ $seance->classe->nom ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="py-4 text-xs italic text-foreground/40">
                                                Libre
                                            </div>
                                        @endif

                                    </td>
                                @endforeach

                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>
@endsection
