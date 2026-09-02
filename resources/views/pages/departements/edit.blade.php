@extends('layouts.admin.admin-layout')

@section('content')
    <div class="p-6">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Modifier le Département</h1>
                <p class="text-sm text-gray-500 mt-1">Mise à jour des informations de la structure académique.</p>
            </div>
            <a href="{{ route('admin.departments.index') }}"
                class="flex items-center px-4 py-2 bg-secondary text-gray-700 rounded-xl border border-border hover:bg-border transition-all duration-300 shadow-sm text-sm font-medium">
              <x-lucide-arrow-left class="w-4 h-4 mr-2" />
                Retour à la liste
            </a>
        </div>

        <div class="max-w-3xl mx-auto">
            <form action="{{ route('admin.departments.update', $departement->id) }}" method="POST"
                class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
                @csrf
                @method('PUT')

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="nom" class="text-sm font-bold text-foreground ml-1">Nom du Département</label>
                            <div class="relative">
                                <input type="text" name="nom" id="nom"
                                    value="{{ old('nom', $departement->nom) }}" required
                                    class="w-full px-4 py-3 bg-secondary/50 border border-border rounded focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none text-sm"
                                    placeholder="Ex: Sciences et Technologies">
                            </div>
                            @error('nom')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="code" class="text-sm font-bold text-foreground ml-1">Code / Abréviation</label>
                            <input type="text" name="code" id="code"
                                value="{{ old('code', $departement->code) }}" required
                                class="w-full px-4 py-3 bg-secondary/50 border border-border rounded focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none text-sm uppercase"
                                placeholder="Ex: ST">
                            @error('code')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="description" class="text-sm font-bold text-foreground ml-1">Description
                            (Optionnel)</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full px-4 py-3  border border-border rounded focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none text-sm resize-none"
                            placeholder="Décrivez brièvement le rôle de ce département...">{{ old('description', $departement->description) }}</textarea>
                    </div>

                    <div class="p-4 bg-primary/5 border border-primary/10 rounded-xl flex items-start space-x-3">
                <x-lucide-info class="w-5 h-5 text-primary mt-0.5" />
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Toute modification sera immédiatement répercutée sur les classes et enseignants rattachés à ce
                            département. Assurez-vous que le <strong>Code</strong> reste unique.
                        </p>
                    </div>
                </div>

                <div class="p-6 bg-secondary/30 border-t border-border flex items-center justify-end space-x-4">
                    <button type="reset"
                        class="px-6 py-2.5 text-sm font-semibold text-gray-500 hover:text-foreground transition-colors">
                        Réinitialiser
                    </button>
                    <button type="submit"
                        class="px-8 py-2.5 bg-primary text-primary-foreground rounded-xl font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all text-sm">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
