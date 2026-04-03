<header
    class="fixed top-0 right-0 left-0 z-30 bg-card border-b border-border text-foreground h-16 transition-colors duration-300">
    <div class="px-4 h-full">
        <div class="flex items-center justify-between h-full">

            <div class="flex items-center">
                <button id="toggleSidebarMobile"
                    class="md:hidden mr-3 p-2 rounded-lg hover:bg-secondary text-gray-500 transition-colors"
                    onclick="toggleSidebar()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div class="hidden sm:block">
                    <span class="text-sm font-semibold text-primary uppercase tracking-wider">
                        Espace Gestion <span class="text-foreground">/</span>
                        <span class="text-gray-500 font-normal normal-case">Tableau de bord</span>
                    </span>

                </div>
            </div>

            <div class="flex items-center space-x-2 sm:space-x-4">

                <button id="theme-toggle" type="button"
                    class="p-2.5 rounded-xl border border-border bg-secondary text-gray-500 hover:text-primary hover:border-primary transition-all duration-300 group">
                    <svg id="theme-toggle-dark-icon" class="w-5 h-5 hidden group-hover:rotate-12 transition-transform"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-toggle-light-icon" class="w-5 h-5 hidden group-hover:rotate-90 transition-transform"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z">
                        </path>
                    </svg>
                </button>

                <div class="h-8 w-px bg-border"></div>
                <div class="h-4 w-px bg-border mx-2"></div>
                <div id="digital-clock"
                    class="text-sm font-mono font-bold text-primary bg-primary/10 px-3 py-1 rounded-full border border-primary/20">
                    00:00:00
                </div>
                <div class="flex items-center space-x-3 pl-2">
                    <div class="hidden text-right lg:block">
                        <p class="text-sm font-bold leading-none">{{ auth()->user()->name ?? 'Utilisateur' }}</p>
                        <p class="text-[10px] text-primary font-medium uppercase mt-1">Administrateur</p>
                    </div>

                    <div class="relative group cursor-pointer">
                        <div
                            class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-primary-foreground font-bold shadow-sm group-hover:ring-4 group-hover:ring-primary/20 transition-all">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <span
                            class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-success ring-2 ring-card"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
