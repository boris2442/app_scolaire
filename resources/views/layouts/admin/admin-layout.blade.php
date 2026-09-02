<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AcademiaPro | Logiciel de Gestion Scolaire & Suivi Académique') }} - Gestion Scolaire</title>


    <meta name="description" content="AcademiaPro est la solution tout-en-un pour les établissements scolaires : gestion des notes, calcul automatique des moyennes, imports Excel et génération rapide des bulletins PDF.">
    <meta name="keywords" content="gestion scolaire, bulletins de notes, calcul moyennes, logiciel école, gestion élèves, Cameroun, AcademiaPro">
    <meta name="author" content="AcademiaPro">
    <meta name="robots" content="index, follow">




<!-- Canonical URL -->
    <link rel="canonical" href="https://academiapro.com">

    <!-- Open Graph / Facebook / WhatsApp / LinkedIn -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://academiapro.com">
    <meta property="og:title" content="AcademiaPro | Simplifiez la Gestion Académique de votre Établissement">
    <meta property="og:description" content="Saisie des notes, calculs automatisés, gestion des absences et impression des bulletins en quelques clics.">
    <meta property="og:image" content="https://academiapro.com/images/og-academiapro.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="fr_FR">




<!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://academiapro.com">
    <meta name="twitter:title" content="AcademiaPro | Logiciel de Gestion Scolaire">
    <meta name="twitter:description" content="Découvrez AcademiaPro : la plateforme moderne de gestion des notes et bulletins scolaires.">
    <meta name="twitter:image" content="https://academiapro.com/images/og-academiapro.jpg">





    {{-- 1. SCRIPT CRITIQUE : Prévention du Flash de thème (FOUC) --}}
    <script>
        (function() {
            const theme = localStorage.getItem('color-theme') ||
                (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches ?
                    'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- 3. ASSETS (VITE) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- <link rel="stylesheet" href="{{ asset('assets/webfonts/fa-brands-400.woff2') }}"> --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

</head>

<body
    class="bg-background text-foreground font-sans antialiased selection:bg-primary/10 selection:text-primary transition-colors duration-300">
    <x-alert />
    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        @include('components.dashboard.sidebar')

        {{-- Conteneur Principal --}}
        <div id="main-content"
            class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden transition-all duration-300 md:ml-64">

            {{-- Header --}}
            @include('components.dashboard.header')

            {{-- Main Content Area --}}
            <main class="flex-1 pt-20 pb-12 px-4 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto">

                    {{-- Fil d'ariane (Breadcrumb) optionnel ici --}}
                    <div class="mb-6">
                        @yield('header_actions')
                    </div>

                    {{-- Contenu de la page --}}
                    <div class="animate-in fade-in slide-in-from-bottom-4 duration-700">
                        @yield('content')
                    </div>

                </div>
            </main>

            {{-- Footer Simple --}}
            <footer class="py-4 px-6 border-t border-border bg-card/50 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} AcademiaPro. Tous droits réservés.(+237) 679 13 51 77 / 689 58 72 79 / 694 22
                35 03 / 675 06 60 01
            </footer>

        </div>
    </div>

    {{-- 4. SCRIPTS DE CONTROLE DU LAYOUT --}}
    <script>
        // Synchronisation de la marge du contenu avec l'état de la Sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleCollapse = document.getElementById('toggle-collapse');

            if (toggleCollapse) {
                toggleCollapse.addEventListener('click', () => {
                    // On attend un cycle pour lire la classe mise à jour sur la sidebar
                    setTimeout(() => {
                        const isCollapsed = sidebar.classList.contains('w-20');
                        if (isCollapsed) {
                            mainContent.classList.replace('md:ml-64', 'md:ml-20');
                        } else {
                            mainContent.classList.replace('md:ml-20', 'md:ml-64');
                        }
                    }, 50);
                });
            }
        });
    </script>
</body>

</html>
