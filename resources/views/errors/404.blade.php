<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - 404</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- 3. ASSETS (VITE) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- <link rel="stylesheet" href="{{ asset('assets/webfonts/fa-brands-400.woff2') }}"> --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
</head>

<body class="h-full bg-background text-foreground flex items-center justify-center p-6 antialiased">
    <div class="max-w-md w-full text-center space-y-6">

        <!-- Illustration / Badge 404 -->
        <div class="relative flex items-center justify-center">
            <span class="text-9xl font-extrabold text-primary/10 select-none"></span>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="p-4 bg-primary/10 rounded-full ">
                    <x-lucide-alert-triangle class="w-12 h-12 text-red-600" />
                </div>
            </div>
        </div>

        <!-- Message d'erreur -->
        <div class="space-y-2">
            <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
                Oups ! Page introuvable
            </h1>
            <p class="text-sm text-muted-foreground">
                La page que vous recherchez n'existe pas, a été déplacée ou est temporairement indisponible.
            </p>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ url()->previous() }}"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-border bg-card hover:bg-secondary/20 text-foreground text-sm font-medium transition shadow-sm">
                <x-lucide-arrow-left class="w-4 h-4" />
                Page précédente
            </a>

            <a href="{{ url('/') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-primary-foreground text-sm font-medium transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Tableau de bord
            </a>
        </div>

    </div>
</body>

</html>
