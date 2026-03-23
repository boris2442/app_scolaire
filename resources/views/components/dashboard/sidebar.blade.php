<aside id="sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-4 transition-all duration-300 border-r bg-card text-foreground border-border -translate-x-full md:translate-x-0"
    data-collapsed="false">

    <div class="absolute top-6 -right-3 hidden md:block">
        <button id="toggle-collapse"
            class="flex items-center justify-center w-6 h-6 rounded-full border border-border bg-primary text-primary-foreground shadow-sm hover:scale-110 transition-transform">
            <i class="fas fa-angle-left text-xs" id="collapse-icon"></i>
        </button>
    </div>

    <div class="flex items-center px-6 mb-8 gap-3">
        <div class="flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="logo" class="w-10 h-10 rounded-lg shadow-sm">
        </div>
        <div class="sidebar-label transition-opacity duration-300">
            <h1 class="font-bold text-lg leading-none tracking-tight">ACADEMIA<span
                    class="text-primary text-xs ml-1">PRO</span></h1>
            <p class="text-[10px] text-muted-foreground uppercase tracking-widest mt-1">Management System</p>
        </div>
    </div>

    {{-- <nav class="px-3 space-y-1">

        <div class="sidebar-label px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Tableau de
            Bord</div>
        <a href="{{ route('settings.index') }}"
            class="flex items-center px-3 py-2.5 rounded-lg bg-secondary text-primary transition-colors group">
            <i class="fas fa-cogs w-6 text-center"></i>
            <span class="sidebar-label ml-3 font-medium">Paramètres École</span>
        </a>
        <a href="#"
            class="flex items-center px-3 py-2.5 rounded-lg bg-secondary text-primary transition-colors group">
            <i class="fas fa-chart-line w-6 text-center"></i>
            <span class="sidebar-label ml-3 font-medium">Vue d'ensemble</span>
        </a>

        <div class="sidebar-label px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-4">
            Scolarité</div>

        <a href="{{ route('settings.annees.index') }}"
            class="flex items-center px-3 py-2.5 rounded-lg hover:bg-secondary hover:text-primary transition-colors group">
            <i class="fas fa-calendar-alt w-6 text-center"></i>
            <span class="sidebar-label ml-3">Années </span>
        </a>

        <a href="#"
            class="flex items-center px-3 py-2.5 rounded-lg hover:bg-secondary hover:text-primary transition-colors group">
            <i class="fas fa-school w-6 text-center"></i>
            <span class="sidebar-label ml-3">Classes & Salles</span>
        </a>

        <a href="#"
            class="flex items-center px-3 py-2.5 rounded-lg hover:bg-secondary hover:text-primary transition-colors group">
            <i class="fas fa-book w-6 text-center"></i>
            <span class="sidebar-label ml-3">Matières & Coeffs</span>
        </a>

        <div class="sidebar-label px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-4">
            Utilisateurs</div>

        <a href="#"
            class="flex items-center px-3 py-2.5 rounded-lg hover:bg-secondary hover:text-primary transition-colors group">
            <i class="fas fa-user-graduate w-6 text-center"></i>
            <span class="sidebar-label ml-3">Inscriptions Élèves</span>
        </a>

        <a href="#"
            class="flex items-center px-3 py-2.5 rounded-lg hover:bg-secondary hover:text-primary transition-colors group">
            <i class="fas fa-chalkboard-teacher w-6 text-center"></i>
            <span class="sidebar-label ml-3">Enseignants</span>
        </a>

        <div class="sidebar-label px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-4">
            Évaluations</div>

        <a href="#"
            class="flex items-center px-3 py-2.5 rounded-lg hover:bg-secondary hover:text-primary transition-colors group">
            <i class="fas fa-edit w-6 text-center"></i>
            <span class="sidebar-label ml-3">Saisie des Notes</span>
        </a>

        <a href="#"
            class="flex items-center px-3 py-2.5 rounded-lg hover:bg-secondary hover:text-primary transition-colors group text-danger">
            <i class="fas fa-file-pdf w-6 text-center"></i>
            <span class="sidebar-label ml-3 font-semibold">Bulletins & Bilans</span>
        </a>

    </nav> --}}


    <nav class="px-3 space-y-1">
        <div class="sidebar-label px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
            Tableau de Bord
        </div>
        <ul>
            <li>


                <a href="{{ route('settings.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-colors group {{ request()->routeIs('settings.index') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                    <i class="fas fa-cogs w-6 text-center"></i>
                    <span class="sidebar-label ml-3 font-medium">Paramètres École</span>
                </a>
            </li>
            <div class="sidebar-label px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-4">
                Scolarité
            </div>
            <li>

                <a href="{{ route('settings.annees.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-colors group {{ request()->routeIs('settings.annees.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                    <i class="fas fa-calendar-alt w-6 text-center"></i>
                    <span class="sidebar-label ml-3">Années</span>
                </a>
            </li>
            <li>

                <a href="{{ route('settings.academique.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-colors group {{ request()->routeIs('settings.academique.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                    <i class="fas fa-sitemap w-6 text-center"></i>
                    <span class="sidebar-label ml-3">Cycles & Niveaux</span>
                </a>
            </li>
            <li>
                <a href="{{ route('settings.classes.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-colors group {{ request()->routeIs('settings.classes.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                    <i class="fas fa-school w-6 text-center"></i>
                    <span class="sidebar-label ml-3">Classes & Salles</span>
                </a>
            </li>
            <li>
                <a href="{{ route('settings.matieres.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-colors group {{ request()->routeIs('settings.matieres.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-secondary hover:text-primary' }}">
                    <i class="fas fa-book w-6 text-center"></i>
                    <span class="sidebar-label ml-3">Matières & Coeffs</span>
                </a>
            </li>
            {{-- <a href="{{ route('settings.classes.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-secondary/50 transition-all {{ request()->routeIs('settings.classes.*') ? 'bg-primary/10 text-primary font-bold' : 'text-foreground/70' }}">
            <i class="fas fa-chalkboard-teacher w-5"></i>
            <span class="text-sm">Classes & Programme</span>
        </a> --}}
        </ul>

    </nav>








    <div class="absolute bottom-4 w-full px-3">
        <div class="border-t border-border my-4"></div>
        <form method="POST" action="#"> @csrf
            <button type="submit"
                class="flex items-center w-full px-3 py-2.5 rounded-lg bg-danger text-white hover:opacity-90 transition-all">
                <i class="fas fa-sign-out-alt w-6 text-center"></i>
                <span class="sidebar-label ml-3">Déconnexion</span>
            </button>
        </form>
    </div>
