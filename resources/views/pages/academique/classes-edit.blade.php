@extends('layouts.admin.admin-layout')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-black text-foreground">Modifier la Classe</h1>
            <p class="text-xs text-muted-foreground tracking-tighter">Mise à jour des informations de la classe :
                {{ $classe->nom }}</p>
        </div>
    </div>

    <div class="max-w-xl">
        <div class="bg-card p-6 rounded-xl border border-border shadow-sm">
            <h2 class="text-xs font-black text-primary mb-4 tracking-widest">Informations de la Classe</h2>

            <form action="{{ route('settings.classes.update', $classe->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-[10px] text-muted-foreground">Cycle concerné</label>
                    <select name="cycle_id" class="w-full bg-secondary border-border rounded-lg text-sm px-3 py-2 mt-1"
                        required>
                        <option value="">Sélectionner un cycle...</option>
                        @foreach ($cycles as $cycle)
                            <option value="{{ $cycle->id }}"
                                {{ old('cycle_id', $classe->cycle_id) == $cycle->id ? 'selected' : '' }}>
                                {{ $cycle->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[10px] text-muted-foreground">Nom de la Classe (ex: 6ème A, 3ème B)</label>
                    <input type="text" name="nom" value="{{ old('nom', $classe->nom) }}" placeholder="Ex: 6ème A"
                        class="w-full bg-secondary border-border rounded-lg text-sm px-3 py-2 mt-1" required>
                </div>
                <div>
                    <label class="text-[10px] text-muted-foreground">Section</label>
                    <select name="section" class="w-full bg-secondary border-border rounded-lg text-sm px-3 py-2 mt-1"
                        required>
                        <option value="">Sélectionner une section...</option>
                        <option value="francophone"
                            {{ old('section', $classe->section) == 'francophone' ? 'selected' : '' }}>
                            Subdivision Francophone 
                            </option>
                        <option value="anglophone"
                            {{ old('section', $classe->section) == 'anglophone' ? 'selected' : '' }}>
                            Subdivision Anglophone 
                            </option>
                    </select>
                        
                </div>


                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-primary text-white font-black py-2.5 rounded-lg hover:opacity-90 transition-all flex items-center justify-center gap-2 text-[10px]">
                        <x-lucide-check class="w-4 h-4" /> Enregistrer les modifications
                    </button>

                    <a href="{{ route('settings.classes.index') }}"
                        class="px-4 py-2.5 rounded-lg border border-border text-xs font-bold text-muted-foreground hover:bg-secondary transition-all">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
