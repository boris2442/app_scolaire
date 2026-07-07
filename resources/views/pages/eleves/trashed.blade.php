@extends('layouts.admin.admin-layout')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-black uppercase text-foreground tracking-tight">Archives / Corbeille</h1>
            <p class="text-[10px] text-muted-foreground font-bold uppercase tracking-widest text-red-500">
                Liste des élèves supprimés logiquement
            </p>
        </div>
        <a href="{{ route('admin.students.index') }}"
            class="text-[10px] font-black uppercase bg-secondary px-4 py-2 rounded-lg hover:bg-border transition-all">
            <x-lucide-arrow-left class="w-4 h-4 inline-block mr-1" />
            Retour à la liste active
        </a>
    </div>

    @if (session('success'))
        <div
            class="p-4 mb-6 bg-green-500/10 border border-green-500 rounded-xl text-green-600 text-[10px] font-black uppercase">
            <x-lucide-check-circle class="w-4 h-4 inline-block mr-1" />
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-card rounded-2xl border border-border shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-secondary/50 border-b border-border">
                    <th class="p-4 text-[10px] font-black uppercase text-muted-foreground">Élève</th>
                    <th class="p-4 text-[10px] font-black uppercase text-muted-foreground">Matricule</th>
                    <th class="p-4 text-[10px] font-black uppercase text-muted-foreground text-center">Date Suppression</th>
                    <th class="p-4 text-[10px] font-black uppercase text-muted-foreground text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($elevesArchives as $eleve)
                    <tr class="hover:bg-secondary/20 transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-border flex items-center justify-center overflow-hidden">
                                    @if ($eleve->photo)
                                        <img src="{{ asset('storage/' . $eleve->photo) }}"
                                            class="w-full h-full object-cover grayscale">
                                    @else
                                        <x-lucide-user class="w-4 h-4 text-muted-foreground/50" />
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-black uppercase">{{ $eleve->nom }} {{ $eleve->prenom }}</p>
                                    <p class="text-[10px] text-muted-foreground font-bold uppercase">{{ $eleve->sexe }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-xs font-bold font-mono text-primary">{{ $eleve->matricule }}</td>
                        <td class="p-4 text-center text-[10px] font-bold text-muted-foreground uppercase">
                            {{ $eleve->deleted_at->format('d/m/Y à H:i') }}
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                <form action="{{ route('admin.students.restore', $eleve->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Restaurer"
                                        class="p-2 bg-green-500/10 text-green-600 rounded-lg hover:bg-green-500 hover:text-white transition-all">
                                        <x-lucide-undo class="w-4 h-4" />
                                    </button>
                                </form>

                                <form action="{{ route('admin.students.force-delete', $eleve->id) }}" method="POST"
                                    onsubmit="return confirm('ATTENTION : Cette action est irréversible. Supprimer définitivement ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Supprimer définitivement"
                                        class="p-2 bg-red-500/10 text-red-600 rounded-lg hover:bg-red-500 hover:text-white transition-all">
                                        {{-- <i class="fas fa-fire"></i> --}}
                                        <x-lucide-fire class='w-4 h-4 inline-block mr-1' />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center">
                            <i class="fas fa-trash-restore text-4xl text-muted-foreground/20 mb-4"></i>
                            <p class="text-[10px] font-black uppercase text-muted-foreground tracking-widest">La corbeille
                                est vide</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $elevesArchives->links() }}
    </div>
@endsection
