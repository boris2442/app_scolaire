@extends('layouts.admin.admin-layout')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-foreground">Configuration de l'Établissement</h1>
            <p class="text-gray-500">Ces informations apparaîtront sur les bulletins officiels.</p>
        </div>

        <form action="#" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-card p-6 rounded-xl border border-border shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Nom officiel de l'établissement</label>
                        <input type="text" name="nom" value="{{ old('nom', $etablissement->nom) }}"
                            class="w-full bg-secondary border-border rounded-lg focus:ring-primary focus:border-primary px-4 py-2.5"
                            placeholder="Ex: Lycée Classique de Bafoussam">
                    </div>

                    {{-- <div
                        class="md:col-span-2 flex items-center space-x-6 bg-background/50 p-4 rounded-lg border border-dashed border-border">
                        <div class="shrink-0">
                            <img class="h-16 w-16 object-cover rounded-lg" src="{{ asset('images/logo.jpg') }}"
                                alt="Current logo">
                        </div>
                        <label class="block">
                            <span class="sr-only">Choisir un logo</span>
                            <input type="file" name="logo"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-foreground hover:file:opacity-90">
                        </label>
                    </div> --}}



                    <div class="mb-4">
                        <label class="block text-sm font-medium">Logo actuel</label>
                        <div class="mt-2 mb-4">
                            @if ($etablissement->logo)
                                <img src="{{ asset('storage/' . $etablissement->logo) }}" alt="Logo"
                                    class="h-20 w-20 object-contain border rounded-lg p-1 bg-white">
                            @else
                                <span class="text-gray-400 text-xs italic">Aucun logo configuré</span>
                            @endif
                        </div>
                        <input type="file" name="logo" class="block w-full text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Téléphone</label>
                        <input type="text" name="telephone"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            value="{{ old('telephone', $etablissement->telephone) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Email</label>
                        <input type="email" name="email"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            value="{{ old('email', $etablissement->email) }}">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Adresse physique</label>
                        <textarea name="adresse" rows="2" class="w-full bg-secondary border-border rounded-lg px-4 py-2.5">{{ old('adresse', $etablissement->adresse) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Slogan / Devise</label>
                        <input type="text" name="slogan"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            placeholder="Travail - Paix - Patrie" value="{{ old('slogan', $etablissement->slogan) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Code École (Ministère)</label>
                        <input type="text" name="code_ecole"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            value="{{ old('code_ecole', $etablissement->code_ecole) }}">
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit"
                        class="bg-primary text-primary-foreground px-6 py-2.5 rounded-lg font-bold hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                        <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
