@extends('layouts.admin.admin-layout')

@section('content')
    <div class="min-h-screen bg-background p-4 lg:p-8 text-foreground relative">



        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Registre des élèves</h1>
                <p class="text-sm text-muted-foreground">{{ $stats['total'] }} apprenants enregistrés cette année</p>
            </div>


            <div class="relative inline-block text-left menu-wrapper">
                <button class="menu-trigger p-2 hover:bg-secondary rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="1" />
                        <circle cx="12" cy="5" r="1" />
                        <circle cx="12" cy="19" r="1" />
                    </svg>
                </button>

                <div
                    class="menu-content hidden absolute right-0 mt-2 w-56 bg-card border border-border rounded-xl shadow-lg z-50 overflow-auto max-h-[200px]">
                    <div class="py-1">
                        <header
                            class="px-4 py-2 text-[10px] font-bold text-muted-foreground uppercase tracking-widest border-b border-border mb-1">
                            Actions
                        </header>

                        <a href="{{ route('admin.students.trashed') }}"
                            class="flex items-center gap-3 px-4 py-2 text-red-500 hover:bg-secondary transition-colors">
                            <x-lucide-trash-2 class="w-4 h-4" /> Voir la corbeille
                            ({{ \App\Models\Eleve::onlyTrashed()->count() }})
                        </a>

                        <a href="{{ route('admin.students.create') }}"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors">
                            <x-lucide-plus class="w-4 h-4" /> Add student
                        </a>

                        <a href="{{ route('admin.students.export') }}"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors">
                            <x-lucide-file-up class="w-4 h-4" /> Exporter en Excel
                        </a>
                        <a href="{{ route('admin.inscriptions.export') }}"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors">
                            <x-lucide-file-up class="w-4 h-4" /> Exporter en Excel les inscrits
                        </a>
                        {{-- <a href="{{ route('admin.inscriptions.export') }}"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors">
                            <x-lucide-file-up class="w-4 h-4" /> Exporter en Excel les inscrits
                        </a> --}}
                        {{-- <a href="{{ route('admin.students.importer') }}"
                            class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-secondary transition-colors">
                            <x-lucide-file-up class="w-4 h-4" /> Importer depuis Excel
                        </a> --}}






                        {{-- On n'affiche le bouton d'impression que si une classe est filtrée --}}
                        @if (request()->filled('classe_id'))
                            <a href="{{ route('admin.eleves.imprimer', ['classe_id' => request('classe_id')]) }}"
                                target="_blank"
                                class="flex items-center gap-3 px-4 py-2 text-sm text-primary font-bold hover:bg-primary/10 transition-colors">
                                <x-lucide-printer class="w-4 h-4" /> Imprimer cette classe
                            </a>
                        @else
                            <div class="px-4 py-2 text-[10px] text-muted-foreground italic">
                                Filtrez une classe pour imprimer
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('click', function(e) {
                // 1. Détecter si on a cliqué sur un bouton de menu
                const trigger = e.target.closest('.menu-trigger');
                const allMenus = document.querySelectorAll('.menu-content');

                if (trigger) {
                    // Trouver le menu associé au bouton cliqué
                    const currentMenu = trigger.nextElementSibling;

                    // Fermer tous les autres menus ouverts avant d'ouvrir celui-ci
                    allMenus.forEach(menu => {
                        if (menu !== currentMenu) menu.classList.add('hidden');
                    });

                    // Toggle l'état du menu cliqué
                    currentMenu.classList.toggle('hidden');
                } else {
                    // Si on clique ailleurs sur la page, fermer tous les menus
                    allMenus.forEach(menu => menu.classList.add('hidden'));
                }
            });
        </script>


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

            <a href="{{ route('admin.students.create') }}"
                class="bg-primary hover:opacity-90 text-primary-foreground px-6 py-3 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-3 text-xs font-black ">
                <x-lucide-plus class="w-4 h-4" /> Nouvel Élève
            </a>



            <details class="group mb-6 bg-card rounded-2xl border border-border shadow-sm overflow-hidden transition-all">
                <!-- Bouton Déclencheur (Toggle natif) -->
                <summary
                    class="cursor-pointer p-4 font-semibold text-sm flex items-center justify-between bg-card hover:bg-secondary/40 transition select-none list-none">
                    <span class="flex items-center gap-2 text-foreground">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Importer des élèves (Excel)
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 transform group-open:rotate-180 transition-transform duration-200 text-foreground/60"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>

                <!-- Contenu qui s'affiche au dépliement -->
                <div class="p-6 border-t border-border space-y-6 bg-secondary/10">
                    <div>
                        <h3 class="text-lg font-bold text-foreground">Assistant d'importation massive</h3>
                        <p class="text-xs text-foreground/60">Sélectionnez la classe cible, joignez votre fichier Excel et
                            assurez-vous qu'il respecte la structure ci-dessous.</p>
                    </div>

                    <!-- Formulaire d'importation -->
                    <form action="{{ route('admin.students.importer') }}" method="POST" enctype="multipart/form-data"
                        class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        @csrf
                        <input type="hidden" name="annee_id" value="{{ $anneeActive->id }}">

                        <!-- Choix de la classe -->
                        <div>
                            <label class="block text-xs font-semibold text-foreground/70 mb-1">Classe de destination <span
                                    class="text-red-500">*</span></label>
                            <select name="classe_id" required
                                class="w-full px-3 py-2 bg-secondary text-secondary-foreground text-sm rounded-lg border border-border focus:ring-2 focus:ring-primary">
                                <option value="">-- Choisir la classe --</option>
                                @foreach ($classes as $classe)
                                    <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fichier Excel -->
                        <div>
                            <label class="block text-xs font-semibold text-foreground/70 mb-1">Fichier Excel (.xlsx, .xls,
                                .csv) <span class="text-red-500">*</span></label>
                            <input type="file" name="fichier_excel" accept=".xlsx, .xls, .csv" required
                                class="w-full text-xs text-foreground/70 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-primary-foreground hover:file:bg-primary/90">
                        </div>

                        <!-- Bouton de soumission -->
                        <div>
                            <button type="submit"
                                class="w-full px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition shadow">
                                Lancer l'importation
                            </button>
                        </div>
                    </form>

                    <!-- Aperçu visuel / Maquette du template Excel attendu -->
                    <div class="pt-4 border-t border-border">
                        <h4
                            class="text-xs font-bold text-foreground uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Modèle de structure requis pour les colonnes de votre fichier Excel :
                        </h4>

                        <div class="overflow-x-auto rounded-xl border border-border bg-secondary/20">
                            <table class="w-full text-left text-xs font-mono">
                                <thead class="bg-secondary text-secondary-foreground font-sans font-semibold">
                                    <tr>
                                        <th class="p-2 border-r border-border">matricule <span
                                                class="text-foreground/40 font-normal text-[10px] block">(Optionnel)</span>
                                        </th>
                                        <th class="p-2 border-r border-border text-red-500">nom <span
                                                class="font-sans font-normal text-[10px] block text-foreground/50">(Requis)</span>
                                        </th>
                                        <th class="p-2 border-r border-border">prenom <span
                                                class="text-foreground/40 font-normal text-[10px] block">(Optionnel)</span>
                                        </th>
                                        <th class="p-2 border-r border-border">sexe <span
                                                class="text-foreground/40 font-normal text-[10px] block">(M ou F)</span>
                                        </th>
                                        <th class="p-2 border-r border-border">date_naissance <span
                                                class="text-foreground/40 font-normal text-[10px] block">(AAAA-MM-JJ)</span>
                                        </th>
                                        <th class="p-2 border-r border-border">lieu_naissance <span
                                                class="text-foreground/40 font-normal text-[10px] block">(Optionnel)</span>
                                        </th>
                                        <th class="p-2 border-r border-border">telephone_parent <span
                                                class="text-foreground/40 font-normal text-[10px] block">(Optionnel)</span>
                                        </th>
                                        <th class="p-2">est_redoublant <span
                                                class="text-foreground/40 font-normal text-[10px] block">(0 ou 1)</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-foreground/70">
                                    <tr class="border-t border-border bg-card/50">
                                        <td class="p-2 border-r border-border italic text-foreground/40">Généré auto si
                                            vide</td>
                                        <td class="p-2 border-r border-border font-semibold text-foreground">Simo</td>
                                        <td class="p-2 border-r border-border">Boris Aubin</td>
                                        <td class="p-2 border-r border-border">M</td>
                                        <td class="p-2 border-r border-border">2005-04-12</td>
                                        <td class="p-2 border-r border-border">Bafoussam</td>
                                        <td class="p-2 border-r border-border">690000000</td>
                                        <td class="p-2">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-[11px] text-foreground/50 mt-2">
                            💡 Note : La première ligne de votre fichier Excel doit impérativement contenir ces noms exacts
                            de colonnes en minuscules pour que le système puisse les lire correctement.
                        </p>
                    </div>
                </div>
            </details>



        </div>

        <form action="{{ route('admin.students.index') }}" method="GET" class="space-y-4 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="relative flex items-center">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="NOM OU MATRICULE..."
                        class="w-full bg-secondary border-border rounded-full pl-4 pr-12 py-3 text-xs font-black  outline-none focus:ring-2 focus:ring-primary/20 transition-all lowercase">
                    <button type="submit"
                        class="absolute right-2 p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors">
                        <x-lucide-search class="w-4 h-4" />
                    </button>
                </div>

                {{-- <select name="classe_id" onchange="this.form.submit()"
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
                </select> --}}
                <select name="classe_id" onchange="this.form.submit()"
                    class="bg-secondary border-border rounded-xl px-4 py-3 text-xs font-black outline-none cursor-pointer focus:ring-2 focus:ring-primary/20">
                    <option value="">Toutes les classes</option>

                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" {{ request('classe_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->nom }}
                        </option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-primary text-primary-foreground py-3 rounded-xl text-[10px] font-black  hover:opacity-90 transition-all shadow-md shadow-primary/10">
                        Filtrer les données
                    </button>

                    @if (request()->anyFilled(['search', 'classe_id']))
                        <a href="{{ route('admin.students.index') }}"
                            class="bg-danger/10 text-danger border border-danger/20 px-4 py-3 rounded-xl text-[10px] font-black  hover:bg-danger hover:text-white transition-all flex items-center justify-center"
                            title="Réinitialiser">
                            <x-lucide-undo class="w-4 h-4" />
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
                                            class="h-5 w-5 bg-primary/10 text-primary rounded-full flex items-center justify-center  text-sm border border-primary/20">
                                            {{ strtoupper(substr($eleve->nom, 0, 1)) }}{{ strtoupper(substr($eleve->prenom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm  uppercase">
                                                <a href="{{ route('admin.students.show', $eleve) }}">
                                                    {{ $eleve->nom }}
                                                    {{ $eleve->prenom }}
                                                </a>
                                            </p>
                                            <p class="text-[10px] text-primary font-bold tracking-wider">
                                                {{ $eleve->matricule }}</p>
                                            <span class="text-[10px] text-primary font-bold tracking-wider">Inscrit le
                                                <i>{{ $eleve->created_at }}</i></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-2 text-center">
                                    <span class="px-2 py-1 bg-secondary rounded text-[10px] ">{{ $eleve->sexe }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php $ins = $eleve->inscriptions->first(); @endphp
                                    @if ($ins && $ins->classe)
                                        <span
                                            class="bg-primary/10 text-primary border border-primary/20 px-3 py-1 rounded-full text-[9px]  uppercase">
                                            {{ $ins->classe->nom }}
                                        </span>
                                    @else
                                        <span class="text-danger/50 text-[9px] font-black uppercase italic">Dossier en
                                            attente</span>
                                    @endif
                                </td>
                                <td class="px-6 py-2 text-right">
                                    <div class="flex justify-center items-center gap-2 text-foreground/30">

                                        <a href="{{ route('admin.students.show', $eleve) }}"
                                            title="Voir les détails"class="p-2 hover:text-primary transition-colors"><x-lucide-eye
                                                class="w-4 h-4" /></a>
                                                  @can('access-admin')
                                        <form action="{{ route('admin.students.destroy', $eleve->id) }}" method="POST"
                                            onsubmit="return confirm('Voulez-vous vraiment archiver cet élève ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                                {{-- Archiver --}}
                                            </button>
                                        </form>
                                        @endcan
                                        <a title="Modifier les informations de l'élève"
                                            href="{{ route('admin.students.edit', $eleve) }}"class="p-2 hover:text-danger transition-colors"><x-lucide-edit
                                                class="w-4 h-4" /></a>
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
