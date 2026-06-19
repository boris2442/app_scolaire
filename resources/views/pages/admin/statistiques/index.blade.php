@extends('layouts.admin.admin-layout')

@section('content')
    
        <h2 class="font-semibold text-xl text-foreground leading-tight">
            {{ __('Génération des Statistiques') }}
        </h2>
   

    <div class="py-12 bg-background min-h-screen">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-card text-card-foreground overflow-hidden shadow-sm sm:rounded-lg p-6 border border-border">
                
                @if (session('success'))
                    <div class="mb-4 p-4 bg-success/10 border-l-4 border-success text-success rounded-r-lg text-sm font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-4 bg-danger/10 border-l-4 border-danger text-danger rounded-r-lg text-sm font-medium">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.statistiques.generer_sequence') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="sequence_id" class="block text-sm font-medium text-foreground/80">Séquence</label>
                        <select id="sequence_id" name="sequence_id" required 
                            class="mt-1 block w-full rounded-md border-border bg-background text-foreground focus:border-primary focus:ring-primary shadow-sm">
                            <option value="" class="bg-card">-- Choisir la séquence --</option>
                            @foreach($sequences as $sequence)
                                <option value="{{ $sequence->id }}" class="bg-card">{{ $sequence->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="classe_id" class="block text-sm font-medium text-foreground/80">Classe</label>
                        <select id="classe_id" name="classe_id" required 
                            class="mt-1 block w-full rounded-md border-border bg-background text-foreground focus:border-primary focus:ring-primary shadow-sm">
                            <option value="" class="bg-card">-- Choisir la classe --</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" class="bg-card">{{ $classe->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit" 
                            class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:opacity-90 font-semibold uppercase text-xs tracking-wider transition ease-in-out duration-150 shadow-md">
                            Calculer et Classer la classe
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
