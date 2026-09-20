@extends('layouts.admin.admin-layout')

@section('content')
    {{-- 3. Affichage du Niveau et de la Salle --}}
    @php
        $derniereInsc = $eleve->inscriptions->last();
    @endphp


    <div class="min-h-screen bg-background p-4 md:p-8 text-foreground font-sans">

        <!-- En-tête -->
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.students.index') }}"
                    class="p-2 hover:bg-secondary rounded-lg border border-border text-muted-foreground hover:text-foreground transition-colors"
                    title="Retour à la liste">
                    <x-lucide-arrow-left class="w-5 h-5" />
                </a>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">Dossier de l'élève</h1>
                    <p class="text-xs text-muted-foreground font-mono">Matricule : {{ $eleve->matricule }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                {{-- <button type="button" onclick="window.print()"
                    class="flex-1 sm:flex-none px-4 py-2 bg-secondary border border-border rounded-lg text-xs font-semibold hover:bg-accent transition-colors flex items-center justify-center gap-2">
                    <x-lucide-printer class="w-4 h-4" />
                    Imprimer
                </button> --}}

                <a href="{{ route('admin.students.edit', $eleve->id) }}"
                    class="flex-1 sm:flex-none px-4 py-2 bg-primary text-primary-foreground rounded-lg text-xs font-semibold hover:opacity-90 transition-opacity flex items-center justify-center gap-2 shadow-sm">
                    <x-lucide-pencil class="w-4 h-4" />
                    Modifier
                </a>
            </div>
        </div>

        <!-- Grille Principale -->
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Colonne Gauche : Identité & Classe -->
            <div class="space-y-6">
                <!-- Carte Identité -->
                <div class="bg-card border border-border rounded-2xl p-6 shadow-sm flex flex-col items-center text-center">
                    <div
                        class="w-32 h-32 bg-secondary rounded-2xl mb-4 overflow-hidden border border-border flex items-center justify-center shadow-inner">
                        @if ($eleve->photo)
                            <img src="{{ asset('storage/' . $eleve->photo) }}" alt="{{ $eleve->nom }}"
                                class="w-full h-full object-cover">
                        @else
                            <x-lucide-user class="w-12 h-12 text-muted-foreground/40" />
                        @endif
                    </div>

                    <h2 class="text-lg font-bold text-card-foreground leading-snug">{{ $eleve->nom }} {{ $eleve->prenom }}
                    </h2>
                    <p class="text-xs text-muted-foreground mt-0.5">Élève régulier</p>

                    <div
                        class="mt-4 inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-medium border border-emerald-500/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Inscrit</span>
                    </div>
                </div>

                <!-- Carte Classe -->
                <div class="bg-card border border-border rounded-2xl p-6 shadow-sm">
                    <p class="text-xs font-semibold text-muted-foreground  tracking-wider mb-2">Classe Actuelle
                        (2025-2026)</p>

                    @php
                        $derniereInsc = $eleve->inscriptions->last();
                    @endphp

                    <div class="flex items-center gap-2">
                        @if ($derniereInsc && $derniereInsc->classe)
                            <span class="text-2xl font-bold text-primary">
                                {{ $derniereInsc->classe->nom }}
                            </span>
                        @else
                            <span class="text-sm font-semibold text-destructive ">Non Inscrit</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne Droite : Informations Détaillées -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Section État Civil -->
                <div class="bg-card border border-border rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <h3 class="text-xs font-bold text-muted-foreground  tracking-wider">État Civil</h3>
                        <div class="h-px flex-1 bg-border"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <span class="text-xs text-muted-foreground block mb-1">Date de naissance</span>
                            <p class="text-sm font-medium text-foreground">
                                {{ \Carbon\Carbon::parse($eleve->date_naissance)->translatedFormat('d F Y') }}
                            </p>
                        </div>

                        <div>
                            <span class="text-xs text-muted-foreground block mb-1">Lieu de naissance</span>
                            <p class="text-sm font-medium text-foreground">{{ $eleve->lieu_naissance ?? 'Non renseigné' }}
                            </p>
                        </div>

                        <div>
                            <span class="text-xs text-muted-foreground block mb-1">Sexe</span>
                            <p class="text-sm font-medium text-foreground">
                                {{ $eleve->sexe == 'M' ? 'Masculin' : 'Féminin' }}</p>
                        </div>

                        <div>
                            <span class="text-xs text-muted-foreground block mb-1">Statut académique</span>
                            <span
                                class="inline-flex items-center text-xs font-semibold {{ $eleve->statut ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                {{ $eleve->statut ? 'Redoublant' : 'Nouveau / Passant' }}
                            </span>
                        </div>

                        <div class="sm:col-span-2 pt-4 border-t border-border/60">
                            <span class="text-xs text-muted-foreground block mb-1">Âge actuel</span>
                            <div class="flex items-center gap-2">
                                <span class="text-base font-bold text-foreground">{{ $eleve->age }} ans</span>
                                <span
                                    class="text-[10px] px-2 py-0.5 bg-secondary text-secondary-foreground rounded font-semibold ">
                                    {{ $eleve->age >= 18 ? 'Majeur' : 'Mineur' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Contacts -->
                <div class="bg-card border border-border rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <h3 class="text-xs font-bold text-muted-foreground  tracking-wider">Contact & Urgence</h3>
                        <div class="h-px flex-1 bg-border"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 p-3.5 bg-secondary/40 rounded-xl border border-border">
                            <div class="p-2.5 rounded-lg bg-primary/10 text-primary shrink-0">
                                <x-lucide-phone-call class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-muted-foreground">Téléphone Parent</p>
                                <p class="text-sm font-semibold text-foreground truncate">
                                    {{ $eleve->telephone_parent ?? 'Non renseigné' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3.5 bg-secondary/40 rounded-xl border border-border">
                            <div class="p-2.5 rounded-lg bg-primary/10 text-primary shrink-0">
                                <x-lucide-map-pin class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-muted-foreground">Adresse / Quartier</p>
                                <p class="text-sm font-semibold text-foreground truncate">
                                    {{ $eleve->adresse ?? 'Non spécifiée' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 p-3.5 bg-secondary/40 rounded-xl border border-border">
                            <div class="p-2.5 rounded-lg bg-primary/10 text-primary shrink-0">
                                <x-lucide-phone-call class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-muted-foreground">Nom du Père</p>
                                <p class="text-sm font-semibold text-foreground truncate">
                                    {{ $eleve->name_father ?? 'Non renseigné' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3.5 bg-secondary/40 rounded-xl border border-border">
                            <div class="p-2.5 rounded-lg bg-primary/10 text-primary shrink-0">
                                <x-lucide-phone-call class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-muted-foreground">Nom de la Mère</p>
                                <p class="text-sm font-semibold text-foreground truncate">
                                    {{ $eleve->name_mother ?? 'Non renseigné' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
