@extends('layouts.admin.admin-layout')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="space-y-6">
            <div class="bg-card p-6 rounded-xl border border-border shadow-sm text-center">
                <h2 class="text-xs font-black uppercase text-primary mb-4 tracking-widest">1. Ajouter un Cycle</h2>
                <form action="{{ route('settings.academique.cycles.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="nom" placeholder="ex: Premier Cycle"
                        class="flex-1 bg-secondary border-border rounded-lg text-sm px-3 py-2">
                    <button class="bg-primary text-white p-2 rounded-lg hover:opacity-90"><i
                            class="fas fa-plus"></i></button>
                </form>
            </div>

            <div class="bg-card p-6 rounded-xl border border-border shadow-sm">
                <h2 class="text-xs font-black uppercase text-primary mb-4 tracking-widest text-center">2. Ajouter un Niveau
                </h2>
                <form action="{{ route('settings.academique.niveaux.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <select name="cycle_id" class="w-full bg-secondary border-border rounded-lg text-sm px-3 py-2">
                        <option value="">Sélectionner le Cycle...</option>
                        @foreach ($cycles as $cycle)
                            <option value="{{ $cycle->id }}">{{ $cycle->nom }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="nom" placeholder="ex: 6ème"
                        class="w-full bg-secondary border-border rounded-lg text-sm px-3 py-2">
                    <button class="w-full bg-primary text-white font-bold py-2 rounded-lg hover:opacity-90 transition-all">
                        Enregistrer le Niveau
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <h2 class="text-sm font-bold uppercase mb-4 flex items-center gap-2 text-muted-foreground">
                <i class="fas fa-sitemap text-primary"></i> Structure des Études
            </h2>

            @forelse($cycles as $cycle)
                <div class="bg-card rounded-xl border border-border overflow-hidden">
                    {{-- <div class="bg-secondary/50 px-4 py-3 border-b border-border flex justify-between items-center">
                        <span class="text-sm font-black text-foreground uppercase tracking-wider">{{ $cycle->nom }}</span>
                        <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] rounded-full font-bold">
                            {{ $cycle->niveaux->count() }} Niveaux
                        </span>
                    </div> --}}

                    <div class="bg-secondary/50 px-4 py-3 border-b border-border flex justify-between items-center">
                        {{-- À gauche : Le nom et le badge --}}
                        <div class="flex items-center gap-3">
                            <span
                                class="text-sm font-black text-foreground uppercase tracking-wider">{{ $cycle->nom }}</span>
                            <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] rounded-full font-bold">
                                {{ $cycle->niveaux->count() }} Niveaux
                            </span>
                        </div>

                        {{-- À DROITE : TES BOUTONS D'ACTION (COLLE LE CODE ICI) --}}
                        <div class="flex items-center gap-2">
                            <a href="{{ route('settings.academique.cycles.edit', $cycle) }}"
                                class="text-gray-400 hover:text-primary transition-colors">
                                <i class="fas fa-pen text-[10px]"></i>
                            </a>

                            <form action="{{ route('settings.academique.cycles.destroy', $cycle) }}" method="POST"
                                onsubmit="return confirm('Attention: Cela supprimera aussi tous les niveaux de ce cycle. Continuer ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-danger transition-colors px-1">
                                    <i class="fas fa-times text-[12px]"></i>
                                </button>
                            </form>
                        </div>
                    </div>


                    <div class="p-4 flex flex-wrap gap-3">
                        @forelse($cycle->niveaux as $niveau)
                            <div
                                class="group flex items-center gap-3 bg-background border border-border px-3 py-2 rounded-lg hover:border-primary transition-all">
                                <span class="text-sm font-bold text-foreground">{{ $niveau->nom }}</span>
                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{-- Bouton Modifier (Ouvre un prompt simple pour l'exemple, ou tu peux faire une modale) --}}
                                    <a href="{{ route('settings.academique.niveaux.edit', $niveau) }}"
                                        class="p-1 text-primary hover:bg-primary/10 rounded transition-colors">
                                        <i class="fas fa-edit text-[10px]"></i>
                                    </a>

                                    {{-- Bouton Supprimer --}}
                                    <form action="{{ route('settings.academique.niveaux.destroy', $niveau) }}"
                                        method="POST" onsubmit="return confirm('Supprimer ce niveau ?')">
                                        @csrf @method('DELETE')
                                        <button class="p-1 text-danger hover:bg-danger/10 rounded">
                                            <i class="fas fa-trash text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs italic text-muted-foreground">Aucun niveau défini pour ce cycle.</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="bg-card p-12 rounded-xl border border-dashed border-border text-center">
                    <p class="text-muted-foreground italic">Commencez par créer votre premier Cycle (ex: Francophone).</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
