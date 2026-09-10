@extends('layouts.admin.admin-layout')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-black  text-foreground">Gestion des Classes</h1>
            <p class="text-xs text-muted-foreground   tracking-tighter">Année en cours :
                {{ $anneeActive->libelle }}</p>
        </div>
    </div>





    <div class="lg:col-span-3 pb-4">
        <div class="bg-card rounded-xl border border-border overflow-hidden">
            <div class="bg-secondary/30 px-4 py-3 border-b border-border flex justify-between items-center">
                <span class="text-xs font-black uppercase text-foreground">Liste des Classes</span>
                <span class="text-[9px] bg-primary/10 text-primary px-2 py-0.5 rounded-full font-bold uppercase">
                    {{ $classes->count() }} Classe(s)
                </span>
            </div>


            <div class="p-3 space-y-2">
                @forelse($classes as $classe)
                    <div class="flex justify-between items-center bg-background p-3 rounded-xl border border-border group">

                        {{-- Informations de la classe --}}
                        <div class="flex flex-col flex-1 min-w-0">
                            <span class="text-sm font-bold text-foreground">
                                {{ $classe->nom }}
                            </span>

                            <span class="text-[10px] text-muted-foreground">
                                {{ $classe->cycle?->nom ?? 'Sans cycle' }} -
                                {{ ucfirst($classe->section ?? 'hh') }}
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
                                        <x-lucide-alert-triangle class="w-3 h-3" />
                                        Aucun programme défini
                                    </span>
                                @endforelse
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="relative ml-3 shrink-0 z-1000">

                            {{-- Bouton 3 points --}}
                            <button type="button"
                                class="class-action-btn flex items-center justify-center w-8 h-8 rounded-full
                           text-muted-foreground hover:text-foreground hover:bg-secondary
                           transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20"
                                aria-label="Actions" aria-expanded="false">

                                <x-lucide-more-vertical class="w-4 h-4" />
                            </button>

                            {{-- Menu --}}
                            <div
                                class="class-action-menu hidden absolute right-4 bottom-[40%] mt-1 w-48
                           bg-background border border-border rounded-xl shadow-lg
                           z-10000 overflow-hidden">

                                {{-- Configurer settings.classes.matieres.edit  --}}
                                <a href="{{ route('settings.classes.matieres.edit', $classe) }}"
                                    class="flex items-center gap-3 px-3 py-2.5 text-xs font-medium
                               text-foreground hover:bg-secondary transition-colors">

                                    <x-lucide-book-open class="w-4 h-4 text-primary shrink-0" />

                                    <span>Configurer le programme</span>
                                </a>

                                {{-- Modifier --}}
                                <a href="{{ route('settings.classes.edit', $classe) }}"
                                    class="flex items-center gap-3 px-3 py-2.5 text-xs font-medium
                               text-foreground hover:bg-secondary transition-colors">

                                    <x-lucide-edit-3 class="w-4 h-4 text-muted-foreground shrink-0" />

                                    <span>Modifier la classe</span>
                                </a>

                                {{-- Séparateur --}}
                                <div class="border-t border-border"></div>

                                {{-- Supprimer --}}
                                <form action="{{ route('settings.classes.destroy', $classe) }}" method="POST"
                                    onsubmit="return confirm('Voulez-vous vraiment supprimer cette classe ?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="w-full flex items-center gap-3 px-3 py-2.5
                                   text-xs font-medium text-red-500
                                   hover:bg-red-500/10 transition-colors text-left">

                                        <x-lucide-trash-2 class="w-4 h-4 shrink-0" />

                                        <span>Supprimer la classe</span>
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-[10px] italic text-muted-foreground text-center py-4">
                        Aucune classe enregistrée.
                    </p>
                @endforelse
            </div>
        </div>
    </div>

@endsection
