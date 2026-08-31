@extends('layouts.admin.admin-layout')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('settings.years.index') }}" class="p-2 bg-secondary rounded-lg hover:text-primary">
        <x-lucide-arrow-left class="w-4 h-4" />
        </a>
        <h1 class="text-xl font-bold">Modifier l'année : {{ $annee_scolaire->libelle }}</h1>
    </div>

    <div class="bg-card p-8 rounded-xl border border-border shadow-sm">
        <form action="{{ route('settings.years.update', $annee_scolaire) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT') <div>
                <label class="block text-sm font-medium mb-2">Libellé de l'année</label>
                <input type="text" name="libelle" required
                       value="{{ old('libelle', $annee_scolaire->libelle) }}" 
                       class="w-full bg-secondary border-border rounded-lg px-4 py-2.5 focus:ring-primary">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Date de début</label>
                    <input type="date" name="date_debut"  required
                           value="{{ old('date_debut', $annee_scolaire->date_debut->format('Y-m-d')) }}" 
                           class="w-full bg-secondary border-border rounded-lg px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Date de fin</label>
                    <input type="date" name="date_fin"  required
                           value="{{ old('date_fin', $annee_scolaire->date_fin->format('Y-m-d')) }}" 
                           class="w-full bg-secondary border-border rounded-lg px-4 py-2.5">
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                    <x-lucide-check-circle class="w-4 h-4 mr-2" /> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
