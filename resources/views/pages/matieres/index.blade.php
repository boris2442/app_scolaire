@extends('layouts.admin.admin-layout')

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-black uppercase text-foreground tracking-tight">Catalogue des Matières</h1>
    <p class="text-xs text-muted-foreground font-bold uppercase">Définissez la liste globale des enseignements de l'établissement</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-1">
        <div class="bg-card p-6 rounded-2xl border border-border shadow-sm sticky top-24">
            <h2 class="text-[10px] font-black uppercase text-primary mb-5 tracking-widest flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Ajouter une matière
            </h2>

            <form action="{{ route('settings.matieres.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold uppercase text-muted-foreground ml-1">Nom complet</label>
                    <input type="text" name="nom" placeholder="ex: Mathématiques" 
                        class="w-full bg-secondary border-border rounded-xl text-sm px-4 py-3 mt-1 focus:ring-2 focus:ring-primary/20 outline-none transition-all" required>
                </div>

                <div>
                    <label class="text-[10px] font-bold uppercase text-muted-foreground ml-1">Code court (Trigramme)</label>
                    <input type="text" name="code" placeholder="ex: MATH" 
                        class="w-full bg-secondary border-border rounded-xl text-sm px-4 py-3 mt-1 focus:ring-2 focus:ring-primary/20 outline-none transition-all uppercase" required>
                </div>

                <button class="w-full bg-primary text-white font-black py-4 rounded-xl hover:shadow-lg hover:shadow-primary/30 transition-all uppercase text-[10px] tracking-widest">
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
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-muted-foreground">Code</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-muted-foreground">Nom de la matière</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-muted-foreground text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($matieres as $matiere)
                        <tr class="hover:bg-secondary/20 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="bg-primary/10 text-primary text-[10px] font-black px-2 py-1 rounded uppercase">
                                    {{ $matiere->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-sm text-foreground">
                                {{ $matiere->nom }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    {{-- Edit (Tu peux faire une page simple ou un prompt) --}}
                                    <button class="text-muted-foreground hover:text-primary transition-colors p-2">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    
                                    {{-- Delete --}}
                                    <form action="{{ route('settings.matieres.destroy', $matiere) }}" method="POST" onsubmit="return confirm('Supprimer cette matière du catalogue ?')">
                                        @csrf @method('DELETE')
                                        <button class="text-muted-foreground hover:text-danger transition-colors p-2">
                                            <i class="fas fa-trash-alt text-xs"></i>
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
            
            @if($matieres->hasPages())
                <div class="p-4 border-t border-border bg-secondary/10">
                    {{ $matieres->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
