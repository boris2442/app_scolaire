@extends('layouts.admin.admin-layout')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Emploi du temps : {{ $classe->nom }}</h1>

        <!-- Tableau de l'emploi du temps -->
        <div class="overflow-x-auto bg-white shadow rounded-lg p-4">
            <table class="min-w-full border-collapse border border-gray-200 text-center">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-200 p-2">Créneaux / Jours</th>
                        @foreach ($jours as $jour)
                            <th class="border border-gray-200 p-2">{{ $jour->nom }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($creneaux as $creneau)
                        <tr>
                            <!-- Colonne des horaires -->
                            <td class="border border-gray-200 p-2 font-semibold bg-gray-50">
                                {{ \Carbon\Carbon::parse($creneau->heure_debut)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($creneau->heure_fin)->format('H:i') }}
                                @if ($creneau->libelle)
                                    <br><span class="text-xs text-gray-500">{{ $creneau->libelle }}</span>
                                @endif
                            </td>

                            <!-- Cellules des jours pour ce créneau -->
                            @foreach ($jours as $jour)
                                <td class="border border-gray-200 p-2 align-top">
                                    @php
                                        // On cherche si une séance existe pour ce jour et ce créneau dans cette classe
                                        $seance = $seances
                                            ->where('jour_id', $jour->id)
                                            ->where('creneau_id', $creneau->id)
                                            ->first();
                                    @endphp

                                    @if ($seance)
                                        <div class="bg-blue-50 border border-blue-200 rounded p-2 text-sm">
                                            <p class="font-bold text-blue-800">{{ $seance->matiere->nom ?? 'Matière' }}</p>
                                            <p class="text-gray-600 text-xs">{{ $seance->enseignant->nom ?? 'Enseignant' }}
                                            </p>
                                        </div>
                                    @else
                                        <span class="text-gray-300 text-xs">- Libre -</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
