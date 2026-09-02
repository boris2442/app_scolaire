@extends('layouts.admin.admin-layout')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-black  text-foreground">Gestion des Classes</h1>
            <p class="text-xs text-muted-foreground   tracking-tighter">Année en cours :
                {{ $anneeActive->libelle }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">



        <div class="lg:col-span-3">
            <div class="bg-card rounded-xl border border-border overflow-hidden">
                <div class="bg-secondary/30 px-4 py-3 border-b border-border flex justify-between items-center">
                    <span class="text-xs font-black uppercase text-foreground">Liste des Classes</span>
                    <span class="text-[9px] bg-primary/10 text-primary px-2 py-0.5 rounded-full font-bold uppercase">
                        {{ $classes->count() }} Classe(s)
                    </span>
                </div>
                <div class="p-3 space-y-2">
                    @forelse($classes as $classe)
                        <div
                            class="flex justify-between items-center bg-background p-3 rounded-xl border border-border group">

                            <div class="flex flex-col flex-1">
                                <span class="text-sm font-bold text-foreground">{{ $classe->nom }}</span>
                                <span class="text-[10px] text-muted-foreground">
                                   {{ $classe->cycle?->nom ?? 'Sans cycle' }} - {{ ucfirst($classe->section ?? 'hh') }}
                                </span>

                                <div class="flex flex-wrap gap-1 mt-1">
                                    @forelse($classe->matieres as $m)
                                        <span
                                            class="text-[9px] bg-secondary px-1.5 py-0.5 rounded border border-border text-muted-foreground font-medium uppercase">
                                            {{ $m->code }} ({{ $m->pivot->coefficient }})
                                        </span>
                                    @empty
                                        <span
                                            class="text-[9px] text-red-500 font-bold uppercase italic flex items-center gap-1">
                                            <x-lucide-alert-triangle class="w-3 h-3" /> Aucun programme défini
                                        </span>
                                    @endforelse
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('settings.classes.matieres.edit', $classe) }}"
                                    class="text-primary hover:bg-primary/10 p-2 rounded-lg opacity-0 group-hover:opacity-100 transition-all flex items-center gap-1"
                                    title="Configurer le programme">
                                    <span class="text-[10px] font-black">
                                        Configurer
                                    </span>
                                    <x-lucide-book-open class="w-4 h-4" />
                                </a>
                                <a href="{{ route('settings.classes.edit', $classe) }}"
                                    class="text-primary hover:bg-primary/10 p-2 rounded-lg opacity-0 group-hover:opacity-100 transition-all flex items-center gap-1"
                                    title="Modifier la classe">
                                    <span class="text-[10px] font-black">
                                        Modifier
                                    </span>
                                    <x-lucide-edit-3 class="w-4 h-4" />
                                </a>

                                <form action="{{ route('settings.classes.destroy', $classe) }}" method="POST"
                                    onsubmit="return confirm('Supprimer ?')">
                                    @csrf @method('DELETE')
                                    <button
                                        class="text-muted-foreground hover:text-danger p-2 opacity-0 group-hover:opacity-100 transition-all"
                                        title="Supprimer la classe {{ $classe->nom }}">
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-[10px] italic text-muted-foreground text-center py-4">Aucune classe enregistrée.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
