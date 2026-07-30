@extends('layouts.admin.admin-layout')

@section('content')
    <div class="container mx-auto p-6 bg-background text-foreground min-h-screen">

        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-primary">
                Gestion des Emplois du Temps
            </h1>

            <p class="mt-2 text-sm text-foreground/70">
                Choisissez une classe pour consulter ou modifier son emploi du temps.
            </p>
        </div>

        <!-- Liste des classes -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            @foreach ($classes as $classe)
                <div
                    class="bg-card text-card-foreground border border-border rounded-xl shadow-lg p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">

                    <div class="flex items-center justify-between gap-4">

                        <div>

                            <h2 class="text-xl font-semibold text-primary">
                                {{ $classe->nom }}
                            </h2>

                            

                        </div>

                        <a href="{{ route('admin.emplois.classe', $classe->id) }}" title="ouvrir"
                            class="inline-flex items-center justify-center rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground shadow transition-all duration-300 hover:opacity-90 hover:scale-105">

                            Ouvrir

                        </a>

                    </div>

                </div>
            @endforeach

        </div>

    </div>
@endsection
