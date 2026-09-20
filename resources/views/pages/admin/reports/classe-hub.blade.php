@extends('layouts.admin.admin-layout')

@section('content')
    <div class="container mx-auto px-4 py-6">

        <div class="mb-6">
            <a href="{{ route('admin.bulletins.index', ['trimestre_id' => $trimesterId]) }}"
                class="inline-flex items-center text-sm text-foreground/60 hover:text-primary transition mb-4">
                <x-lucide-chevrons-left class="w-4 h-4 mr-1.5" />
                Retour aux classes
            </a>



            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-border pb-5">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Gestion des Bulletins — {{ $classe->nom }}</h1>
                    <p class="text-sm text-foreground/60">Générez le bulletins de notes global de la classe ou ciblez un
                        élève
                        précis.</p>
                </div>


            </div>
            <div class="flex items-center gap-3 flex-wrap pt-3">
                <!-- Bouton existant pour imprimer tous les bulletins -->
                <a href="{{ route('admin.bulletins.etat-controle-notes', [
                    'classeId' => $classe->id,
                    'trimestreId' => $trimesterId,
                ]) }}"
                    target="_blank" data-turbo="false"
                    class="inline-flex items-center justify-center px-3 py-2 bg-primary text-primary-foreground hover:bg-primary/90 text-sm font-semibold rounded transition shadow-sm gap-2">
                    <span> Contrôler les notes</span>
                </a>
                <!-- Bouton existant pour imprimer tous les bulletins -->
                <a href="{{ route('admin.bulletins.imprimer-classe', [$classe->id, $trimesterId]) }}" target="_blank"
                    data-turbo="false"
                    class="inline-flex items-center justify-center px-3 py-2 bg-primary text-primary-foreground hover:bg-primary/90 text-sm font-semibold rounded transition shadow-sm gap-2">
                    <span>Imprimer les Bulletins</span>
                </a>

                <!-- Bouton pour imprimer les Statistiques de la classe -->
                <a href="{{ route('admin.bulletins.download-stats', [$classe->id, $trimesterId]) }}" target="_blank"
                    data-turbo="false"
                    class="inline-flex items-center justify-center px-3 py-2 bg-secondary text-secondary-foreground hover:bg-secondary/80 text-sm font-semibold rounded transition shadow-sm gap-2 border border-border">
                    <x-lucide-bar-chart-3 class="w-4 h-4" />
                    <span>Imprimer les Statistiques</span>
                </a>
                <!-- NOUVEAU : Bouton pour le Tableau d'Honneur -->
                <a href="{{ route('admin.bulletins.tableau-honneur', [$classe->id, $trimesterId]) }}" target="_blank"
                    data-turbo="false"
                    class="inline-flex items-center justify-center px-3 py-2 bg-amber-500 text-white hover:bg-amber-600 text-sm font-semibold rounded transition shadow-sm gap-2">
                    <x-lucide-award class="w-4 h-4" />
                    <span>Tableau d'Honneur</span>
                </a>

            </div>
        </div>

        <div class="bg-card text-card-foreground rounded-xl border border-border shadow-sm overflow-hidden">
            <div class="p-4 bg-secondary/20 border-b border-border">
                <h2 class="font-semibold text-foreground">Affichage de {{ $students->firstItem() }} à
                    {{ $students->lastItem() }} sur
                    <span class="font-bold text-foreground">{{ $students->total() }}</span> élèves au total
                </h2>
            </div>

            <div class="space-y-4">
                <div class="overflow-x-auto rounded-lg border border-border shadow-sm">
                    <table class="w-full border-collapse text-left text-sm">
                        <thead>
                            <tr class="bg-secondary/40 text-secondary-foreground border-b border-border font-medium">
                                <th class="p-4">Numero / Matricule</th>
                                <th class="p-4">Nom & Prénom</th>
                                <th class="p-4 text-right">Action</th> {{-- En-tête ajoutée ici --}}
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse ($students as $index => $student)
                                <tr class="hover:bg-secondary/10 transition">
                                    <td class="p-2 sm:p-4 font-mono text-xs text-foreground/70 whitespace-nowrap">
                                        <span class="font-bold sm:font-normal">#{{ $students->firstItem() + $index }}</span>
                                        <span class="hidden sm:inline"> / {{ $student->matricule ?? 'N/A' }}</span>
                                    </td>
                                    <td class="p-2 sm:p-4 text-xs sm:text-sm font-medium text-foreground">
                                        <div>{{ $student->nom }} {{ $student->prenom }}</div>
                                        <div class="sm:hidden font-mono text-[10px] text-muted-foreground">
                                            {{ $student->matricule ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="p-2 sm:p-4 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.bulletins.imprimer-eleve', ['inscriptionId' => $student->inscription_id, 'trimestreId' => $trimesterId]) }}"
                                            class="inline-flex items-center gap-1.5 bg-primary hover:bg-primary/90 text-white text-xs font-medium p-2 sm:px-3 sm:py-1.5 rounded transition shadow-sm"
                                            target="_blank">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            <span class="hidden sm:inline">Imprimer le bulletin</span>
                                        </a>
                                    </td>
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

                {{-- Liens de pagination placés EN DEHORS du <table> --}}
                <div class="p-2">
                    {{ $students->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection
