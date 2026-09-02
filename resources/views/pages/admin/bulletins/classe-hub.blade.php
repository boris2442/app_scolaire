@extends('layouts.admin.admin-layout')

@section('content')
    <div class="container mx-auto px-4 py-6">

        <div class="mb-6">
            <a href="{{ route('admin.bulletins.index', ['trimestre_id' => $trimestreId]) }}"
                class="inline-flex items-center text-sm text-foreground/60 hover:text-primary transition mb-4">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7m8 14l-7-7 7-7">
                    </path>
                </svg>
                Retour aux classes
            </a>



            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-border pb-5">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Gestion des Bulletins — {{ $classe->nom }}</h1>
                    <p class="text-sm text-foreground/60">Générez le livret de notes global de la classe ou ciblez un élève
                        précis.</p>
                </div>

              
            </div>
              <div class="flex items-center gap-3 flex-wrap pt-3">
                    <!-- Bouton existant pour imprimer tous les bulletins -->
                    <a href="{{ route('admin.bulletins.imprimer-classe', [$classe->id, $trimestreId]) }}" target="_blank"
                        data-turbo="false"
                        class="inline-flex items-center justify-center px-3 py-2 bg-primary text-primary-foreground hover:bg-primary/90 text-sm font-semibold rounded transition shadow-sm gap-2">
                        <span>Imprimer les Bulletins</span>
                    </a>

                    <!-- Bouton pour imprimer les Statistiques de la classe -->
                    <a href="{{ route('admin.bulletins.download-stats', [$classe->id, $trimestreId]) }}" target="_blank"
                        data-turbo="false"
                        class="inline-flex items-center justify-center px-3 py-2 bg-secondary text-secondary-foreground hover:bg-secondary/80 text-sm font-semibold rounded transition shadow-sm gap-2 border border-border">
                        <x-lucide-bar-chart-3 class="w-4 h-4" />
                        <span>Imprimer les Statistiques</span>
                    </a>
                    <!-- NOUVEAU : Bouton pour le Tableau d'Honneur -->
                    <a href="{{ route('admin.bulletins.tableau-honneur', [$classe->id, $trimestreId]) }}" target="_blank"
                        data-turbo="false"
                        class="inline-flex items-center justify-center px-3 py-2 bg-amber-500 text-white hover:bg-amber-600 text-sm font-semibold rounded transition shadow-sm gap-2">
                        <x-lucide-award class="w-4 h-4" />
                        <span>Tableau d'Honneur</span>
                    </a>

                </div>
        </div>

        <div class="bg-card text-card-foreground rounded-xl border border-border shadow-sm overflow-hidden">
            <div class="p-4 bg-secondary/20 border-b border-border">
                <h2 class="font-semibold text-foreground">Effectif de la classe ({{ $eleves->count() }} élèves)</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="bg-secondary/40 text-secondary-foreground border-b border-border font-medium">
                            <th class="p-4">Numero /   Matricule</th>
                            <th class="p-4">Nom & Prénom</th>
                            <th class="p-4 text-center">Date de Naissance</th>
                            <th class="p-4 text-center">Lieu de Naissance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        {{-- @forelse($eleves as $eleve) --}}
                        @forelse ($eleves as $index => $eleve)
                        {{--   @foreach ($eleves as $index => $eleve) --}}
                            <tr class="hover:bg-secondary/10 transition">
                                <td class="p-4 font-mono text-xs text-foreground/70">{{ $index+ 1 }}   /   {{ $eleve->matricule ?? 'N/A' }}</td>
                                <td class="p-4 font-medium text-foreground">{{ $eleve->nom }} {{ $eleve->prenom }}</td>
                                <td class="p-4 font-medium text-foreground">{{ $eleve->date_naissance ?? 'N/A' }}</td>
                                <td class="p-4 font-medium text-foreground">{{ $eleve->lieu_naissance ?? 'N/A' }}</td>
                                {{-- <td class="p-4 text-center">
                                 
                                    <a href="{{ route('admin.bulletins.imprimer-eleve', ['inscription_id' => $eleve->inscription_id, 'trimestre_id' => $trimestreId]) }}"
                                        target="_blank"
                                        class="inline-flex items-center px-3 py-1.5 bg-secondary text-secondary-foreground text-xs font-medium rounded hover:bg-secondary/80 transition border border-border gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <span>Imprimer le Bulletin</span>
                                    </a>
                                </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-foreground/40 italic">
                                    Aucun élève inscrit dans cette classe pour l'année en cours.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
