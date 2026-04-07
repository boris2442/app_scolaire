@extends('layouts.admin.admin-layout')

@section('content')
    <div class="min-h-screen bg-background p-4 lg:p-8 text-foreground relative">



        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Registre des élèves</h1>
                <p class="text-sm text-muted-foreground">{{ $stats['total'] }} apprenants enregistrés cette année</p>
            </div>

            <div class=" inline-block text-left" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false"
                    class="p-2 hover:bg-secondary rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="1" />
                        <circle cx="12" cy="5" r="1" />
                        <circle cx="12" cy="19" r="1" />
                    </svg>
                </button>
                <div x-show="open" x-transition
                    class="absolute right-0 mt-2 w-56 bg-card border border-border rounded-xl shadow-lg z-50 overflow-auto max-h-[200px]">
                    <div class="py-1">
                        <header
                            class="px-4 py-2 text-[10px] font-bold text-muted-foreground uppercase tracking-widest border-b border-border mb-1">
                            Actions</header>
                        <a href="{{ route('admin.eleves.trashed') }}"
                            class="flex items-center gap-3 px-4 py-2 text-red-500 hover:underline transition-colors"><i
                                class="fas fa-trash-alt mr-1 opacity-50 text-xs "></i>Voir la corbeille
                            ({{ \App\Models\Eleve::onlyTrashed()->count() }})</a>
                        <a href="{{ route('admin.eleves.create') }}"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors"><i
                                class="fas fa-print opacity-50 text-xs"></i> Add student</a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors"><i
                                class="fas fa-file-export opacity-50 text-xs"></i> Exporter en Excel</a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors"><i
                                class="fas fa-print opacity-50 text-xs"></i> Imprimer</a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors"><i
                                class="fas fa-file-export opacity-50 text-xs"></i> Exporter en Excel</a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors"><i
                                class="fas fa-print opacity-50 text-xs"></i> Imprimer</a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors"><i
                                class="fas fa-file-export opacity-50 text-xs"></i> Exporter en Excel</a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors"><i
                                class="fas fa-print opacity-50 text-xs"></i> Imprimer</a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors"><i
                                class="fas fa-file-export opacity-50 text-xs"></i> Exporter en Excel</a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors"><i
                                class="fas fa-print opacity-50 text-xs"></i> Imprimer</a>
                    </div>
                </div>
            </div>
        </div>




        <div class="max-w-7xl mx-auto px-6 mb-10">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                <div class="bg-card p-5 rounded-xl border border-border shadow-sm">
                    <p class="text-[11px] font-medium text-muted-foreground  tracking-wider mb-1">Apprenants</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-bold">{{ $stats['total'] }}</span>
                        <span class="text-[10px] text-primary font-medium">Inscrits</span>
                    </div>
                </div>

                <div class="bg-card p-5 rounded-xl border border-border shadow-sm">
                    <p class="text-[11px] font-medium text-muted-foreground uppercase tracking-wider mb-1">Parité F/G</p>
                    <div class="flex flex-col gap-2">
                        <span class="text-lg font-semibold">{{ $stats['filles'] }}f · {{ $stats['garcons'] }}g</span>
                        <div class="w-full h-1 bg-secondary rounded-full overflow-hidden flex">
                            @php $p = $stats['total'] > 0 ? ($stats['filles'] / $stats['total']) * 100 : 0; @endphp
                            <div class="h-full bg-primary transition-all duration-500" style="width: {{ $p }}%">
                            </div>
                        </div>
                    </div>
                </div>



                <div class="bg-card p-5 rounded-xl border border-border shadow-sm col-span-2 lg:col-span-1">
                    <p class="text-[11px] font-medium text-muted-foreground uppercase tracking-wider mb-2">Répartition
                        cycles</p>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($stats['cycles'] as $c)
                            <div class="flex flex-col">
                                <span class="text-[10px] text-muted-foreground font-bold">{{ $c['cycle'] }}</span>
                                <span class="text-xs font-semibold">{{ $c['total'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>



        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
            <div>
                <h1 class="text-2xl font-black  tracking-tighter">Registre des Élèves</h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="h-1 w-12 bg-primary rounded-full"></span>
                    <p class="text-[10px] font-bold  text-primary tracking-widest">
                        {{ $eleves->total() }} Apprenants enregistrés
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.eleves.create') }}"
                class="bg-primary hover:opacity-90 text-primary-foreground px-6 py-3 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-3 text-xs font-black ">
                <i class="fas fa-plus"></i> Nouvel Élève
            </a>
        </div>

        <form action="{{ route('admin.eleves.index') }}" method="GET" class="space-y-4 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="relative flex items-center">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="NOM OU MATRICULE..."
                        class="w-full bg-secondary border-border rounded-xl pl-4 pr-12 py-3 text-xs font-black  outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                    <button type="submit"
                        class="absolute right-2 p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors">
                        <i class="fas fa-search text-sm"></i>
                    </button>
                </div>

                <select name="classe_id" onchange="this.form.submit()"
                    class="bg-secondary border-border rounded-xl px-4 py-3 text-xs font-black  outline-none cursor-pointer focus:ring-2 focus:ring-primary/20">
                    <option value="">Toutes les classes</option>
                    @foreach ($niveaux as $n)
                        <optgroup label="{{ $n->nom }}" class="bg-card">
                            @foreach ($n->classes as $c)
                                <option value="{{ $c->id }}"
                                    {{ request('classe_id') == $c->id ? 'selected' : '' }}>
                                    {{ $n->nom }} {{ $c->nom }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-primary text-primary-foreground py-3 rounded-xl text-[10px] font-black  hover:opacity-90 transition-all shadow-md shadow-primary/10">
                        Filtrer les données
                    </button>

                    @if (request()->anyFilled(['search', 'classe_id']))
                        <a href="{{ route('admin.eleves.index') }}"
                            class="bg-danger/10 text-danger border border-danger/20 px-4 py-3 rounded-xl text-[10px] font-black  hover:bg-danger hover:text-white transition-all flex items-center justify-center"
                            title="Réinitialiser">
                            <i class="fas fa-undo-alt"></i>
                        </a>
                    @endif
                </div>
            </div>

            @if (request('search') || request('classe_id'))
                <div class="flex items-center gap-2 animate-pulse">
                    <span class="text-[9px] font-black  text-foreground/40">Filtre actif :</span>
                    <span
                        class="bg-primary/10 text-primary text-[8px] font-black px-2 py-0.5 rounded border border-primary/20 ">
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
                            <th class="px-6 py-4 text-[10px]  uppercase text-foreground/50 font-bold-200">Apprenant</th>
                            <th class="px-6 py-4 text-[10px]  uppercase text-foreground/50 text-center">Genre
                            </th>
                            <th class="px-6 py-4 text-[10px]  uppercase text-foreground/50 text-center">Position
                                Académique</th>
                            <th class="px-6 py-4 text-[10px]  uppercase text-foreground/50 text-right">Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        @forelse($eleves as $eleve)
                            <tr class="hover:bg-secondary/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-10 w-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center  text-sm border border-primary/20">
                                            {{ strtoupper(substr($eleve->nom, 0, 1)) }}{{ strtoupper(substr($eleve->prenom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm  uppercase">
                                                <a href="{{ route('admin.eleves.show', $eleve) }}">
                                                    {{ $eleve->nom }}
                                                    {{ $eleve->prenom }}
                                            </p>
                                            </a>
                                            <p class="text-[10px] text-primary font-bold tracking-wider">
                                                {{ $eleve->matricule }}</p>
                                            <span class="text-[10px] text-primary font-bold tracking-wider">Inscrit le
                                                <i>{{ $eleve->created_at }}</i></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 bg-secondary rounded text-[10px] ">{{ $eleve->sexe }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php $ins = $eleve->inscriptions->first(); @endphp
                                    @if ($ins && $ins->classe)
                                        <span
                                            class="bg-primary/10 text-primary border border-primary/20 px-3 py-1 rounded-full text-[9px]  uppercase">
                                            {{ $ins->classe->niveau->nom }} {{ $ins->classe->nom }}
                                        </span>
                                    @else
                                        <span class="text-danger/50 text-[9px] font-black uppercase italic">Dossier en
                                            attente</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2 text-foreground/30">
                                        <a href="{{ route('admin.eleves.show', $eleve) }}"
                                            title="Voir les détails"class="p-2 hover:text-primary transition-colors"><i
                                                class="fas fa-eye"></i></a>
                                        <form action="{{ route('admin.eleves.destroy', $eleve->id) }}" method="POST"
                                            onsubmit="return confirm('Voulez-vous vraiment archiver cet élève ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-trash-alt"></i> Archiver
                                            </button>
                                        </form>
                                        <a
                                    title="Modifier les informations de l'élève"
                                            href="{{ route('admin.eleves.edit', $eleve) }}"class="p-2 hover:text-danger transition-colors"><i
                                                class="fas fa-pen-alt"></i></a>
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
