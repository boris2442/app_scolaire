{{-- 
    Composant Dark Mode Toggle (Bouton et Logique JS simplifiée)
--}}

<button id="theme-toggle" type="button"
    class="relative inline-flex items-center justify-center p-2.5 text-sm font-medium transition-all duration-300 rounded-xl border border-border bg-secondary text-foreground hover:text-primary hover:border-primary focus:outline-none focus:ring-2 focus:ring-ring group">

    {{-- Icône Lune : Visible en mode clair, cachée en mode sombre --}}
    <x-lucide-moon class="w-5 h-5 transition-transform duration-500 group-hover:-rotate-12 dark:hidden" />

    {{-- Icône Soleil : Cachée en mode clair, visible en mode sombre --}}
    <x-lucide-sun class="w-5 h-5 transition-transform duration-500 group-hover:rotate-90 hidden dark:block" />
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
