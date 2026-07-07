@extends('layouts.admin.admin-layout')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-xl font-semi-bold  text-foreground tracking-tight">Catalogue des Matières</h1>
            <p class="text-xs text-muted-foreground   tracking-tighter">Définissez la liste globale des
                enseignements</p>
        </div>
        {{-- <div class=""> --}}
        <form action="{{ route('settings.matieres.index') }}" method="GET" class="relative group">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i
                        class="fas fa-search text-muted-foreground group-focus-within:text-primary transition-colors text-xs"></i>
                </div>

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une matière..."
                    class="block w-full md:w-72 bg-secondary/50 border  rounded-full  pl-10 pr-10 text-xs   tracking-widest  focus:ring-primary/20 focus:border-primary   placeholder:text-muted-foreground/50    bg-secondary border-border   px-4 py-2 mt-1 focus:ring-2 focus:ring-primary/20 outline-none transition-all">

                <button type="submit"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-primary text-muted-foreground transition-colors">
                 
                    <x-lucide-arrow-right class='w-4 h-4' />
                </button>
            </div>

            @if (request('search'))
                <a href="{{ route('settings.matieres.index') }}"
                    class="absolute -bottom-5 right-0 text-[9px] font-black  text-danger hover:underline">
                    Effacer la recherche
                </a>
            @endif
        </form>
        {{-- </div> --}}
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-1">
            <div class="bg-card p-6 rounded-2xl border border-border shadow-sm sticky top-24">
                <h2 class="text-[10px] font-black  text-primary mb-5 tracking-widest flex items-center gap-2">
                  <x-lucide-plus class='w-4 h-4'/>
                    Ajouter une matière
                </h2>

                <form action="{{ route('settings.matieres.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[12px] font-bold  text-muted-foreground ml-1">Nom complet</label>
                        <input type="text" name="nom" placeholder="ex: Mathématiques"
                            class="w-full bg-secondary border-border rounded text-sm px-4 py-2 mt-1 focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            required>
                        {{-- affichage des erreur --}}
                        @error('nom')
                            <span class="text-red-600 text-[10px]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="text-[12px] font-bold  text-muted-foreground ml-1">Code court (Trigramme)</label>
                        <input type="text" name="code" placeholder="ex: MATH"
                            class="w-full bg-secondary border-border rounded text-sm px-4 py-2 mt-1 focus:ring-2 focus:ring-primary/20 outline-none transition-all uppercase"
                            required>
                        @error('code')
                            <span class="text-red-600 text-[10px]">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-bold  mb-2">Groupe de matière</label>
                        <select name="groupe_matiere_id" class="w-full px-4 py-2 rounded border border-border bg-secondary">
                            <option value="">Sélectionner un groupe...</option>

                            {{-- On boucle sur la liste des groupes --}}
                            @foreach ($groupes as $groupe)
                                <option value="{{ $groupe->id }}"
                                    {{ isset($matiere) && $matiere->groupe_matiere_id == $groupe->id ? 'selected' : '' }}>
                                    {{ $groupe->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('groupe_matiere_id')
                            <span class="text-red-600 text-[10px]">{{ $message }}</span>
                        @enderror
                    </div>




                    <button
                        class="w-full bg-primary text-white  py-2 rounded hover:shadow-lg hover:shadow-primary/30 transition-all  text-[10px] tracking-widest px-4">
                        Enregistrer au catalogue
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-card rounded-2xl border border-border overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-secondary/50 border-b border-border">
                            <th class="px-6 py-4 text-[10px] font-black  text-muted-foreground">Code</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase text-muted-foreground">Nom de la matière
                            </th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase text-muted-foreground text-right">Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($matieres as $matiere)
                            <tr class="hover:bg-secondary/20 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="bg-primary/10 text-primary text-[10px] font-black px-2 py-1 rounded ">
                                        {{ $matiere->code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-sm text-foreground">
                                    {{ $matiere->nom }}<br />
                                    <span class="text-muted-foreground  text-[10px]  px-2 py-1 rounded italic ">
                                        {{ $matiere->groupeMatiere->nom ?? 'sans groupe de matiere' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('settings.matieres.edit', $matiere->id) }}"
                                            class="text-muted-foreground hover:text-primary transition-colors p-2">
                                            {{-- <i class="fas fa-edit text-xs"></i> --}}
                                        <x-lucide-edit class='w-4 h-4'/>
                                            Modifier
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('settings.matieres.destroy', $matiere) }}" method="POST"
                                            onsubmit="return confirm('Supprimer cette matière du catalogue ?')">
                                            @csrf @method('DELETE')
                                            <button class="text-muted-foreground hover:text-danger transition-colors p-2">
                                                {{-- <i class="fas fa-trash-alt text-xs"></i> --}}
                                                <x-lucide-trash class='w-4 h-4'/>
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-xs text-muted-foreground italic">
                                    Le catalogue est vide pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($matieres->hasPages())
                    <div class="p-4 border-t border-border bg-secondary/10">
                        {{ $matieres->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
