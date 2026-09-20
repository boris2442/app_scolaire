@extends('layouts.admin.admin-layout')

@section('content')
    <div class="p-6 space-y-6 max-w-7xl mx-auto">

        <!-- En-tête de page -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[var(--border)]">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-[var(--foreground)]">
                    Gestion des Séquences & Verrouillage
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Configurez les dates limites et le verrouillage de la saisie des notes pour l'année scolaire active.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[var(--secondary)] text-[var(--secondary-foreground)] border border-[var(--border)]">
                    Année Active : <strong
                        class="ml-1 text-[var(--primary)]">{{ $actifYear->libelle ?? 'Non définie' }}</strong>
                </span>
            </div>
        </div>



        @if (session('error'))
            <div
                class="p-4 rounded-xl bg-[var(--danger)]/10 border border-[var(--danger)]/20 text-[var(--danger)] text-sm flex items-center gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Grille des Séquences -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($sequences as $sequence)
                @php
                    $isExpired = $sequence->submission_deadline && now()->greaterThan($sequence->submission_deadline);
                    $isLocked = $sequence->is_closed || $isExpired;
                @endphp

                <div
                    class="bg-[var(--card)] text-[var(--card-foreground)] rounded-2xl border border-[var(--border)] shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden">

                    <!-- Card Header -->
                    <div
                        class="p-5 border-b border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/30 flex items-start justify-between gap-3">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                {{ $sequence->trimestre->nom ?? 'Trimestre' }}
                            </span>
                            <h3 class="text-lg font-bold text-[var(--foreground)] mt-0.5">
                                {{ $sequence->nom }}
                            </h3>
                        </div>

                        <!-- Badge Statut -->
                        @if ($sequence->is_closed)
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[var(--danger)]/10 text-[var(--danger)] border border-[var(--danger)]/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--danger)]"></span>
                                Fermée
                            </span>
                        @elseif($isExpired)
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[var(--warning)]/10 text-[var(--warning)] border border-[var(--warning)]/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--warning)]"></span>
                                Expirée
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[var(--success)]/10 text-[var(--success)] border border-[var(--success)]/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--success)] animate-pulse"></span>
                                Ouverte
                            </span>
                        @endif
                    </div>

                    <!-- Formulaire de Modification -->
                    <form action="{{ route('admin.sequences.update', $sequence->id) }}" method="POST"
                        class="p-5 space-y-4 flex-1 flex flex-col justify-between">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <!-- Date Limite -->
                            <div>
                                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                                    Date limite de saisie
                                </label>
                                <div class="relative">
                                    <input type="datetime-local" name="submission_deadline"
                                        value="{{ $sequence->submission_deadline ? \Carbon\Carbon::parse($sequence->submission_deadline)->format('Y-m-d\TH:i') : '' }}"
                                        class="w-full px-3 py-2 text-sm rounded-xl border border-[var(--input)] bg-[var(--background)] text-[var(--foreground)] focus:outline-none focus:ring-2 focus:ring-[var(--ring)] transition">
                                </div>
                            </div>

                            <!-- Interrupteur / Checkbox Clôture manuelle -->
                            <div
                                class="flex items-center justify-between p-3 rounded-xl bg-[var(--secondary)] border border-[var(--border)]">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="lock" class="w-4 h-4 text-slate-500"></i>
                                    <span class="text-xs font-medium text-[var(--secondary-foreground)]">Verrouiller
                                        manuellement</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_closed" value="1" class="sr-only peer"
                                        {{ $sequence->is_closed ? 'checked' : '' }}>
                                    <div
                                        class="w-9 h-5 bg-slate-300 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--primary)]">
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Bouton Enregistrer -->
                        <button type="submit"
                            class="w-full mt-4 py-2.5 px-4 inline-flex items-center justify-center gap-2 rounded-full text-sm font-semibold text-[var(--primary-foreground)] bg-[var(--primary)] hover:opacity-90 active:scale-[0.98] transition shadow-sm">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Mettre à jour</span>
                        </button>
                    </form>

                </div>
            @empty
                <div class="col-span-full p-12 text-center rounded-2xl bg-[var(--card)] border border-[var(--border)]">
                    <i data-lucide="calendar-x" class="w-12 h-12 mx-auto text-slate-400 mb-3"></i>
                    <h3 class="text-base font-semibold text-[var(--foreground)]">Aucune séquence trouvée</h3>
                    <p class="text-sm text-slate-500 mt-1">Veuillez d'abord configurer le calendrier de l'année scolaire
                        active.</p>
                </div>
            @endforelse
        </div>

    </div>

    <!-- Script pour réinitialiser les icônes Lucide -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
@endsection
