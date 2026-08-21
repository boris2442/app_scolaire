@extends('layouts.admin.admin-layout')

@section('content')
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
                        <th class="p-4">Rôle Actuel</th>
                        <th class="p-4 text-right">Actions</th>
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
                            <td class="p-4">
                                <form action="{{ route('admin.users.update-role', $user) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" onchange="this.form.submit()"
                                        class="bg-secondary border border-border text-xs rounded-lg px-2 py-1.5 text-foreground font-medium focus:ring-1 focus:ring-primary">
                                        @foreach ($roles as $roleValue)
                                            @php
                                                $val = is_object($roleValue) ? $roleValue->value : $roleValue;
                                                $userVal = is_object($user->role) ? $user->role->value : $user->role;
                                            @endphp
                                            <option value="{{ $val }}" {{ $userVal === $val ? 'selected' : '' }}>
                                                {{ ucfirst($val) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
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
@endsection
