@extends('layouts.admin.admin-layout')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-foreground mb-2">Ajouter un Groupe de Matières</h1>
        <p class="text-sm text-gray-500 mb-8">Définissez une nouvelle catégorie pour organiser les matières sur le bulletin.</p>

        <form action="{{ route('admin.groupes.store') }}" method="POST" class="bg-card p-6 rounded-2xl border border-border shadow-sm">
            @csrf
            
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nom du groupe</label>
                <input type="text" name="nom" required 
                    class="w-full px-4 py-3 rounded-xl border border-border focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
                    placeholder="Ex: Matières Littéraires">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Ordre d'affichage</label>
                <input type="number" name="ordre" required min="1" value="1"
                    class="w-full px-4 py-3 rounded-xl border border-border focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
                    placeholder="Ex: 1">
                <p class="text-xs text-gray-400 mt-2">Plus le chiffre est petit, plus le groupe apparaîtra haut sur le bulletin.</p>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.groupes.index') }}" class="px-6 py-3 text-sm font-bold text-gray-600 hover:text-foreground transition-colors">Annuler</a>
                <button type="submit" class="px-8 py-3 bg-primary text-white rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-105 transition-all">
                    Enregistrer le groupe
                </button>
            </div>
        </form>
    </div>
@endsection
