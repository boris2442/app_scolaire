<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="antialiased min-h-screen flex flex-col transition-colors duration-300">

    <header
        class="sticky top-0 z-40 w-full bg-card/80 backdrop-blur border-b border-border transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

            <div class="flex items-center space-x-3">
                <div
                    class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-primary-foreground font-bold shadow-md shadow-primary/20">
                    A
                </div>
                <span class="text-lg font-bold tracking-tight text-foreground">Academia<span
                        class="text-primary">Pro</span></span>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('admin.statistiques.index') }}"
                        class="text-sm font-semibold text-foreground hover:text-primary transition-colors mr-2">
                        Espace Gestion
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 bg-secondary text-secondary-foreground rounded-xl text-sm font-bold border border-border hover:bg-danger/10 hover:text-danger hover:border-danger/20 transition-all">
                            Déconnexion
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                    data-turbo="true"
                        class="px-5 py-2.5 bg-primary text-primary-foreground rounded-xl text-sm font-bold shadow-lg shadow-primary/15 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Connexion
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-grow">

        <section
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20 lg:pt-24 flex flex-col lg:flex-row items-center gap-12">
            <div class="w-full lg:w-1/2 space-y-6 text-center lg:text-left">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary tracking-wide uppercase">
                    Structure & Rigueur Pédagogique
                </span>
                <h1 class="text-4xl sm:text-5xl font-black text-foreground tracking-tight leading-none">
                    Pilotez les performances de votre établissement.
                </h1>
                <p
                    class="text-gray-500 dark:text-gray-400 text-base sm:text-lg max-w-xl mx-auto lg:mx-0 font-normal leading-relaxed">
                    Une architecture logicielle conçue spécifiquement pour la gestion des structures académiques :
                    centralisation des inscriptions, calcul automatisé des moyennes séquentielles et édition instantanée
                    des palmarès d'excellence.
                </p>
                <div class="flex justify-center lg:justify-start pt-2">
                    @auth
                        <a href="{{ route('admin.statistiques.index') }}"
                            class="px-6 py-3.5 bg-primary text-primary-foreground font-bold rounded-xl shadow-lg shadow-primary/20 hover:translate-y-[-2px] transition-all text-center">
                            Accéder à l'Espace Administration
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-6 py-3.5 bg-primary text-primary-foreground font-bold rounded-xl shadow-lg shadow-primary/20 hover:translate-y-[-2px] transition-all text-center">
                            Ouvrir la session de travail
                        </a>
                    @endauth
                </div>
            </div>

            <div class="w-full lg:w-1/2">
                <div
                    class="bg-card border border-border rounded-2xl shadow-2xl p-6 relative overflow-hidden transition-colors duration-300">
                    <div class="flex items-center justify-between border-b border-border pb-4 mb-4">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded-full bg-danger/20 border border-danger/40"></span>
                            <span class="w-3 h-3 rounded-full bg-warning/20 border border-warning/40"></span>
                            <span class="w-3 h-3 rounded-full bg-success/20 border border-success/40"></span>
                            <span class="text-xs font-mono text-gray-400 pl-2">Aperçu Synoptique</span>
                        </div>
                        <span
                            class="text-[10px] font-bold bg-success/10 text-success px-2 py-0.5 rounded uppercase">Session
                            active</span>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-secondary/50 p-4 rounded-xl border border-border">
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Taux
                                    d'admission (Moy ≥ 10)</span>
                                <div class="text-2xl font-black mt-1 text-foreground">78.4 %</div>
                            </div>
                            <div class="bg-secondary/50 p-4 rounded-xl border border-border">
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Major de
                                    Séquence</span>
                                <div class="text-2xl font-black mt-1 text-primary">18.42/20</div>
                            </div>
                        </div>
                        <div class="border border-border rounded-xl overflow-hidden text-xs">
                            <div
                                class="bg-secondary px-3 py-2 font-bold text-secondary-foreground border-b border-border">
                                Top 3 des élèves - Palmarès National</div>
                            <div class="divide-y divide-border bg-card">
                                <div class="px-3 py-2 flex justify-between"><span>1. Simo Boris</span> <span
                                        class="font-bold text-success">18.42</span></div>
                                <div class="px-3 py-2 flex justify-between"><span>2. Ombason Hilaire</span> <span
                                        class="font-bold text-success">16.15</span></div>
                                <div class="px-3 py-2 flex justify-between"><span>3. Sunier Pierre</span> <span
                                        class="font-bold text-success">15.80</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-secondary/40 border-y border-border py-20 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                    <h2 class="text-3xl font-black text-foreground tracking-tight">Modules de Traitement Académique</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Chaque entité du système répond aux exigences des structures d'évaluation modernes, assurant une
                        intégrité parfaite des données.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        class="bg-card border border-border rounded-2xl p-6 hover:border-primary/30 transition-all duration-300">
                        <div
                            class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-2">Inscriptions & Niveaux</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Suivi du statut d'inscription des élèves par année scolaire active. Segmentation par niveaux
                            d'études et classes pour empêcher toute incohérence d'effectif.
                        </p>
                    </div>
                    <div
                        class="bg-card border border-border rounded-2xl p-6 hover:border-primary/30 transition-all duration-300">
                        <div
                            class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 11h.01M12 7h.01M15 11h.01M15 14h.01M13 21h-2a2 2 0 01-2-2v-1a2 2 0 00-2-2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2h-2a2 2 0 00-2 2v1z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-2">Moyennes & Séquences</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Moteur d'agrégation calculant automatiquement les notes par séquence. Génération
                            transparente des mentions d'appréciations réglementaires de manière structurée.
                        </p>
                    </div>
                    <div
                        class="bg-card border border-border rounded-2xl p-6 hover:border-primary/30 transition-all duration-300">
                        <div
                            class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-2">Palmarès & Statistiques</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Extraction immédiate des indicateurs clés : taux de réussite par classe, répartition des
                            moyennes et listes d'excellence par période d'évaluation.
                        </p>
                    </div>
                </div>
            </div>
        </section>
