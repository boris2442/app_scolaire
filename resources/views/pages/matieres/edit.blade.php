@extends('layouts.admin.admin-layout')

@section('content')
    <div class="max-w-xl mx-auto mt-10">
        <div class="bg-card p-8 rounded-2xl border border-border shadow-lg">
            <h2 class="text-lg font-bold mb-6">Modifier : {{ $matiere->nom }}</h2>

            <form action="{{ route('settings.matieres.update', $matiere->id) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="text-sm font-bold">Nom</label>
                    <input type="text" name="nom" value="{{ old('nom', $matiere->nom) }}"
                        class="w-full bg-secondary border rounded p-2 mt-1">
                </div>

                <div>
                    <label class="text-sm font-bold">Code</label>
                    <input type="text" name="code" value="{{ old('code', $matiere->code) }}"
                        class="w-full bg-secondary border rounded p-2 mt-1 uppercase">
                </div>

                <div>
                    <label class="text-sm font-bold">Groupe</label>
                    <select name="groupe_matiere_id" class="w-full bg-secondary border rounded p-2 mt-1">
                        @foreach ($groupes as $groupe)
                            <option value="{{ $groupe->id }}"
                                {{ $matiere->groupe_matiere_id == $groupe->id ? 'selected' : '' }}>
                                {{ $groupe->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3 pt-4">
                    <a href="{{ route('settings.matieres.index') }}"
                        class="px-4 py-2 bg-secondary rounded text-sm">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded text-sm">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection
