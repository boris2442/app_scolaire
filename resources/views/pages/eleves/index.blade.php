@extends('layouts.admin.admin-layout')

@section('content')
    <div class="min-h-screen bg-background p-4 lg:p-8 text-foreground">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
            <div>
                <h1 class="text-2xl font-black uppercase tracking-tighter">Registre des Élèves</h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="h-1 w-12 bg-primary rounded-full"></span>
                    <p class="text-[10px] font-bold uppercase text-primary tracking-widest">
                        {{ $eleves->total() }} Apprenants enregistrés
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.eleves.create') }}"
                class="bg-primary hover:opacity-90 text-primary-foreground px-6 py-3 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-3 text-xs font-black uppercase">
                <i class="fas fa-plus"></i> Nouvel Élève
            </a>
        </div>

        <form action="{{ route('admin.eleves.index') }}" method="GET" class="space-y-4 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="relative flex items-center">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="NOM OU MATRICULE..."
                        class="w-full bg-secondary border-border rounded-xl pl-4 pr-12 py-3 text-xs font-black uppercase outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                    <button type="submit"
                        class="absolute right-2 p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors">
                        <i class="fas fa-search text-sm"></i>
                    </button>
                </div>

                <select name="classe_id" onchange="this.form.submit()"
                    class="bg-secondary border-border rounded-xl px-4 py-3 text-xs font-black uppercase outline-none cursor-pointer focus:ring-2 focus:ring-primary/20">
                    <option value="">Toutes les classes</option>
                    @foreach ($niveaux as $n)
                        <optgroup label="{{ $n->nom }}" class="bg-card">
                            @foreach ($n->classes as $c)
                                <option value="{{ $c->id }}" {{ request('classe_id') == $c->id ? 'selected' : '' }}>
                                    {{ $n->nom }} {{ $c->nom }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-primary text-primary-foreground py-3 rounded-xl text-[10px] font-black uppercase hover:opacity-90 transition-all shadow-md shadow-primary/10">
                        Filtrer les données
                    </button>

                    @if (request()->anyFilled(['search', 'classe_id']))
                        <a href="{{ route('admin.eleves.index') }}"
                            class="bg-danger/10 text-danger border border-danger/20 px-4 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-danger hover:text-white transition-all flex items-center justify-center"
                            title="Réinitialiser">
                            <i class="fas fa-undo-alt"></i>
                        </a>
                    @endif
                </div>
            </div>

            @if (request('search') || request('classe_id'))
                <div class="flex items-center gap-2 animate-pulse">
                    <span class="text-[9px] font-black uppercase text-foreground/40">Filtre actif :</span>
                    <span
                        class="bg-primary/10 text-primary text-[8px] font-black px-2 py-0.5 rounded border border-primary/20 uppercase">
                        {{ request('search') ?? 'Classe spécifique' }}
                    </span>
                </div>
            @endif
        </form>

        <div class="bg-card border border-border rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-secondary/50 border-b border-border">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black uppercase text-foreground/50">Apprenant</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase text-foreground/50 text-center">Genre</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase text-foreground/50 text-center">Position
                                Académique</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase text-foreground/50 text-right">Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        @forelse($eleves as $eleve)
                            <tr class="hover:bg-secondary/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-10 w-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center font-black text-sm border border-primary/20">
                                            {{ strtoupper(substr($eleve->nom, 0, 1)) }}{{ strtoupper(substr($eleve->prenom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black uppercase">{{ $eleve->nom }}
                                                {{ $eleve->prenom }}</p>
                                            <p class="text-[10px] text-primary font-bold tracking-wider">
                                                {{ $eleve->matricule }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 py-1 bg-secondary rounded text-[10px] font-black">{{ $eleve->sexe }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php $ins = $eleve->inscriptions->first(); @endphp
                                    @if ($ins && $ins->classe)
                                        <span
                                            class="bg-primary/10 text-primary border border-primary/20 px-3 py-1 rounded-full text-[9px] font-black uppercase">
                                            {{ $ins->classe->niveau->nom }} {{ $ins->classe->nom }}
                                        </span>
                                    @else
                                        <span class="text-danger/50 text-[9px] font-black uppercase italic">Dossier en
                                            attente</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2 text-foreground/30">
                                        <a href="#" class="p-2 hover:text-primary transition-colors"><i
                                                class="fas fa-fingerprint"></i></a>
                                        <a href="#" class="p-2 hover:text-danger transition-colors"><i
                                                class="fas fa-trash-alt"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="py-20 text-center text-foreground/20 uppercase font-black text-xs italic">
                                    Aucune donnée disponible dans cette section
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $eleves->links() }}
        </div>
    </div>
@endsection
