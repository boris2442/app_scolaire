@extends('layouts.admin.admin-layout')

@section('content')
    <script>
        window.copyGeneratedPassword = function(buttonElement) {
            const passwordText = document.getElementById('passwordValue').textContent.trim();

            navigator.clipboard.writeText(passwordText).then(() => {
                const iconCopy = buttonElement.querySelector('.icon-copy');
                const iconCheck = buttonElement.querySelector('.icon-check');
                const textSpan = buttonElement.querySelector('.btn-text');

                // Bascule vers l'état "Copié !"
                if (iconCopy) iconCopy.classList.add('hidden');
                if (iconCheck) iconCheck.classList.remove('hidden');
                if (textSpan) textSpan.textContent = 'Copié !';

                // Retour à l'état initial après 2 secondes
                setTimeout(() => {
                    if (iconCopy) iconCopy.classList.remove('hidden');
                    if (iconCheck) iconCheck.classList.add('hidden');
                    if (textSpan) textSpan.textContent = 'Copier';
                }, 2000);
            }).catch(err => {
              
            });
        };
    </script>

    <!-- En-tête avec le titre et le compteur -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-black tracking-tighter">Gestion des Utilisateurs</h1>
            <p class="text-xs text-muted-foreground">
                Liste des comptes et attribution des rôles
                <span
                    class="ml-2 px-2 py-0.5 bg-secondary text-foreground text-[11px] font-bold rounded-full border border-border">
                    {{ $users->total() }} utilisateur(s)
                </span>
            </p>
        </div>
    </div>
    @if (session('generated_password'))
        <div
            class="p-4 rounded-xl bg-[var(--card)] border border-[var(--border)] text-[var(--foreground)] text-sm space-y-3 shadow-sm">
            <div class="flex items-center gap-2 text-[var(--success)] font-semibold">
                <x-lucide-check-circle-2 class="w-5 h-5" />
                <span>{{ session('success') }}</span>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-[var(--border)]">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Nouveau mot de passe généré :</span>
                    <code id="passwordValue"
                        class="px-3 py-1.5 rounded-lg bg-[var(--secondary)] text-[var(--primary)] font-mono font-bold text-base tracking-wider border border-[var(--border)]">
                        {{ session('generated_password') }}
                    </code>
                </div>

                <!-- Bouton de copie avec icônes Blade -->
                <button type="button" onclick="copyGeneratedPassword(this)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[var(--primary)] text-[var(--primary-foreground)] text-xs font-semibold hover:opacity-90 active:scale-95 transition cursor-pointer shadow-sm">
                    <span class="icon-copy flex items-center">
                        <x-lucide-copy class="w-3.5 h-3.5" />
                    </span>
                    <span class="icon-check hidden flex items-center">
                        <x-lucide-check class="w-3.5 h-3.5 text-emerald-300" />
                    </span>
                    <span class="btn-text">Copier</span>
                </button>
            </div>
        </div>
    @endif
    <!-- Barre de recherche -->
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 flex gap-3 items-center">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
                class="w-full bg-card border border-border rounded-full text-sm pl-4 pr-10 py-2.5 text-foreground focus:outline-none focus:ring-2 focus:ring-primary">

            <!-- Bouton Croix (apparaît uniquement si une recherche est en cours) -->
            @if (request('search'))
                <a href="{{ route('admin.users.index') }}"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition p-1"
                    title="Effacer la recherche">
                    <x-lucide-x class="w-4 h-4" />
                </a>
            @endif
        </div>

        <!-- Bouton Icône Recherche -->
        <button type="submit"
            class="bg-primary text-white p-2.5 rounded-full hover:opacity-95 transition flex items-center justify-center aspect-square"
            title="Rechercher">
            <x-lucide-search class="w-4 h-4" />
        </button>
    </form>

    <!-- Tableau des utilisateurs -->
    <!-- Le conteneur global avec overflow-x-auto -->
    <div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr
                        class="border-b border-border bg-secondary/50 text-[10px] text-muted-foreground uppercase tracking-wider">
                        <th class="p-4">Utilisateur</th>
                        <th class="p-4">Email / Contact</th>
                        @can('access-admin')
                            <th class="p-4">Rôle Actuel</th>
                            <th class="p-4 text-right">Actions</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-secondary/20 transition">
                            <td class="p-4 font-bold text-foreground">{{ $user->name }}</td>
                            <td class="p-4 text-muted-foreground">
                                {{ $user->email }}<br>
                                {{ $user->phone }}
                            </td>
                            @can('access-admin')
                                <td class="p-4">
                                    <form action="{{ route('admin.users.update-role', $user) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" onchange="this.form.submit()"
                                            class="bg-secondary border border-border text-xs rounded-lg px-2 py-1.5 text-foreground font-medium focus:ring-1 focus:ring-primary">
                                            @foreach ($roles as $roleValue)
                                                @php
                                                    $val = is_object($roleValue) ? $roleValue->value : $roleValue;
                                                    $userVal = is_object($user->role)
                                                        ? $user->role->value
                                                        : $user->role;
                                                @endphp
                                                <option value="{{ $val }}" {{ $userVal === $val ? 'selected' : '' }}>
                                                    {{ ucfirst($val) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                            @endcan
                            @can('access-admin')
                                <td class="p-4 text-right">
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-destructive hover:text-red-700 transition">
                                            <x-lucide-trash-2 class="w-4 h-4" />
                                        </button>
                                    </form>
                                </td>
                                <td class="p-4 text-right">
                                    <form method="POST" action="{{ route('admin.enseignants.reset-password', $user->id) }}"
                                        class="inline">
                                        @csrf
                                        @method('PUT')

                                        <button type="submit"
                                            onclick="return confirm('Générer un nouveau mot de passe pour {{ $user->name }} ?')"
                                            class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-lg transition"
                                            title="Générer un mot de passe">
                                            <x-lucide-key-round class="w-4 h-4" />
                                        </button>
                                    </form>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-xs text-muted-foreground italic">Aucun
                                utilisateur trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>
    <script>
        window.closeResetModal = function() {
            const modal = document.getElementById('resetPasswordModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        };











       
    </script>
@endsection
