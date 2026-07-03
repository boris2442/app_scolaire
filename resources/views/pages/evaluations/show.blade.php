@extends('layouts.admin.admin-layout')

@section('content')
    {{-- Formulaire de Création --}}
    <div class="bg-secondary/10 border border-white/5 p-8 rounded-3xl mb-10">
        <h2 class="text-sm font-bold uppercase mb-6 text-primary tracking-widest">Initialiser une Session de Notes</h2>

     <form action="{{ route('admin.notes.store_bulk', $evaluation->id) }}" method="POST">
    @csrf
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-white/10 text-primary uppercase text-xs">
                <th class="p-4">Élève</th>
                <th class="p-4">Note / 20</th>
                <th class="p-4">Appréciation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($eleves as $eleve)
                <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                    <td class="p-4 text-white font-medium">
                        {{ $eleve->nom }} {{ $eleve->prenom }}
                    </td>
                    <td class="p-4">
                        <input type="number" 
                               name="notes[{{ $eleve->id }}][valeur]" 
                               step="0.25" min="0" max="20"
                               class="bg-secondary/20 border border-white/10 rounded p-2 text-white w-24 focus:border-primary outline-none"
                               placeholder="10.5">
                    </td>
                    <td class="p-4">
                        <input type="text" 
                               name="notes[{{ $eleve->id }}][commentaire]" 
                               class="bg-secondary/20 border border-white/10 rounded-lg p-2 text-white w-full focus:border-primary outline-none"
                               placeholder="RAS">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-8 flex justify-end">
        <button type="submit" class="bg-primary hover:bg-primary/80 text-secondary font-black px-8 py-3 rounded-xl uppercase text-xs shadow-lg shadow-primary/20">
            Enregistrer les Notes
        </button>
    </div>
</form>
    </div>
@endsection
