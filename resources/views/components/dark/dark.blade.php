{{-- 
    Composant Dark Mode Toggle (Bouton et Logique JS simplifiée)
--}}

<button id="theme-toggle" type="button"
    class="relative inline-flex items-center justify-center p-2.5 text-sm font-medium transition-all duration-300 rounded-xl border border-border bg-secondary text-foreground hover:text-primary hover:border-primary focus:outline-none focus:ring-2 focus:ring-ring group">

    {{-- Icône Lune (SVG brut) : Visible en mode clair, cachée en mode sombre --}}
    <svg class="w-5 h-5 transition-transform duration-500 group-hover:-rotate-12 dark:hidden"
        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="d" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
    </svg>

    {{-- Icône Soleil (SVG brut) : Cachée en mode clair, visible en mode sombre --}}
    <svg class="w-5 h-5 transition-transform duration-500 group-hover:rotate-90 hidden dark:block"
        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="5"></circle>
        <line x1="12" y1="1" x2="12" y2="3"></line>
        <line x1="12" y1="21" x2="12" y2="23"></line>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
        <line x1="1" y1="12" x2="3" y2="12"></line>
        <line x1="21" y1="12" x2="23" y2="12"></line>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
    </svg>
</button>

<script>
    // On s'assure que le script s'exécute bien après le chargement de Turbo / de la page
    document.addEventListener('turbo:load', () => {
        const rootHtml = document.documentElement;
        const toggleBtn = document.getElementById('theme-toggle');

        if (!toggleBtn) return;

        // Évite d'accumuler les écouteurs si turbo:load se déclenche plusieurs fois
        const newBtn = toggleBtn.cloneNode(true);
        toggleBtn.parentNode.replaceChild(newBtn, toggleBtn);

        newBtn.addEventListener('click', function() {
            // Appliquer une transition fluide
            rootHtml.style.setProperty('transition',
                'background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease', 'important');

            // Bascule de la classe dark
            if (rootHtml.classList.contains('dark')) {
                rootHtml.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                rootHtml.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }

            // Nettoyage de la transition
            setTimeout(() => {
                rootHtml.style.removeProperty('transition');
            }, 300);
        });
    });
</script>
