{{-- 
    Composant Dark Mode Toggle (Bouton et Logique JS)
    
    1. Utilise localStorage pour mémoriser le choix.
    2. Ajoute/Supprime la classe 'dark' à la balise <html>.
    3. Utilise la classe 'transition duration-300' de Tailwind pour une animation fluide.
--}}

<button id="theme-toggle" type="button"
    class="relative inline-flex items-center justify-center p-2.5 text-sm font-medium transition-all duration-300 rounded-xl border border-border bg-secondary text-foreground hover:text-primary hover:border-primary focus:outline-none focus:ring-2 focus:ring-ring group">

    {{-- Icône Lune (affichée en mode CLAIR pour passer au SOMBRE) --}}
    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 transition-transform duration-500 group-hover:-rotate-12" 
        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
    </svg>

    {{-- Icône Soleil (affichée en mode SOMBRE pour passer au CLAIR) --}}
    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 transition-transform duration-500 group-hover:rotate-90" 
        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
    </svg>
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
        rootHtml.style.setProperty('transition', 'background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease', 'important');

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
