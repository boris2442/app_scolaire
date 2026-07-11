@extends('layouts.admin.admin-layout')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Conseil de classe : {{ $classe->nom }}</h1>
    
    <form action="{{ route('conseil.valider', $classe->id) }}" method="POST">
        @csrf
        <div class="bg-card shadow rounded-lg overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-secondary text-secondary-foreground">
                        <th class="p-4">Élève</th>
                        <th class="p-4">Moyenne Annuelle</th>
                        <th class="p-4">Statut Suggéré</th>
                        <th class="p-4">Décision Finale</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eleves as $eleve)
                        @php
                            $moyenne = $eleve->calculerMoyenneAnnuelle($anneeActive);
                            $estAdmis = $moyenne >= $seuilReussite;
                        @endphp
                        <tr>
                            <td class="p-4">{{ $eleve->nom }}</td>
                            <td class="p-4 font-bold">{{ number_format($moyenne, 2) }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-xs {{ $estAdmis ? 'bg-success/20 text-success' : 'bg-danger/20 text-danger' }}">
                                    {{ $estAdmis ? 'ADMIS' : 'REDOUBLANT' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <select name="decisions[{{ $eleve->id }}]" class="border rounded p-1">
                                    <option value="admis" {{ $estAdmis ? 'selected' : '' }}>Admis</option>
                                    <option value="redouble" {{ !$estAdmis ? 'selected' : '' }}>Redoublant</option>
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button type="submit" class="mt-6 bg-primary text-primary-foreground px-6 py-2 rounded">
            Valider le passage de classe
        </button>
    </form>
</div>
@endsection
