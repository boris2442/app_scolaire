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

                    <!-- Informations Générales -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Nom officiel de l'établissement (FR)</label>
                        <input type="text" name="nom" value="{{ old('nom', $etablissement->nom) }}"
                            class="w-full bg-secondary border-border rounded-lg focus:ring-primary focus:border-primary px-4 py-2.5"
                            placeholder="Ex: Lycée Classique de .....">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Nom officiel de l'établissement (EN)</label>
                        <input type="text" name="english_name"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            placeholder="School Name in English"
                            value="{{ old('english_name', $etablissement->english_name) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Code École (Ministère)</label>
                        <input type="text" name="code_ecole"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            value="{{ old('code_ecole', $etablissement->code_ecole) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Slogan / Devise (FR)</label>
                        <input type="text" name="slogan"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            placeholder="Travail - Paix - Patrie" value="{{ old('slogan', $etablissement->slogan) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Slogan / Devise (EN)</label>
                        <input type="text" name="english_slogan"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            placeholder="Work - Peace - Fatherland"
                            value="{{ old('english_slogan', $etablissement->english_slogan) }}">
                    </div>

                    <!-- Contacts & Adresse -->
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
                        <textarea name="adresse" rows="2" class="w-full bg-secondary border-border rounded-lg px-4 py-2.5 resize-none">{{ old('adresse', $etablissement->adresse) }}</textarea>
                    </div>

                    <!-- Localisation Administrative (FR) -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Région (FR)</label>
                        <input type="text" name="region"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5" placeholder="Ex: Ouest"
                            value="{{ old('region', $etablissement->region) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Département (FR)</label>
                        <input type="text" name="department"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5" placeholder="Ex: Mifi"
                            value="{{ old('department', $etablissement->department) }}">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Arrondissement / Sub-Division (FR)</label>
                        <input type="text" name="sub_division"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5" placeholder="Ex: Bafoussam 1er"
                            value="{{ old('sub_division', $etablissement->sub_division) }}">
                    </div>

                    <!-- Localisation Administrative (EN) -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Région (EN)</label>
                        <input type="text" name="english_region"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5" placeholder="Ex: West"
                            value="{{ old('english_region', $etablissement->english_region) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Département (EN)</label>
                        <input type="text" name="english_department"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5" placeholder="Ex: Mifi"
                            value="{{ old('english_department', $etablissement->english_department) }}">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Arrondissement / Sub-Division (EN)</label>
                        <input type="text" name="english_sub_division"
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5" placeholder="Ex: Bafoussam I"
                            value="{{ old('english_sub_division', $etablissement->english_sub_division) }}">
                    </div>

                    <!-- Logo -->
                    <div class="md:col-span-2">
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

                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit"
                        class="bg-primary text-primary-foreground px-6 py-2.5 rounded-lg font-bold hover:opacity-90 transition-all shadow-lg shadow-primary/20 flex justify-center items-center">
                        <x-lucide-save class='mr-2 w-4 h-4' />
                        Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
