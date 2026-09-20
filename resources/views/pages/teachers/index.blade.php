@extends('layouts.admin.admin-layout')

@section('content')
    {{-- En-tête --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-black text-foreground">
                Personnel Enseignant
            </h1>

            <p class="text-[10px] text-muted-foreground font-bold tracking-widest">
                Gestion des comptes et profils instructeurs
            </p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.teachers.export') }}"
                class="bg-primary text-white px-6 py-3 rounded font-black text-[10px] tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all flex items-center">
                <x-lucide-download class="mr-2 w-4 h-4" />
                <span class="hidden sm:inline">Exporter en Excel</span>
            </a>
            @can('access-admin')
                <a href="{{ route('admin.enseignants.create') }}"
                    class="bg-primary text-white px-6 py-3 rounded font-black text-[10px] tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all flex items-center">
                    <x-lucide-plus class="mr-2 w-4 h-4" />
                    <span class="hidden sm:inline">Nouveau</span>
                </a>
            @endcan
        </div>
    </div>


    {{-- Notification de création + identifiants --}}
    @if (session('success') && session('credentials'))
        <div class="mt-6 rounded-2xl border border-[var(--border)] bg-[var(--card)] p-5 shadow-sm">

            {{-- Notification --}}
            <div class="flex items-start gap-3">

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[color-mix(in_srgb,var(--success)_12%,transparent)]">
                    <x-lucide-circle-check class="h-5 w-5 text-[var(--success)]" />
                </div>

                <div>
                    <h3 class="text-sm font-black text-[var(--foreground)]">
                        {{ session('success') }}
                    </h3>

                    <p class="mt-1 text-xs font-medium text-[color-mix(in_srgb,var(--foreground)_65%,transparent)]">
                        Le compte de l'enseignant a été créé avec succès.
                    </p>
                </div>

            </div>

            {{-- Carte des identifiants --}}
            <div class="mt-5 rounded-xl border border-[var(--border)] bg-[var(--background)] p-5">

                <div class="mb-5">
                    <h4 class="text-xs font-black uppercase tracking-widest text-[var(--foreground)]">
                        Identifiants de connexion
                    </h4>

                    <p class="mt-1 text-[11px] text-[color-mix(in_srgb,var(--foreground)_55%,transparent)]">
                        Communiquez ces informations à l'enseignant.
                    </p>
                </div>

                {{-- Login --}}
                <div class="mb-4">

                    <label
                        class="mb-1.5 block text-[10px] font-black  tracking-wider text-[color-mix(in_srgb,var(--foreground)_55%,transparent)]">
                        Login
                    </label>

                    <div class="flex items-center gap-2">

                        <div class="flex-1 rounded-lg border border-[var(--border)] bg-[var(--card)] px-3 py-2.5">
                            <span id="teacher-login" class="font-mono text-sm font-bold text-[var(--foreground)]">
                                {{ session('credentials.phone') }}
                            </span>
                        </div>

                        <button type="button" onclick="copyCredential('teacher-login', this)"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-[var(--border)] bg-[var(--card)] text-[color-mix(in_srgb,var(--foreground)_65%,transparent)] transition hover:bg-[var(--secondary)] hover:text-[var(--foreground)]"
                            title="Copier le login">

                            <x-lucide-copy class="h-4 w-4" />

                        </button>

                    </div>

                </div>

                {{-- Mot de passe --}}
                <div>

                    <label
                        class="mb-1.5 block text-[10px] font-black  tracking-wider text-[color-mix(in_srgb,var(--foreground)_55%,transparent)]">
                        Mot de passe
                    </label>

                    <div class="flex items-center gap-2">

                        <div class="flex-1 rounded-lg border border-[var(--border)] bg-[var(--card)] px-3 py-2.5">

                            <span id="teacher-password"
                                class="font-mono text-sm font-bold tracking-wider text-[var(--foreground)]">
                                {{ session('credentials.password') }}
                            </span>

                        </div>

                        <button type="button" onclick="copyCredential('teacher-password', this)"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-[var(--border)] bg-[var(--card)] text-[color-mix(in_srgb,var(--foreground)_65%,transparent)] transition hover:bg-[var(--secondary)] hover:text-[var(--foreground)]"
                            title="Copier le mot de passe">

                            <x-lucide-copy class="h-4 w-4" />

                        </button>

                    </div>

                </div>

                {{-- Avertissement --}}
                <div
                    class="mt-5 flex items-start gap-2 rounded-lg border border-[color-mix(in_srgb,var(--warning)_25%,transparent)] bg-[color-mix(in_srgb,var(--warning)_10%,transparent)] p-3">

                    <x-lucide-triangle-alert class="mt-0.5 h-4 w-4 shrink-0 text-[var(--warning)]" />

                    <p
                        class="text-[11px] font-medium leading-relaxed text-[color-mix(in_srgb,var(--foreground)_75%,transparent)]">
                        Conservez soigneusement ces identifiants et transmettez-les à l'enseignant.
                        {{-- Le mot de passe n'est pas enregistré en clair dans la base de données. --}}
                    </p>

                </div>

            </div>

        </div>
    @endif


    {{-- Script pour copier les identifiants --}}
    <script>
        function copyCredential(elementId, button) {

            const element = document.getElementById(elementId);

            if (!element) {
                return;
            }

            const text = element.innerText.trim();

            navigator.clipboard.writeText(text).then(() => {

                const originalContent = button.innerHTML;

                button.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M20 6 9 17l-5-5"/>
                </svg>
            `;

                button.classList.add('text-green-600');

                setTimeout(() => {

                    button.innerHTML = originalContent;
                    button.classList.remove('text-green-600');

                }, 1500);

            }).catch(() => {

                alert('Impossible de copier automatiquement.');

            });
        }
    </script>



    <div class="bg-card rounded-2xl border border-border shadow-sm overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[650px]">
            <thead>
                <tr class="bg-secondary/50 border-b border-border">
                    <th class="p-4 text-[10px] font-black  text-muted-foreground rounded-tl-2xl">Enseignant</th>
                    <th class="p-4 text-[10px] font-black  text-muted-foreground">Matricule</th>
                    <th class="p-4 text-[10px] font-black  text-muted-foreground">Département</th>
                    <th class="p-4 text-[10px] font-black  text-muted-foreground">Statut Compte</th>

                    @can('access-admin')
                        <th class="p-4 text-[10px] font-black  text-muted-foreground text-right rounded-tr-2xl">Actions
                        </th>
                    @endcan
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
                        @can('access-admin')
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
                        @endcan
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
