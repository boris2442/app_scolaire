<aside id="sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-4 pb-20 transition-all duration-300 border-r bg-card text-foreground border-border -translate-x-full md:translate-x-0 overflow-y-auto flex flex-col"
    data-collapsed="false">

    <div class="absolute top-6 -right-3 hidden md:block">
        <button id="toggle-collapse"
            class="flex items-center justify-center w-6 h-6 rounded-full border border-border bg-primary text-primary-foreground shadow-sm hover:scale-110 transition-transform">

            <x-lucide-chevron-left class="w-4 h-4" id="collapse-icon" />
        </button>
    </div>

    <!-- 1. Ton bloc Logo actuel -->
    <div class="flex items-center px-6 mb-8 gap-3">
        <div class="flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="logo" class="w-10 h-10 rounded-full shadow-sm">
        </div>
        <div class="sidebar-label transition-opacity duration-300">
            <h1 class="font-bold text-lg leading-none tracking-tight">ACADEMIA<span
                    class="text-primary text-xs ml-1">PRO</span></h1>
            <p class="text-[10px] text-muted-foreground uppercase tracking-widest mt-1">Management System</p>
        </div>
    </div>

    <!-- 2. COLLE TON BLOC UTILISATEUR ICI -->
    <div class="flex items-center px-6 py-4 mb-4 gap-3 border-b border-border/50">
        <div class="flex-shrink-0">
            <a href="{{ route('profile.edit') }}" title="Voir le profil" aria-label="Voir le profil">
                @if (auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar"
                        class="w-12 h-12 rounded-full object-cover border-2 border-primary shadow-sm">
                @else
                    <div
                        class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm border-2 border-primary shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                @endif
            </a>
        </div>
        <div class="sidebar-label transition-opacity duration-300">
            <p class="text-[11px] text-muted-foreground leading-none">Bienvenue,</p>
            <h2 class="font-bold text-sm tracking-tight text-foreground mt-1 truncate max-w-[140px]">
                <a href="{{ route('profile.edit') }}" title="Voir le profil" aria-label="Voir le profil">
                    {{ auth()->user()->name }}
                </a>
            </h2>
            <p class="text-[10px] text-primary uppercase  mt-1 font-bold">
                {{ auth()->user()->role ?? 'UTILISATEUR' }}
            </p>
        </div>
    </div>

    <!-- 3. Ta navigation commence ici -->





    <nav class="px-3 space-y-1">
        <div class="sidebar-label px-3 py-2 text-[10px] font-semibold text-gray-500  tracking-wider">
            Tableau de Bord
        </div>
        <ul>
            <li>


                <a href="{{ route('home') }}" title="Accueil" aria-label="Accueil"
                    class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('home') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                    <x-lucide-home class="w-4 h-4 text-center" />
                    <span class="sidebar-label ml-3 font-medium">Accueil</span>
                </a>
            </li>
            @can('access-admin')
                <li>


                    <a href="{{ route('settings.index') }}" title="Parametres Ecole"
                        aria-label="Configuration des parametres de l ecole"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('settings.index') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-settings class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3 font-medium">Paramètres École</span>
                    </a>
                </li>
                <div class="sidebar-label px-3 py-2 text-[10px] font-semibold text-gray-500  tracking-wider mt-4">
                    Scolarité
                </div>
                <li>

                    <a href="{{ route('settings.years.index') }}" aria-label="Gestion des Années scolaires"
                        title="Anees Scolaires"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('settings.years.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        {{-- <i class="fas fa-calendar-alt w-6 text-center"></i> --}}
                        <x-lucide-camera class=" w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Années</span>
                    </a>
                </li>


                {{-- <li>
                    <a href="{{ route('admin.resultats.index') }}" title="Calcul des Résultats"
                        aria-label="Calcul des Résultats"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.resultats.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-calculator class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Calcul des Résultats</span>
                    </a>
                </li> --}}

                {{-- <li>
                    <a href="{{ route('admin.statistiques.index') }}" title="Statistiques" aria-label="Statistiques"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.statistiques.index') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-bar-chart-2 class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Statistiques</span>
                    </a>
                </li> --}}
                {{-- <li>
                    <a href="{{ route('emplois.index') }}" title="Emplois de temps" aria-label="Emplois de temps"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('emplois.index') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-bar-chart-2 class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Gestion des EDT</span>
                    </a>
                </li> --}}
            @endcan
            @canany(['access-admin', 'access-censeur', 'access-secretaire'])
                <li>
                    <a href="{{ route('admin.departments.index') }}" title="Départements" aria-label="Départements"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.departments.index') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-building class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Départements</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.creneaux.index') }}" title="Creneaux emploies de temps"
                        aria-label="Creneaux emploies de temps"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.creneaux.index') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-clock class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Creneaux</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.emplois.classes') }}" title=" emploies de temps"
                        aria-label=" emploies de temps"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.emplois.classes') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-clock class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Emplois de temps </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('avancement.index') }}" title="Evaluations" aria-label="Evaluations"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('avancement.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-book-open-check class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Suivie des cours</span>
                    </a>
                </li>


                <li>
                    <a href="{{ route('admin.users.index') }}" title="Utilisateurse" aria-label="Utilisateurse"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.users.index') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-users class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Utilisateurs</span>
                    </a>
                </li>





                <li>
                    <a href="{{ route('admin.groupes.index') }}"title="groupement des matieres "
                        aria-label="groupement des matieres"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.groupes.index') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }} text-sm">
                        <x-lucide-users class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">groupement matieres</span>
                    </a>
                </li>











                <li>

                    <a href="{{ route('settings.academique.index') }}" title="Niveaux et cycles"
                        aria-label="Niveaux et cycles"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('settings.academique.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">

                        <x-lucide-tree-pine class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Cycles & Niveaux</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings.classes.index') }}" title="Clases et salles" aria-label="Clases et salles"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('settings.classes.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        {{-- <i class="fas fa-school w-6 text-center"></i> --}}
                        <x-lucide-building class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Classes & Salles</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings.matieres.index') }}" title="Matières & Coeffs"
                        aria-label="Matières & Coeffs"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('settings.matieres.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-book class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Matières & Coeffs</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.students.index') }}" title="Eleves" aria-label="Eleves"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.students.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-user class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Eleves</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.affectations.index') }}" title="Affectations" aria-label="Affectations"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.affectations.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-book class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Affectation</span>
                    </a>
                </li>








                <li>
                    <a href="{{ route('admin.bulletins.index') }}"title="impression des bulletins"
                        aria-label="impression"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.bulletins.index') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-file-text class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Impressions</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.audit.saisie') }}" title="Audit de Saisie" aria-label="Audit de Saisie"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.audit.saisie') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-search class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Audit de Saisie</span>
                    </a>
                </li>
            @endcanany
            {{-- @can('access-censeur')
                <li>
                    <a href="{{ route('admin.bulletins.index') }}"title="impresion" aria-label="impresion"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.statistiques.registre') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-file-text class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Impressions</span>
                    </a>
                </li>
            @endcan --}}

            @can('access-enseignant')
                <li>
                    <a href="{{ route('enseignant.dashboard') }}" title="Enseignants" aria-label="Enseignants"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('enseignant.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-user class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Progression saisie</span>
                    </a>
                </li>
                <li>

                    <a href="{{ route('emplois.enseignant', auth()->id()) }}" title="Emploi du temps"
                        aria-label="Emploi du temps"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('emplois.enseignant') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                        <x-lucide-user class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Mon Emploi du temps</span>
                    </a>
                </li>
            @endcan





            @can('access-sg')
                <li>
                    <a href="{{ route('discipline.index') }}"title="Discipline" aria-label="Discipline"
                        class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('discipline.index') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }} text-sm">
                        <x-lucide-file-text class="w-4 h-4 text-center" />
                        <span class="sidebar-label ml-3">Discipline</span>
                    </a>
                </li>
            @endcan

            <li>
                <a href="{{ route('admin.evaluations.index') }}" title="Evaluations" aria-label="Evaluations"
                    class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.evaluations.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                    <x-lucide-calculator class="w-4 h-4 text-center" />
                    <span class="sidebar-label ml-3">Evaluations</span>
                </a>
            </li>


            <li>
                <a href="{{ route('admin.enseignants.index') }}" title="Enseignants" aria-label="Enseignants"
                    class="flex items-center px-3 py-2.5 rounded transition-colors group {{ request()->routeIs('admin.enseignants.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                    <x-lucide-user class="w-4 h-4 text-center" />
                    <span class="sidebar-label ml-3">Enseignants</span>
                </a>
            </li>

        </ul>

    </nav>










    <!-- 3. Bouton Déconnexion (Fixé en bas grâce à mt-auto) -->
    <div class="pt-4 border-t border-border mt-auto">
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" title="Déconnexion" aria-label="Déconnexion"
                class="flex items-center w-full px-3 py-2.5 rounded bg-danger text-white hover:opacity-90 transition-all">
                <x-lucide-log-out class="w-4 h-4 text-center" />
                <span class="sidebar-label ml-3">Déconnexion</span>
            </button>
        </form>
    </div>
</aside>

<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden transition-opacity" onclick="toggleSidebar()">
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const collapseBtn = document.getElementById('toggle-collapse');
        const collapseIcon = document.getElementById('collapse-icon');
        const labels = document.querySelectorAll('.sidebar-label');

        if (collapseBtn) {
            collapseBtn.addEventListener('click', () => {
                const isCollapsed = sidebar.dataset.collapsed === 'true';

                if (isCollapsed) {
                    // OUVRIR (Largeur 64)
                    sidebar.classList.remove('w-20');
                    sidebar.classList.add('w-64');
                    labels.forEach(el => el.classList.remove('hidden'));
                    collapseIcon.className = 'fas fa-angle-left text-xs';
                    sidebar.dataset.collapsed = 'false';
                } else {

                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-20');
                    labels.forEach(el => el.classList.add('hidden'));
                    collapseIcon.className = 'fas fa-angle-right text-xs';
                    sidebar.dataset.collapsed = 'true';
                }
            });
        }
    });

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>
