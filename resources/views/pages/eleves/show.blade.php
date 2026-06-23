@extends('layouts.admin.admin-layout')

@section('content')
    {{-- 3. Affichage du Niveau et de la Salle --}}
    @php
        $derniereInsc = $eleve->inscriptions->last();
    @endphp

    {{-- <h2 class="text-4xl font-black italic">
        @if ($derniereInsc)
            {{ $derniereInsc->classe->nom }} {{ $derniereInsc->salle }}
        @else
            Pas encore inscrit
        @endif
    </h2> --}}
    <div class="min-h-screen bg-background p-4 lg:p-8 text-foreground font-sans">

        <div class="max-w-6xl mx-auto flex justify-between items-center mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.students.index') }}"
                    class="p-2 hover:bg-secondary rounded-full border border-border transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">Dossier de l'élève</h1>
                    <p class="text-[10px] text-muted-foreground  font-black tracking-widest">ID: {{ $eleve->matricule }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button
                    class="px-4 py-2 bg-secondary border border-border rounded-lg text-xs font-bold  hover:bg-border transition-all">Imprimer
                    Fiche</button>
                <button
                    class="px-4 py-2 bg-primary text-primary-foreground rounded-lg text-xs font-bold  shadow-lg shadow-primary/20">
                    <a href="{{ route('admin.students.edit', $eleve->id) }}"
                        class="text-white no-underline">Modifier</a></button>
            </div>
        </div>

        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="space-y-6">
                <div class="bg-card border border-border rounded-3xl p-6 shadow-sm">
                    <div
                        class="w-full aspect-square bg-secondary rounded-2xl mb-4 overflow-hidden border-2 border-primary/10 flex items-center justify-center">
                        @if ($eleve->photo)
                            <img src="{{ asset('storage/' . $eleve->photo) }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-user text-5xl opacity-10"></i>
                        @endif
                    </div>
                    <div class="text-center">
                        <h2 class="text-lg font-black  tracking-tighter">{{ $eleve->nom }}</h2>
                        <p class="text-sm font-medium text-muted-foreground">{{ $eleve->prenom }}</p>
                        <div
                            class="mt-4 inline-flex items-center gap-2 px-3 py-1 bg-green-500/10 text-green-500 rounded-full border border-green-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-[10px] font-black  tracking-widest">Inscrit</span>
                        </div>
                    </div>
                </div>

                <div class="bg-card p-6 rounded-[2.5rem] text-black shadow-xl">
                    <p class="text-[10px] font-black uppercase opacity-70 mb-2">Affectation Année 2025-2026</p>

                    <div class="flex items-end gap-2">
                        @php
                            $derniereInsc = $eleve->inscriptions->last();
                        @endphp

                        @if ($derniereInsc && $derniereInsc->classe)
                            <span class="text-5xl font-black italic">
                                {{ $derniereInsc->classe->niveau->nom ?? 'Niveau inconnu' }}
                            </span>

                            <span class="text-2xl font-black opacity-80 pb-1">
                                {{ $derniereInsc->classe->nom }}
                            </span>
                        @else
                            <span class="text-2xl font-black italic uppercase">Non Inscrit</span>
                        @endif
                    </div>

                    {{-- <div class="mt-4 pt-4 border-t border-black/10 flex justify-between items-center">
                        <span class="text-[10px] font-bold uppercase">Statut : Occupé</span>
                        <div class="flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-black"></span>
                            <span class="text-[10px] font-black uppercase">Salle {{ $eleve->salle_nom ?? 'A1' }}</span>
                        </div>
                    </div> --}}
                </div>
            </div>

            <div class="md:col-span-2 space-y-6">

                <div class="bg-card border border-border rounded-3xl p-8 shadow-sm">
                    <div class="flex items-center gap-4 mb-6">
                        <h4 class="text-xs font-black  tracking-widest opacity-40">État Civil de l'élève</h4>
                        <div class="h-px flex-1 bg-border/50"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <label class="text-[10px]  text-muted-foreground  tracking-wider">Date de
                                naissance</label>
                            <p class="text-sm ">
                                {{ \Carbon\Carbon::parse($eleve->date_naissance)->translatedFormat('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="text-[10px]  text-muted-foreground  tracking-wider">Lieu de
                                naissance</label>
                            <p class="text-sm  ">{{ $eleve->lieu_naissance }}</p>
                        </div>
                        <div>
                            <label class="text-[10px]  text-muted-foreground  tracking-wider">Genre / Sexe</label>
                            <p class="text-sm  ">{{ $eleve->sexe == 'M' ? 'Masculin' : 'Féminin' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px]  text-muted-foreground tracking-wider">Redoublant</label>
                            <p class="text-sm italic text-{{ $eleve->statut ? 'red' : 'green' }}-600">
                                {{ $eleve->statut ? 'Yes' : 'NO' }}
                            </p>
                        </div>
                        {{-- <div>
                            <label class="text-[10px] font-bold text-muted-foreground  tracking-wider">Tranche d'âge</label>
                            <p class="text-sm font-black  text-primary">
                                {{ \Carbon\Carbon::parse($eleve->date_naissance)->age }} ans</p>
                        </div> --}}

                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tranche d'âge</p>
                            <div class="flex items-center gap-2">
                                <p class="text-lg font-black text-[#00BFFF]">{{ $eleve->age }} ans</p>
                                <span
                                    class="text-[9px] px-2 py-0.5 bg-[#00BFFF]/10 text-[#00BFFF] rounded-full font-bold uppercase">
                                    {{ $eleve->age >= 18 ? 'Majeur' : 'Mineur' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-card border border-border rounded-3xl p-8 shadow-sm">
                    <div class="flex items-center gap-4 mb-6">
                        <h4 class="text-xs font-black  tracking-widest opacity-40 text-blue-500">Contact & Urgence</h4>
                        <div class="h-px flex-1 bg-border/50"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-8">
                        <div class="flex items-center gap-4 p-4 bg-secondary/30 rounded-2xl border border-border">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <p class="text-sm font-black tracking-widest">
                                    {{ $eleve->telephone_parent ?? 'Non renseigné' }}
                                </p>


                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-4 bg-secondary/30 rounded-2xl border border-border">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold  opacity-50">Adresse / Quartier</p>

                                <p class="text-sm font-black uppercase tracking-tighter">
                                    {{ $eleve->adresse ?? 'Non spécifié' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
