@extends('layouts.admin.admin-layout')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground mb-1">Groupes de Matières</h1>
            <p class="text-sm text-gray-500">
                Configurez les catégories pour l'affichage des bulletins (ex: Littéraires, Scientifiques).
            </p>
        </div>
        <a href="{{ route('admin.groupes.create') }}"
            class="inline-flex items-center px-4 py-1 bg-primary text-white rounded font-bold text-sm hover:scale-105 transition-all shadow-lg shadow-primary/20">
            <i class="fas fa-layer-group mr-2"></i> Nouveau Groupe
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($groupes as $groupe)
            <div class="bg-card rounded-2xl border border-border p-5 hover:border-primary/30 transition-all group">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        {{-- Badge Ordre --}}
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-white bg-primary px-2 py-0.5 rounded-full">
                            Ordre : {{ $groupe->ordre }}
                        </span>
                        <h3 class="text-lg font-bold text-foreground mt-2">{{ $groupe->nom }}</h3>
                    </div>

                    {{-- Boutons Actions (ton JS toggleMenu fonctionne ici parfaitement) --}}
                    <button type="button" onclick="toggleMenu(this)"
                        class="menu-trigger p-2 rounded-full hover:bg-secondary text-gray-400">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div
                        class="menu-content hidden absolute right-0 mt-8 w-40 bg-card border border-border rounded-xl shadow-xl z-10">
                        {{-- Tes liens Modifier/Supprimer ici --}}
                    </div>
                </div>

                {{-- Détail dynamique --}}
                <div class="flex items-center text-xs text-gray-500 mb-4">
                    <i class="fas fa-book mr-2"></i>
                    {{ $groupe->matieres_count ?? 0 }} matières dans ce groupe
                </div>

                <div
                    class="pt-4 border-t border-border flex justify-between items-center text-[10px] text-gray-400 italic  tracking-wider">
                    <span>Dernière modif : {{ $groupe->updated_at->format('d/m/y') }}</span>
                </div>
            </div>
        @endforeach
    </div>
@endsection
