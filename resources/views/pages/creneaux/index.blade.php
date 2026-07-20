@extends('layouts.admin.admin-layout')

@section('content')
    <div class="container mx-auto p-6 bg-background text-foreground min-h-screen">

        <!-- Titre -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-primary">
                Gestion des Créneaux Horaires
            </h1>
            <p class="text-sm text-foreground/70 mt-1">
                Configurez les différents créneaux horaires de votre établissement.
            </p>
        </div>

        <!-- Message succès -->
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-success bg-success/10 px-4 py-3 text-success shadow">
                {{ session('success') }}
            </div>
        @endif

        <!-- Message erreur -->
        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-danger bg-danger/10 px-4 py-3 text-danger shadow">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Formulaire -->
            <div class="bg-card text-card-foreground border border-border rounded-xl shadow-lg p-6 h-fit">

                <h2 class="text-xl font-semibold text-primary mb-6">
                    Ajouter un créneau
                </h2>

                <form {{-- action="{{ route('creneaux.store') }}" --}} method="POST">

                    @csrf

                    <div class="mb-5">

                        <label class="block text-sm font-medium text-foreground mb-2">
                            Heure de début
                        </label>

                        <input type="time" name="heure_debut" required
                            class="w-full rounded-lg border-border bg-background text-foreground focus:border-primary focus:ring-primary">

                    </div>

                    <div class="mb-5">

                        <label class="block text-sm font-medium text-foreground mb-2">
                            Heure de fin
                        </label>

                        <input type="time" name="heure_fin" required
                            class="w-full rounded-lg border-border bg-background text-foreground focus:border-primary focus:ring-primary">

                    </div>

                    <div class="mb-6">

                        <label class="block text-sm font-medium text-foreground mb-2">
                            Libellé
                        </label>

                        <input type="text" name="libelle" placeholder="Ex : Pause, Cours..."
                            class="w-full rounded-lg border-border bg-background text-foreground placeholder:text-foreground/40 focus:border-primary focus:ring-primary">

                    </div>

                    <button type="submit"
                        class="w-full rounded-lg bg-primary text-primary-foreground py-3 font-semibold shadow transition duration-300 hover:opacity-90 hover:scale-[1.02]">

                        Enregistrer le créneau

                    </button>

                </form>

            </div>

            <!-- Tableau -->
            <div
                class="lg:col-span-2 bg-card text-card-foreground border border-border rounded-xl shadow-lg overflow-hidden">

                <div class="p-6 border-b border-border">

                    <h2 class="text-xl font-semibold text-primary">
                        Créneaux configurés
                    </h2>

                </div>

                <div class="overflow-x-auto">

                    <table class="min-w-full border-collapse">

                        <thead>

                            <tr class="bg-secondary text-secondary-foreground uppercase text-xs">

                                <th class="border border-border p-4 text-left">
                                    Heure début
                                </th>

                                <th class="border border-border p-4 text-left">
                                    Heure fin
                                </th>

                                <th class="border border-border p-4 text-left">
                                    Libellé
                                </th>

                                <th class="border border-border p-4 text-center">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($creneaux as $creneau)
                                <tr class="hover:bg-secondary/40 transition">

                                    <td class="border border-border p-4 font-semibold">

                                        {{ \Carbon\Carbon::parse($creneau->heure_debut)->format('H:i') }}

                                    </td>

                                    <td class="border border-border p-4 font-semibold">

                                        {{ \Carbon\Carbon::parse($creneau->heure_fin)->format('H:i') }}

                                    </td>

                                    <td class="border border-border p-4 text-foreground/70">

                                        {{ $creneau->libelle ?? '-' }}

                                    </td>

                                    <td class="border border-border p-4 text-center">

                                        <form action="{{ route('admin.creneaux.destroy', $creneau->id) }}" method="POST"
                                            onsubmit="return confirm('Voulez-vous vraiment supprimer ce créneau ?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="rounded-lg bg-danger/10 text-danger border border-danger px-4 py-2 text-sm font-semibold transition hover:bg-danger hover:text-white">

                                                Supprimer

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4"
                                        class="border border-border p-8 text-center text-foreground/50 italic">

                                        Aucun créneau horaire configuré pour le moment.

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
@endsection
