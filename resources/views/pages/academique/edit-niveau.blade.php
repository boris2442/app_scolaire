@extends('layouts.admin.admin-layout')

@section('content')
    <div class="max-w-xl mx-auto">
        {{-- Retour à la liste --}}
        <a href="{{ route('settings.academique.index') }}"
            class="text-xs text-primary font-bold uppercase mb-4 inline-block hover:underline">
            <i class="fas fa-arrow-left mr-1"></i> Retour à la structure
        </a>

        <div class="bg-card p-6 rounded-xl border border-border shadow-sm">
            <div class="flex items-center gap-3 mb-6 border-b border-border pb-4">
                <div class="w-10 h-10 bg-primary/10 text-primary rounded-full flex items-center justify-center">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <h2 class="text-sm font-black uppercase tracking-widest text-foreground">Modifier le Niveau</h2>
                    <p class="text-[10px] text-muted-foreground uppercase font-bold tracking-tighter">Édition de :
                        {{ $niveau->nom }}</p>
                </div>
            </div>

            <form action="{{ route('settings.academique.niveaux.update', $niveau) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Sélection du Cycle --}}
                <div>
                    <label class="text-[10px] font-black uppercase text-muted-foreground tracking-widest">Appartient au
                        Cycle</label>
                    <select name="cycle_id"
                        class="w-full bg-secondary border-border rounded-lg text-sm px-3 py-2.5 mt-1 focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        @foreach ($cycles as $cycle)
                            <option value="{{ $cycle->id }}" {{ $niveau->cycle_id == $cycle->id ? 'selected' : '' }}>
                                {{ $cycle->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('cycle_id')
                        <p class="text-[10px] text-danger mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nom du Niveau --}}
                <div>
                    <label class="text-[10px] font-black uppercase text-muted-foreground tracking-widest">Nom du Niveau (ex:
                        3ème)</label>
                    <input type="text" name="nom" value="{{ old('nom', $niveau->nom) }}"
                        class="w-full bg-secondary border-border rounded-lg text-sm px-3 py-2.5 mt-1 focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('nom') border-danger @enderror">
                    @error('nom')
                        <p class="text-[10px] text-danger mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-primary text-white font-bold py-3 rounded-lg hover:opacity-90 shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                    <a href="{{ route('settings.academique.index') }}"
                        class="px-6 bg-secondary text-foreground font-bold py-3 rounded-lg hover:bg-border transition-all text-sm flex items-center">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
