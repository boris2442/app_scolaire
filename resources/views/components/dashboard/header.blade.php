<header
    class="fixed top-0 right-0 left-0 z-30 bg-card border-b border-border text-foreground h-16 transition-colors duration-300">
    <div class="px-4 h-full">
        <div class="flex items-center justify-between h-full">

            <div class="flex items-center">
                <button id="toggleSidebarMobile"
                    class="md:hidden mr-3 p-2 rounded-lg hover:bg-secondary text-gray-500 transition-colors"
                    onclick="toggleSidebar()">


                    <x-lucide-menu class="w-6 h-6" />

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
                            @if (auth()->user()?->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                    alt="{{ auth()->user()->name }}"
                                    class="w-7 h-7 rounded-full object-cover shadow-sm group-hover:ring-4 group-hover:ring-primary/20 transition-all" />
                            @else
                                <div
                                    class="w-7 h-7 rounded-full bg-primary flex items-center justify-center text-primary-foreground font-bold shadow-sm group-hover:ring-4 group-hover:ring-primary/20 transition-all">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif

                            {{-- Pastille de statut en ligne --}}
                            <span
                                class="absolute bottom-0 right-0 block h-1.5 w-1.5 rounded-full bg-success ring-2 ring-card"></span>
                        </div>

                        <x-lucide-chevron-down
                            class="w-4 h-4 text-gray-400 group-hover:text-primary transition-colors" />
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

                                <x-lucide-user class="w-4 text-center" />
                                <span>Mon Profil</span>
                            </a>




                            <a href="{{ route('teacher.attestation.presence', auth()->user()->id) }}" target="_blank"
                                class="flex items-center space-x-2 px-3 py-2 text-sm rounded-lg hover:bg-secondary hover:text-primary transition-colors">
                                <x-lucide-cog class="w-4 text-center" />
                                <span>Presence effective</span>
                            </a>
                            <a href="{{ route('teacher.attestation.take-service', auth()->user()->id) }}"
                                target="_blank"
                                class="flex items-center space-x-2 px-3 py-2 text-sm rounded-lg hover:bg-secondary hover:text-primary transition-colors">
                                <x-lucide-clipboard class="w-4 text-center" />
                                <span>Prise de service</span>
                            </a>
                            <a href="{{ route('teacher.attestation.reprise-service', auth()->user()->id) }}"
                                target="_blank"
                                class="flex items-center space-x-2 px-3 py-2 text-sm rounded-lg hover:bg-secondary hover:text-primary transition-colors">
                                <x-lucide-clipboard class="w-4 text-center" />
                                <span>Reprise de service</span>
                            </a>

                            <div class="border-t border-border my-1"></div>

                            <button id="theme-toggle" type="button"
                                class="w-full flex items-center space-x-2 px-3 py-2 text-sm rounded-lg text-gray-500 hover:bg-secondary hover:text-primary transition-all duration-300 group/theme text-left">

                                <span class="w-4 flex justify-center">
                                    <!-- On ajoute 'hidden' ici pour que JS prenne le relais proprement -->
                                    {{-- <x-lucide-moon
                                        class="w-4 text-center hidden group-hover/theme:rotate-12 transition-transform" />
                                    <x-lucide-sun
                                        class="w-4 text-center hidden group-hover/theme:rotate-90 transition-transform" /> --}}








                                </span>

                                <span class="text-card-foreground">Changer de mode</span>
                            </button>

                            <div class="border-t border-border my-1"></div>
                            <div class="border-t border-border my-1"></div>
                            <form method="POST" action="/logout" class="block w-full">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center space-x-2 px-3 py-2 text-sm text-red-500 rounded-lg hover:bg-red-500/10 transition-colors text-left">

                                    <x-lucide-log-out class="w-4 text-center" />
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
