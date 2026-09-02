<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AcademiaPro | Logiciel de Gestion Scolaire & Suivi Académique') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


    <!-- Primary Title & Description -->
    <title>AcademiaPro | Logiciel de Gestion Scolaire & Suivi Académique</title>
    <meta name="description"
        content="AcademiaPro est la solution tout-en-un pour les établissements scolaires : gestion des notes, calcul automatique des moyennes, imports Excel et génération rapide des bulletins PDF.">
    <meta name="keywords"
        content="gestion scolaire, bulletins de notes, calcul moyennes, logiciel école, gestion élèves, Cameroun, AcademiaPro">
    <meta name="author" content="AcademiaPro">
    <meta name="robots" content="index, follow">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://boris.espacecameroun.com">

    <!-- Open Graph / Facebook / WhatsApp / LinkedIn -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://boris.boris.espacecameroun.com">
    <meta property="og:title" content="AcademiaPro | Simplifiez la Gestion Académique de votre Établissement">
    <meta property="og:description"
        content="Saisie des notes, calculs automatisés, gestion des absences et impression des bulletins en quelques clics.">
    <meta property="og:image" content="https://boris.espacecameroun.com/images/logo.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="fr_FR">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://boris.espacecameroun.com">
    <meta name="twitter:title" content="AcademiaPro | Logiciel de Gestion Scolaire">
    <meta name="twitter:description"
        content="Découvrez AcademiaPro : la plateforme moderne de gestion des notes et bulletins scolaires.">
    <meta name="twitter:image" content="https://boris.espacecameroun.com/images/logo.png">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>

</html>
