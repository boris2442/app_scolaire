@extends('layouts.admin.admin-layout')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-xl font-black uppercase text-foreground">Personnel Enseignant</h1>
            <p class="text-[10px] text-muted-foreground font-bold uppercase tracking-widest">Gestion des comptes et profils
                instructeurs</p>
        </div>
        <a href="{{ route('admin.enseignants.create') }}"
            class="bg-primary text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">
            <i class="fas fa-plus mr-2"></i> Ajouter un prof
        </a>
    </div>

    <div class="bg-card rounded-2xl border border-border shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-secondary/50 border-b border-border">
                    <th class="p-4 text-[10px] font-black uppercase text-muted-foreground">Enseignant</th>
                    <th class="p-4 text-[10px] font-black uppercase text-muted-foreground">Matricule</th>
                    <th class="p-4 text-[10px] font-black uppercase text-muted-foreground">Département</th>
                    <th class="p-4 text-[10px] font-black uppercase text-muted-foreground">Statut Compte</th>
                    <th class="p-4 text-[10px] font-black uppercase text-muted-foreground text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($enseignants as $enseignant)
                    <tr class="hover:bg-secondary/10 transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-black text-xs">
                                    {{ substr($enseignant->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black uppercase">{{ $enseignant->user->name }}</p>
                                    <p class="text-[10px] text-muted-foreground">{{ $enseignant->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-xs font-bold text-primary">{{ $enseignant->matricule }}</td>
                        <td class="p-4">
                            @if ($enseignant->departement)
                                <span
                                    class="text-[10px] font-black uppercase bg-primary/5 text-primary border border-primary/10 px-2 py-1 rounded">
                                    {{ $enseignant->departement->nom }}
                                </span>
                            @else
                                <span
                                    class="text-[10px] font-black uppercase bg-red-50 text-red-500 border border-red-100 px-2 py-1 rounded">
                                    Département manquant
                                </span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span
                                class="inline-flex items-center gap-1.5 py-1 px-2 rounded-full text-[9px] font-black uppercase bg-green-100 text-green-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                Actif
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <button class="p-2 text-muted-foreground hover:text-primary transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-muted-foreground">
                            <p class="text-[10px] font-black uppercase tracking-widest">Aucun enseignant enregistré pour le
                                moment</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