</aside>

<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden transition-opacity" onclick="toggleSidebar()">
</div>
{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggleCollapse = document.getElementById('toggle-collapse');
        const collapseIcon = document.getElementById('collapse-icon');
        const labels = document.querySelectorAll('.sidebar-label');

        // 1. Gestion du Collapse (Desktop)
        if (toggleCollapse) {
            toggleCollapse.addEventListener('click', () => {
                const isCollapsed = sidebar.dataset.collapsed === 'true';

                if (isCollapsed) {
                    // Ouvrir
                    sidebar.classList.replace('w-20', 'w-64');
                    labels.forEach(el => el.classList.remove('hidden'));
                    collapseIcon.classList.replace('fa-angle-right', 'fa-angle-left');
                    sidebar.dataset.collapsed = 'false';
                } else {
                    // Réduire
                    sidebar.classList.replace('w-64', 'w-20');
                    labels.forEach(el => el.classList.add('hidden'));
                    collapseIcon.classList.replace('fa-angle-left', 'fa-angle-right');
                    sidebar.dataset.collapsed = 'true';
                }
            });
        }
    });

    // 2. Fonction Toggle (Mobile)
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }



    const toggleCollapse = document.getElementById('toggle-collapse');
    const sidebar = document.getElementById('sidebar');

    toggleCollapse.addEventListener('click', () => {
        const collapsed = sidebar.dataset.collapsed === 'true';

        if (collapsed) {
            // Ouvrir
            sidebar.classList.remove('w-20');
            sidebar.classList.add('w-64');
            document.querySelectorAll('.sidebar-label').forEach(el => {
                el.classList.remove('hidden');
            });
            sidebar.dataset.collapsed = 'false';
            toggleCollapse.innerHTML = '<i class="fas fa-angle-left"></i>';
        } else {
            // Réduire
            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-20');
            document.querySelectorAll('.sidebar-label').forEach(el => {
                el.classList.add('hidden');
            });
            sidebar.dataset.collapsed = 'true';
            toggleCollapse.innerHTML = '<i class="fas fa-angle-right"></i>';
        }
    });
</script> --}}
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
                    // RÉDUIRE (Largeur 20)
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
