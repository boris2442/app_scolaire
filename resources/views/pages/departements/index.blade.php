@extends('layouts.admin.admin-layout')

@section('content')
    <h1 class="text-2xl font-bold text-foreground mb-4">Départements Scolaire</h1>
    <p class="text-sm text-gray-500 mb-6">Gérez les différentes
        structures scolaire de votre établissement. Ajoutez, modifiez ou supprimez des départements selon les besoins.
    </p>

    <div class="">
        <a href="{{ route('admin.departments.create') }}"
            class="inline-flex items-center px-6 py-3 bg-primary text-white rounded font-bold text-sm tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all mb-6">
            <x-lucide-plus class="w-4 h-4 mr-2" />
            Ajouter un Département
        </a>
        <a href="{{ route('admin.departments.export') }}"
            class="inline-flex items-center px-6 py-3 bg-primary text-white rounded font-bold text-sm tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all mb-6">
            <x-lucide-file-up class="w-4 h-4 mr-2" />
            Exporter en excel
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($departements as $dept)
            <div
                class="bg-card rounded-2xl border border-border p-5 hover:shadow-lg hover:border-primary/30 transition-all duration-300 group relative">

                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-primary bg-primary/10 px-2 py-0.5 rounded">
                            {{ $dept->code }}
                        </span>
                        <h3 class="text-lg font-bold text-foreground mt-2 group-hover:text-primary transition-colors">
                            {{ $dept->nom }}
                        </h3>
                    </div>



                    <div class="relative inline-block text-left">
                        <button type="button" onclick="toggleMenu(this)"
                            class="menu-trigger p-2 rounded-full hover:bg-secondary text-gray-400 hover:text-foreground transition-colors">
                          <x-lucide-more-vertical class="w-5 h-5" />
                        </button>

                        <div
                            class="menu-content hidden absolute right-0 mt-2 w-48 rounded-xl bg-card border border-border shadow-xl z-50 overflow-hidden">
                            <div class="py-1">
                                <a href="{{ route('admin.departments.edit', $dept->id) }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-primary/5 hover:text-primary transition-colors">
                                    Modifier
                                </a>

                                <button type="button" onclick="confirmDelete({{ $dept->id }})"
                                    class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    Supprimer
                                </button>

                                <form action="{{ route('admin.departments.destroy', $dept->id) }}" method="POST"
                                    id="delete-form-{{ $dept->id }}" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>





                </div>

                <p class="text-gray-500 text-sm line-clamp-2 mb-4 h-10">
                    {{ $dept->description ?? 'Aucune description fournie.' }}
                </p>

                <div
                    class="pt-4 border-t border-border flex justify-between items-center text-[10px] text-gray-400  font-semibold italic tracking-widest">
                    <div class="flex items-center">
                        <x-lucide-calendar class="w-3 h-3 mr-1" />
                        Créé le : {{ \Carbon\Carbon::parse($dept->created_at)->format('d/m/Y') }}
                    </div>
                    {{-- <div class="flex items-center">
                        <div
                            class="w-5 h-5 rounded-full bg-secondary flex items-center justify-center mr-1 text-[8px] text-primary">
                            {{ substr($dept->createur_nom ?? 'A', 0, 1) }}
                        </div>
                        Par : {{ $dept->createur_nom ?? 'Admin' }}
                    </div> --}}
                </div>

            </div>
        @endforeach
    </div>
    <script>
        function confirmDelete(id) {
            if (confirm('Voulez-vous vraiment supprimer ce département ?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }


        function toggleMenu(button) {
            // 1. Trouver le menu associé au bouton (il est juste après le bouton)
            const menu = button.nextElementSibling;

            // 2. Fermer tous les autres menus ouverts sur la page
            document.querySelectorAll('.menu-content').forEach(el => {
                if (el !== menu) el.classList.add('hidden');
            });

            // 3. Basculer l'état du menu cliqué
            menu.classList.toggle('hidden');
        }

        // 4. Fermer le menu si on clique n'importe où ailleurs sur la page
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.menu-trigger')) {
                document.querySelectorAll('.menu-content').forEach(el => {
                    el.classList.add('hidden');
                });
            }
        });
    </script>
@endsection
