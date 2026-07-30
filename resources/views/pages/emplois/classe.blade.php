@extends('layouts.admin.admin-layout')

@section('content')
    <div class="container mx-auto  bg-background text-foreground min-h-screen">

        <!-- Bouton Retour -->
        <div class="mb-4">
            <a href="{{ route('admin.emplois.classes') }}" title="retour"
                class="inline-flex items-center gap-2 text-xs font-bold text-primary hover:opacity-85 transition-opacity bg-white/5 border border-white/10 px-3 py-2 rounded-xl">
                <span>←</span> Retour aux classes
            </a>
        </div>

        <!-- Titre -->
        {{-- <div class="mb-8">
            <h1 class="text-3xl font-bold text-primary">
                Emploi du temps : {{ $classe->niveau->nom ?? '' }} {{ $classe->nom }}
            </h1>

            <a href="{{ route('admin.emplois.classe.pdf', $classe->id) }}" target="_blank"
                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center space-x-2">
                <span>Télécharger en PDF</span>
            </a>


            <p class="text-sm text-foreground/70 mt-1">
                Gérez et organisez les séances de cette classe.
            </p>
        </div> --}}

      <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold"> Emploi du temps : {{ $classe->nom }}</h1>

    <a href="{{ route('admin.emplois.classe.pdf', $classe->id) }}" target="_blank" title="telechager le document pdf"
        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center space-x-2">
        <span>Télécharger en PDF</span>
    </a>
</div>

        <div class="">
            <p class="text-sm text-foreground/70 mt-1">
                Gérez et organisez les séances de cette classe.
            </p>
        </div>



        <!-- Message succès -->
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-success bg-success/10 px-4 py-3 text-success shadow">
                {{ session('success') }}
            </div>
        @endif

        <!-- Message erreur -->
        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-danger bg-danger/10 px-4 py-3 text-danger shadow">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire -->
        <div class="bg-card text-card-foreground border border-border rounded-xl shadow-lg  mb-8">

            <h2 class="text-xl font-semibold text-primary mb-6">
                Planifier un cours
            </h2>

            <form action="{{ route('admin.seances.store') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-5 gap-5 items-end">

                @csrf

                <input type="hidden" name="classe_id" value="{{ $classe->id }}">

                <!-- Jour -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Jour
                    </label>

                    <select name="jour_id" required
                        class="w-full rounded-lg border-border bg-background text-foreground focus:border-primary focus:ring-primary">

                        @foreach ($jours as $jour)
                            <option value="{{ $jour->id }}">
                                {{ $jour->nom }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <!-- Créneau -->
                <div>

                    <label class="block text-sm font-medium text-foreground mb-2">
                        Créneau
                    </label>

                    <select name="creneau_id" required
                        class="w-full rounded-lg border-border bg-background text-foreground focus:border-primary focus:ring-primary">

                        @foreach ($creneaux as $creneau)
                            <option value="{{ $creneau->id }}">

                                {{ \Carbon\Carbon::parse($creneau->heure_debut)->format('H:i') }}

                                -

                                {{ \Carbon\Carbon::parse($creneau->heure_fin)->format('H:i') }}

                            </option>
                        @endforeach

                    </select>

                </div>

                <!-- Matière -->

                <div>

                    <label class="block text-sm font-medium text-foreground mb-2">

                        Matière

                    </label>

                    <select name="matiere_id" required
                        class="w-full rounded-lg border-border bg-background text-foreground focus:border-primary focus:ring-primary">

                        @foreach ($matieres as $matiere)
                            <option value="{{ $matiere->id }}">
                                {{ $matiere->nom }}
                            </option>
                        @endforeach

                    </select>

                </div>

                <!-- Enseignant -->

                <div>

                    <label class="block text-sm font-medium text-foreground mb-2">

                        Enseignant

                    </label>

                    <select name="enseignant_id" required
                        class="w-full rounded-lg border-border bg-background text-foreground focus:border-primary focus:ring-primary">

                        @foreach ($enseignants as $enseignant)
                            <option value="{{ $enseignant->id }}">

                                {{ $enseignant->name }}

                                {{ $enseignant->prenom ?? '' }}

                            </option>
                        @endforeach

                    </select>

                </div>

                <!-- Bouton -->

                <div>

                    <button type="submit"
                        class="w-full rounded-lg bg-primary text-primary-foreground py-3 font-semibold shadow transition duration-300 hover:opacity-90 hover:scale-[1.02]">

                        Ajouter

                    </button>

                </div>
                {{-- <div>

                    <a href="{{ route('admin.emplois.classe.pdf') }}"
                        class="w-full rounded-lg bg-primary text-primary-foreground py-3 font-semibold shadow transition duration-300 hover:opacity-90 hover:scale-[1.02]">

                        download

                    </a>

                </div> --}}

            </form>

        </div>

        <!-- Tableau -->

        <div class="bg-card border border-border rounded-xl shadow-lg overflow-hidden">

            <div class="overflow-x-auto">

                <table class="min-w-full text-center border-collapse">

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

                                    @if ($creneau->libelle)
                                        <div class="text-xs text-foreground/60 mt-1">

                                            {{ $creneau->libelle }}

                                        </div>
                                    @endif

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

                                                <div class="text-xs text-foreground/70 mt-1">

                                                    {{ $seance->enseignant->user->name ?? 'Enseignant' }}

                                                    {{ $seance->enseignant->prenom ?? '' }}

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
