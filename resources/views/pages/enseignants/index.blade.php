@extends('layouts.admin.admin-layout')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-xl font-black  text-foreground">Personnel Enseignant</h1>
            <p class="text-[10px] text-muted-foreground font-bold  tracking-widest">Gestion des comptes et profils
                instructeurs</p>
        </div>
        <a href="{{ route('admin.enseignants.create') }}"
            class="bg-primary text-white px-6 py-3 rounded-xl font-black  text-[10px] tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">

            <x-lucide-plus class='mr-2 w-4 h-4' />
            Ajouter
        </a>
    </div>

    <div class="bg-card rounded-2xl border border-border shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-secondary/50 border-b border-border">
                    <th class="p-4 text-[10px] font-black  text-muted-foreground rounded-tl-2xl">Enseignant</th>
                    <th class="p-4 text-[10px] font-black  text-muted-foreground">Matricule</th>
                    <th class="p-4 text-[10px] font-black  text-muted-foreground">Département</th>
                    <th class="p-4 text-[10px] font-black  text-muted-foreground">Statut Compte</th>
                    <th class="p-4 text-[10px] font-black  text-muted-foreground text-right rounded-tr-2xl">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($enseignants as $enseignant)
                    <tr class="hover:bg-secondary/10 transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-black text-xs">
                                    {{ substr($enseignant->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black ">{{ $enseignant->user->name }}</p>
                                    <p class="text-[10px] text-muted-foreground">{{ $enseignant->user->phone }}</p>
                                    <p class="text-[10px] italic font-bold text-muted-foreground">
                                        {{ $enseignant->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-xs font-bold text-primary">{{ $enseignant->matricule }}</td>
                        <td class="p-4">
                            @if ($enseignant->departement)
                                <span
                                    class="text-[10px] font-black  bg-primary/5 text-primary border border-primary/10 px-2 py-1 rounded">
                                    {{ $enseignant->departement->nom }}
                                </span>
                            @else
                                <span
                                    class="text-[10px] font-black  bg-red-50 text-red-500 border border-red-100 px-2 py-1 rounded">
                                    Département manquant
                                </span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span
                                class="inline-flex items-center gap-1.5 py-1 px-2 rounded-full text-[9px] font-black  bg-[var(--success)]/10 text-[var(--success)]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--success)]"></span>
                                Actif
                            </span>
                        </td>

                        <td class="p-4 text-right relative px-6">
                            <div class="inline-block text-left">

                                <button type="button" onclick="toggleDropdown(event, this)"
                                    class="p-2 rounded-md text-[var(--secondary-foreground)] hover:bg-[var(--secondary)] transition focus:outline-none relative">


                                    <x-lucide-more-horizontal class="w-4 h-4" />
                                </button>

                                <div
                                    class="dropdown-menu hidden absolute right-0 mt-2 w-44 rounded-xl shadow-xl border border-[var(--border)] 
            bg-[var(--card)] text-[var(--card-foreground)] z-50 overflow-hidden text-left">

                                    <a href="{{ route('admin.enseignants.show', $enseignant) }}"
                                        class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-[var(--secondary)] transition">

                                        <x-lucide-eye class='text-[var(--primary)] w-4 h-4' />
                                        Voir plus
                                    </a>

                                    <a href="{{ route('admin.enseignants.edit', $enseignant) }}"
                                        class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-[var(--secondary)] transition">

                                        <x-lucide-edit class='text-[var(--primary)] w-4 h-4' />
                                        Éditer
                                    </a>

                                    <div class="border-t border-[var(--border)] my-1"></div>

                                    <form action="{{ route('admin.enseignants.destroy', $enseignant) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet enseignant ?')"
                                            class="w-full flex items-center gap-3 px-4 py-2 text-sm text-[var(--danger)] hover:bg-red-50 dark:hover:bg-red-900/20 transition text-left">

                                            <x-lucide-trash class='w-4 h-4' />
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-muted-foreground rounded-b-2xl">
                            <p class="text-[10px] font-black  tracking-widest">Aucun enseignant enregistré pour le
                                moment</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        function toggleDropdown(event, button) {
            // Bloque la fermeture immédiate par le clic global
            event.stopPropagation();

            // Trouve le menu juste après le bouton cliqué
            const currentMenu = button.nextElementSibling;

            // Ferme TOUS les autres menus ouverts sur la page
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu !== currentMenu) {
                    menu.classList.add('hidden');
                }
            });

            // Alterne l'affichage du menu actuel
            currentMenu.classList.toggle('hidden');
        }

        // Ferme le menu si on clique n'importe où ailleurs sur la page
        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        });
    </script>
@endsection
