<aside id="sidebar"
    class="z-40 w-64 h-screen transition-all duration-300 border-r bg-card text-foreground border-border -translate-x-full md:translate-x-0 flex flex-col fixed left-0 top-0"
    data-collapsed="false">

    <!-- Bouton Réduction (Desktop uniquement) -->
    <div class="absolute top-6 -right-3 hidden md:block z-50">
        <button id="toggle-collapse"
            class="flex items-center justify-center w-6 h-6 rounded-full border border-border bg-primary text-primary-foreground shadow-sm hover:scale-110 transition-transform">
            <x-lucide-chevron-left class="w-4 h-4" id="collapse-icon" />
        </button>
    </div>

    <!-- ZONE FIXE EN HAUT : Logo + Informations Utilisateur -->
    <div class="flex-shrink-0 p-4 border-b border-border/60 bg-card">
        <!-- 1. Logo AcademiaPro -->
        <div class="flex items-center gap-3 mb-3">
            <div class="flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="logo" class="w-8 h-8 rounded-full shadow-sm">
            </div>
            <div class="sidebar-label transition-opacity duration-300">
                <h1 class="font-bold text-base leading-none tracking-tight">ACADEMIA<span
                        class="text-primary text-xs ml-0.5">PRO</span></h1>
                <p class="text-[9px] text-muted-foreground uppercase tracking-widest mt-0.5">Management System</p>
            </div>
        </div>

        <!-- 2. Coordonnées de l'utilisateur (Compilées pour gagner de l'espace) -->
        <div class="flex items-center gap-2.5 pt-2 border-t border-border/40">
            <div class="flex-shrink-0">
                <a href="{{ route('profile.edit') }}" title="Voir le profil" aria-label="Voir le profil">
                    @if (auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar"
                            class="w-9 h-9 rounded-full object-cover border border-primary shadow-sm">
                    @else
                        <div
                            class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs border border-primary shadow-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                    @endif
                </a>
            </div>

            <div class="sidebar-label transition-opacity duration-300 min-w-0 flex-1">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] text-muted-foreground leading-none">Bienvenue,</p>
                    <span class="text-[9px] bg-primary/10 text-primary px-1.5 py-0.5 rounded font-bold uppercase">
                        {{ auth()->user()->role ?? 'UTILISATEUR' }}
                    </span>
                </div>
                <h2 class="font-bold text-xs tracking-tight text-foreground mt-0.5 truncate">
                    <a href="{{ route('profile.edit') }}" title="Voir le profil" class="hover:underline">
                        {{ auth()->user()->name }}
                    </a>
                </h2>
            </div>
        </div>
    </div>

    <!-- ZONE DÉFILANTE (SCROLLABLE) : Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-1 custom-scrollbar">
        <div class="sidebar-label px-3 py-1.5 text-[10px] font-semibold text-muted-foreground tracking-wider uppercase">
            Tableau de Bord
        </div>

        <ul class="space-y-1">
            <li>
                <a href="{{ route('home') }}" title="Accueil" aria-label="Accueil"
                    class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('home') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-home class="w-4 h-4 flex-shrink-0" />
                    <span class="sidebar-label ml-3 font-medium truncate">Accueil</span>
                </a>
            </li>

            @can('access-admin')
                <li>
                    <a href="{{ route('settings.index') }}" title="Paramètres École"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('settings.index') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-settings class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 font-medium truncate">Paramètres École</span>
                    </a>
                </li>

                <div
                    class="sidebar-label px-3 pt-3 pb-1 text-[10px] font-semibold text-muted-foreground tracking-wider uppercase">
                    Scolarité
                </div>

                <li>
                    <a href="{{ route('settings.years.index') }}" title="Années Scolaires"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('settings.years.*') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-calendar class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Années</span>
                    </a>
                </li>
            @endcan

            @canany(['access-admin', 'access-censeur', 'access-secretaire'])
                <li>
                    <a href="{{ route('admin.sequences.index') }}" title="Clôture Séquences"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('admin.sequences.*') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-lock class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 font-medium truncate">Clôture Séquences</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.departments.index') }}" title="Départements"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('admin.departments.index') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-building class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Départements</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.creneaux.index') }}" title="Créneaux"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('admin.creneaux.index') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-clock class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Créneaux</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.emplois.classes') }}" title="Emplois de temps"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('admin.emplois.classes') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-calendar-days class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Emplois de temps</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('avancement.index') }}" title="Suivi des cours"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('avancement.*') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-book-open-check class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Suivi des cours</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" title="Utilisateurs"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('admin.users.index') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-users class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Utilisateurs</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.groupes.index') }}" title="Groupement matières"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('admin.groupes.index') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-layers class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Groupement matières</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings.academique.index') }}" title="Cycles "
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('settings.academique.*') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-git-fork class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Cycles </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings.classes.index') }}" title="Classes & Salles"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('settings.classes.*') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-school class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Classes </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings.matieres.index') }}" title="Matières & Coeffs"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('settings.matieres.*') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-book class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Matières </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.students.index') }}" title="Élèves"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('admin.students.*') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-graduation-cap class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Élèves</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.affectations.index') }}" title="Affectations"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('admin.affectations.*') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-user-check class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Affectations</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.bulletins.index') }}" title="Impressions"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('admin.bulletins.index') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-printer class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Impressions</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.audit.saisie') }}" title="Audit de Saisie"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('admin.audit.saisie') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-search class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Audit de Saisie</span>
                    </a>
                </li>
            @endcanany

            @can('access-enseignant')
            @endcan

            @can('access-sg')
                <li>
                    <a href="{{ route('discipline.index') }}" title="Discipline"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('discipline.index') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                        <x-lucide-shield-alert class="w-4 h-4 flex-shrink-0" />
                        <span class="sidebar-label ml-3 truncate">Discipline</span>
                    </a>
                </li>
            @endcan
            <li>
                <a href="{{ route('enseignant.dashboard') }}" title="Progression saisie"
                    class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('enseignant.*') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-line-chart class="w-4 h-4 flex-shrink-0" />
                    <span class="sidebar-label ml-3 truncate">Progression saisie</span>
                </a>
            </li>
            <li>
                <a href="{{ route('emplois.enseignant', auth()->id()) }}" title="Mon Emploi du temps"
                    class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('emplois.enseignant') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-calendar class="w-4 h-4 flex-shrink-0" />
                    <span class="sidebar-label ml-3 truncate">Mon Emploi du temps</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.evaluations.index') }}" title="Évaluations"
                    class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('admin.evaluations.*') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-calculator class="w-4 h-4 flex-shrink-0" />
                    <span class="sidebar-label ml-3 truncate">Évaluations</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.enseignants.index') }}" title="Enseignants"
                    class="flex items-center px-3 py-2 rounded-md text-sm transition-colors group {{ request()->routeIs('admin.enseignants.*') ? 'bg-primary text-primary-foreground shadow-md' : 'hover:bg-accent hover:text-accent-foreground' }}">
                    <x-lucide-user class="w-4 h-4 flex-shrink-0" />
                    <span class="sidebar-label ml-3 truncate">Enseignants</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- ZONE FIXE EN BAS : Déconnexion -->
    <div class="flex-shrink-0 p-3 border-t border-border bg-card">
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" title="Déconnexion" aria-label="Déconnexion"
                class="flex items-center justify-center w-full px-3 py-2 rounded-md bg-red-600 text-white hover:opacity-90 transition-all text-sm font-medium">
                <x-lucide-log-out class="w-4 h-4 flex-shrink-0" />
                <span class="sidebar-label ml-3 truncate">Déconnexion</span>
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
