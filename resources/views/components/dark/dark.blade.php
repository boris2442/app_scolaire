{{-- 
    Composant Dark Mode Toggle (Bouton et Logique JS)
    
    1. Utilise localStorage pour mémoriser le choix.
    2. Ajoute/Supprime la classe 'dark' à la balise <html>.
    3. Utilise la classe 'transition duration-300' de Tailwind pour une animation fluide.
--}}

<button id="theme-toggle" type="button"
    class="relative inline-flex items-center justify-center p-2.5 text-sm font-medium transition-all duration-300 rounded-xl border border-border bg-secondary text-foreground hover:text-primary hover:border-primary focus:outline-none focus:ring-2 focus:ring-ring group">

    {{-- Icône Lune (affichée en mode CLAIR pour passer au SOMBRE) --}}
    <x-lucide-moon id="theme-toggle-dark-icon"
        class="hidden w-5 h-5 transition-transform duration-500 group-hover:-rotate-12" />

    {{-- Icône Soleil (affichée en mode SOMBRE pour passer au CLAIR) --}}
    <x-lucide-sun id="theme-toggle-light-icon"
        class="hidden w-5 h-5 transition-transform duration-500 group-hover:rotate-90" />
</button>
<script>
    const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
    const rootHtml = document.documentElement;

    // 1. Initialisation : Détecter et appliquer le thème sans effet de flash
    const isDark = localStorage.getItem('color-theme') === 'dark' ||
        (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

    if (isDark) {
        rootHtml.classList.add('dark');
        themeToggleLightIcon.classList.remove('hidden');
    } else {
        rootHtml.classList.remove('dark');
        themeToggleDarkIcon.classList.remove('hidden');
    }

    // 2. Logique de basculement au clic
    document.getElementById('theme-toggle').addEventListener('click', function() {
        // Appliquer une transition globale fluide sur les couleurs uniquement
        rootHtml.style.setProperty('transition',
            'background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease', 'important');

        // Inverser les icônes
        themeToggleDarkIcon.classList.toggle('hidden');
        themeToggleLightIcon.classList.toggle('hidden');

        // Inverser la classe dark
        if (rootHtml.classList.contains('dark')) {
            rootHtml.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        } else {
            rootHtml.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        }

        // Nettoyage de la transition pour éviter les effets de bord sur le layout
        setTimeout(() => {
            rootHtml.style.removeProperty('transition');
        }, 300);
    });
</script>
