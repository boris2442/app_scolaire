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
     
            <x-lucide-layers class='mr-2 w-4 h-4' />
             Nouveau Groupe
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
                        <x-lucide-more-horizontal class='w-4 h-4' />
                    </button>
                    <div
                        class="menu-content hidden absolute right-0 mt-8 w-40 bg-card border border-border rounded-xl shadow-xl z-10">
                        {{-- Tes liens Modifier/Supprimer ici --}}
                        <a href="{{ route('admin.groupes.edit', $groupe->id) }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <x-lucide-edit class='mr-2 w-4 h-4' />
                            Modifier
                        </a>
                        <form action="{{ route('admin.groupes.destroy', $groupe->id) }}" method="POST"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <x-lucide-trash class='mr-2 w-4 h-4' />
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Détail dynamique --}}
                <div class="flex items-center text-xs text-gray-500 mb-4">
                  
                    <x-lucide-book class='mr-2 w-4 h-4' />
                    {{ $groupe->matieres_count ?? 0 }} matières dans ce groupe
                </div>

                <div
                    class="pt-4 border-t border-border flex justify-between items-center text-[10px] text-gray-400 italic  tracking-wider">
                    <span>Dernière modif : {{ $groupe->updated_at->format('d/m/y') }}</span>
                </div>
            </div>
        @endforeach
    </div>

   <script>
        function toggleMenu(button) {
            const menuContent = button.nextElementSibling;
            menuContent.classList.toggle('hidden');
        }

        // Fermer le menu si on clique en dehors
        document.addEventListener('click', function (event) {
            const menus = document.querySelectorAll('.menu-content');
            menus.forEach(menu => {
                if (!menu.contains(event.target) && !menu.previousElementSibling.contains(event.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
