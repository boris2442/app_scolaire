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



                {{-- <div class="h-8 w-px bg-border"></div> --}}
                <div class="h-4 w-px bg-border mx-2"></div>
                <div id="digital-clock"
                    class="text-xs font-mono font-bold text-primary bg-primary/10 px-3 py-1 rounded-full border border-primary/20">
                    00:00:00
                </div>



                <div class="relative flex items-center pl-2">

                    <button id="user-menu-button"
                        class="flex items-center space-x-3 cursor-pointer focus:outline-none group">
                        <div class="hidden text-right lg:block">
                            <p
                                class="text-sm font-bold leading-none text-foreground group-hover:text-primary transition-colors">
                                {{ auth()->user()->name ?? 'Utilisateur' }}
                            </p>
                            <p
                                class="text-xs italic  leading-none text-foreground group-hover:text-primary transition-colors">
                                {{ auth()->user()->phone ?? '' }}
                            </p>
                        </div>

                        <div class="relative">
                            <div
                                class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-primary-foreground font-bold shadow-sm group-hover:ring-4 group-hover:ring-primary/20 transition-all">
                                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                {{-- {{ substr(auth()->user()->phone ?? 'U', 0, 1) }} --}}
                            </div>
                            <span
                                class="absolute bottom-0 right-0 block h-1.5 w-1.5 rounded-full bg-success ring-2 ring-card"></span>
                        </div>

                        <i
                            class="fas fa-chevron-down text-[10px] text-gray-400 group-hover:text-primary transition-colors pl-1"></i>
                    </button>

                    <div id="user-dropdown"
                        class="hidden absolute right-0 top-full mt-2 w-48 bg-card text-card-foreground border border-border rounded-xl shadow-xl z-50 overflow-hidden transform origin-top-right transition-all duration-200">

                        <div class="px-4 py-2.5 border-b border-border bg-secondary/30">
                            <p class="text-xs text-gray-400">Mon compte</p>
                            <p class="text-sm font-medium truncate">{{ auth()->user()->email ?? 'user@example.com' }}
                            </p>
                        </div>

                        <div class="p-1.5 space-y-0.5">
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center space-x-2 px-3 py-2 text-sm rounded-lg hover:bg-secondary hover:text-primary transition-colors">
                                <i class="fas fa-user-circle w-4 text-center"></i>
                                <span>Mon Profil</span>
                            </a>

                            <a href=""
                                class="flex items-center space-x-2 px-3 py-2 text-sm rounded-lg hover:bg-secondary hover:text-primary transition-colors">
                                <i class="fas fa-bell w-4 text-center"></i>
                                <span>Notifications</span>
                                <span
                                    class="ml-auto bg-danger text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">3</span>
                            </a>

                            <a href=""
                                class="flex items-center space-x-2 px-3 py-2 text-sm rounded-lg hover:bg-secondary hover:text-primary transition-colors">
                                <i class="fas fa-cog w-4 text-center"></i>
                                <span>Paramètres</span>
                            </a>

                            <div class="border-t border-border my-1"></div>

                            <button id="theme-toggle" type="button"
                                class="w-full flex items-center space-x-2 px-3 py-2 text-sm rounded-lg text-gray-500 hover:bg-secondary hover:text-primary transition-all duration-300 group/theme text-left">

                                <span class="w-4 flex justify-center">
                                    <i id="theme-toggle-dark-icon"
                                        class="fas fa-moon text-base hidden group-hover/theme:rotate-12 transition-transform"></i>
                                    <i id="theme-toggle-light-icon"
                                        class="fas fa-sun text-lg hidden group-hover/theme:rotate-90 transition-transform"></i>
                                </span>

                                <span class="text-card-foreground">Changer de mode</span>
                            </button>

                            <div class="border-t border-border my-1"></div>
                            <div class="border-t border-border my-1"></div>
                            <form method="POST" action="/logout" class="block w-full">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center space-x-2 px-3 py-2 text-sm text-red-500 rounded-lg hover:bg-red-500/10 transition-colors text-left">
                                    <i class="fas fa-sign-out-alt w-4 text-center"></i>
                                    <span>Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('turbo:load', () => {
                        const button = document.getElementById('user-menu-button');
                        const dropdown = document.getElementById('user-dropdown');

                        if (button && dropdown) {
                            // Toggle le menu lors du clic sur le bouton
                            button.addEventListener('click', (e) => {
                                e.stopPropagation();
                                dropdown.classList.toggle('hidden');
                            });

                            // Ferme le menu si on clique n'importe où ailleurs
                            document.addEventListener('click', (e) => {
                                if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                                    dropdown.classList.add('hidden');
                                }
                            });
                        }
                    });
                </script>
            </div>



        </div>
    </div>
</header>