<section class="bg-secondary/40 border-y border-border py-20 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                    <h2 class="text-3xl font-black text-foreground tracking-tight">Architecture Fonctionnelle du Système</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Une couverture intégrale des besoins administratifs et pédagogiques, structurée selon une modélisation relationnelle stricte.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    
                    <div class="bg-card border border-border rounded-2xl p-6 hover:border-primary/30 transition-all duration-300">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-2">Multi-Établissements</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Configuration et isolation complète des données de l'établissement (Nom, contacts, logo). Gestion centralisée des paramètres fondamentaux du système.
                        </p>
                    </div>

                    <div class="bg-card border border-border rounded-2xl p-6 hover:border-primary/30 transition-all duration-300">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-2">Années Scolaires & Périodes</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Contrôle strict de l'année active avec dates de début et de fin. Segmentation dynamique en structures d'évaluations (Trimestres et Séquences d'examens).
                        </p>
                    </div>

                    <div class="bg-card border border-border rounded-2xl p-6 hover:border-primary/30 transition-all duration-300">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-2">Départements & Classes</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Organisation hiérarchique par départements académiques, niveaux d'études et affectation des élèves dans des classes physiques aux effectifs contrôlés.
                        </p>
                    </div>

                    <div class="bg-card border border-border rounded-2xl p-6 hover:border-primary/30 transition-all duration-300">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292MM15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-2">Inscriptions & Fiches Élèves</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Gestion du registre matricule, historique des réinscriptions annuelles et suivi individuel du statut pédagogique et civil de chaque apprenant.
                        </p>
                    </div>

                    <div class="bg-card border border-border rounded-2xl p-6 hover:border-primary/30 transition-all duration-300">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 11h.01M12 7h.01M15 11h.01M15 14h.01M13 21h-2a2 2 0 01-2-2v-1a2 2 0 00-2-2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2h-2a2 2 0 00-2 2v1z" /></svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-2">Saisie des Notes & Coeffs</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Centralisation des évaluations par matière avec application stricte des coefficients. Validation et verrouillage des notes pour éviter toute altération ultérieure.
                        </p>
                    </div>

                    <div class="bg-card border border-border rounded-2xl p-6 hover:border-primary/30 transition-all duration-300">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-2">Moyennes & Palmarès</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Calcul automatique des moyennes séquentielles et trimestrielles. Génération instantanée des rangs, des mentions d'appréciation et des tableaux de statistiques.
                        </p>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <footer class="bg-card border-t border-border mt-auto transition-colors duration-300">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-gray-400 uppercase tracking-wider">
            <div class="flex items-center space-x-2">
                <span class="font-bold text-foreground">Academia<span class="text-primary">Pro</span></span>
                <span>—</span>
                <span>Gestion de Scolarité</span>
            </div>
            <div class="text-gray-500 font-normal normal-case text-center sm:text-right">
                &copy; {{ date('Y') }} AcademiaPro. Tous droits réservés.
            </div>
        </div>
    </footer>

</body>

</html>
