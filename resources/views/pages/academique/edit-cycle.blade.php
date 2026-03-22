@extends('layouts.admin.admin-layout')

@section('content')
<div class="max-w-xl mx-auto">
    <a href="{{ route('settings.academique.index') }}" class="text-xs text-primary font-bold uppercase mb-4 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Retour à la structure
    </a>

    <div class="bg-card p-6 rounded-xl border border-border shadow-sm">
        <h2 class="text-sm font-black uppercase mb-6 tracking-widest text-primary border-b border-border pb-3">
            Modifier le Cycle : {{ $cycle->nom }}
        </h2>

        <form action="{{ route('settings.academique.cycles.update', $cycle) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="text-xs font-medium uppercase text-muted-foreground">Nom du Cycle</label>
                <input type="text" name="nom" value="{{ old('nom', $cycle->nom) }}" 
                    class="w-full bg-secondary border-border rounded-lg text-sm px-3 py-2 mt-1 @error('nom') border-danger @enderror">
                @error('nom') <p class="text-[10px] text-danger mt-1">{{ $message }}</p> @enderror
            </div>

            <button class="w-full bg-primary text-white font-bold py-2.5 rounded-lg hover:opacity-90 shadow-lg shadow-primary/20 transition-all">
                Enregistrer les modifications
            </button>
        </form>
    </div>
</div>
@endsection
