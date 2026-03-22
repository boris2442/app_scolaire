@extends('layouts.admin.admin-layout')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-black uppercase text-foreground">Gestion des Classes</h1>
            <p class="text-xs text-muted-foreground uppercase font-bold tracking-tighter">Année en cours :
                {{ $anneeActive->libelle }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        <div class="lg:col-span-1">
            <div class="bg-card p-6 rounded-xl border border-border shadow-sm sticky top-24">
                <h2 class="text-xs font-black uppercase text-primary mb-4 tracking-widest">Nouvelle Classe</h2>
                <form action="{{ route('settings.classes.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="annee_scolaire_id" value="{{ $anneeActive->id }}">

                    <div>
                        <label class="text-[10px] font-bold uppercase text-muted-foreground">Niveau</label>
                        <select name="niveau_id" class="w-full bg-secondary border-border rounded-lg text-sm px-3 py-2 mt-1"
                            required>
                            <option value="">Choisir un niveau...</option>
                            @foreach ($niveaux as $niv)
                                <option value="{{ $niv->id }}">{{ $niv->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold uppercase text-muted-foreground">Nom/Suffixe (ex: A, B ou
                            Bilingue)</label>
                        <input type="text" name="nom" placeholder="Ex: A"
                            class="w-full bg-secondary border-border rounded-lg text-sm px-3 py-2 mt-1" required>
                    </div>

                    <button
                        class="w-full bg-primary text-white font-black py-2.5 rounded-lg hover:opacity-90 transition-all flex items-center justify-center gap-2 uppercase text-[10px]">
                        <i class="fas fa-plus"></i> Créer la Classe
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($niveaux as $niv)
                <div class="bg-card rounded-xl border border-border overflow-hidden h-fit">
                    <div class="bg-secondary/30 px-4 py-2 border-b border-border flex justify-between items-center">
                        <span class="text-xs font-black uppercase text-foreground">{{ $niv->nom }}</span>
                        <span class="text-[9px] bg-primary/10 text-primary px-2 py-0.5 rounded-full font-bold uppercase">
                            {{ $niv->classes->count() }} Section(s)
                        </span>
                    </div>
                    <div class="p-3">
                        @forelse($niv->classes as $classe)
                            <div
                                class="flex justify-between items-center bg-background p-2 rounded-lg border border-border mb-2 last:mb-0 group">
                                <span class="text-sm font-bold text-foreground">{{ $niv->nom }} {{ $classe->nom }}</span>
                                {{-- <span class="text-sm font-bold text-foreground">
                                    {{ $classe->nom_complet }}
                                </span> --}}
                                <form action="{{ route('settings.classes.destroy', $classe) }}" method="POST"
                                    onsubmit="return confirm('Supprimer cette classe ?')">
                                    @csrf @method('DELETE')
                                    <button
                                        class="text-muted-foreground hover:text-danger opacity-0 group-hover:opacity-100 transition-all">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-[10px] italic text-muted-foreground text-center py-2">Aucune classe créée pour ce
                                niveau.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
