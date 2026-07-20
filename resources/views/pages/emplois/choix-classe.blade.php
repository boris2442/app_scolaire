@extends('layouts.admin.admin-layout')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Gestion des Emplois du Temps - Choix de la classe</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($classes as $classe)
            <div class="bg-white p-4 rounded-lg shadow border border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="font-bold text-lg text-gray-800">{{ $classe->nom }}</h2>
                    <p class="text-sm text-gray-500">Niveau : {{ $classe->niveau->nom ?? 'Standard' }}</p>
                </div>
                <a href="{{ route('emplois.classe', $classe->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Ouvrir
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
