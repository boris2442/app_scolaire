@extends('layouts.admin.admin-layout')

@section('content')
    <form action="{{ route('seances.store') }}" method="POST" class="bg-card p-6 rounded-xl border border-border">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <!-- Classe et Matière (pré-filtrées) -->
            <div>
                <label for="matiere_id" class="block text-sm font-medium text-foreground">Matière</label>
                <select id="matiere_id" name="matiere_id" class="w-full mt-1 bg-background border-input rounded-lg">
                    @foreach ($matieres as $m)
                        <option value="{{ $m->id }}">{{ $m->nom }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Enseignant -->
            <div>
                <label for="enseignant_id" class="block text-sm font-medium text-foreground">Enseignant</label>
                <select id="enseignant_id" name="enseignant_id" class="w-full mt-1 bg-background border-input rounded-lg">
                    @foreach ($enseignants as $e)
                        <option value="{{ $e->id }}">{{ $e->user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Jour et Créneau -->
            <div>
                <label for="jour_id" class="block text-sm font-medium text-foreground">Jour</label>
                <select id="jour_id" name="jour_id" class="w-full mt-1 bg-background border-input rounded-lg">
                    @foreach ($jours as $j)
                        <option value="{{ $j->id }}">{{ $j->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="creneau_id" class="block text-sm font-medium text-foreground">Créneau</label>
                <select id="creneau_id" name="creneau_id" class="w-full mt-1 bg-background border-input rounded-lg">
                    @foreach ($creneaux as $c)
                        <option value="{{ $c->id }}">{{ $c->heure_debut }} - {{ $c->heure_fin }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="submit" class="mt-6 w-full bg-primary text-primary-foreground py-2 rounded-lg font-bold">
            Ajouter la séance
        </button>
    </form>
@endsection
