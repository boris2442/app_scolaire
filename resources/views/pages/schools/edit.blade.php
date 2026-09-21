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
                        <label for='nom' class="block text-sm font-medium mb-2">Nom officiel de l'établissement
                            (FR)</label>
                        <input type="text" name="nom" value="{{ old('nom', $school->nom) }}"
                            class="w-full bg-secondary border-border rounded-lg focus:ring-primary focus:border-primary px-4 py-2.5"
                            id='nom' placeholder="Ex: Lycée Classique de .....">
                    </div>

                    <div>
                        <label for='english_name' class="block text-sm font-medium mb-2">Nom officiel de l'établissement
                            (EN)</label>
                        <input type="text" name="english_name" id='english_name'
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            placeholder="School Name in English" value="{{ old('english_name', $school->english_name) }}">
                    </div>

                    <div>
                        <label for='code_ecole' class="block text-sm font-medium mb-2">Code École (Ministère)</label>
                        <input type="text" name="code_ecole" id='code_ecole'
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            value="{{ old('code_ecole', $school->code_ecole) }}">
                    </div>

                    <div>
                        <label for='slogan' class="block text-sm font-medium mb-2">Slogan / Devise (FR)</label>
                        <input type="text" name="slogan" id='slogan'
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            placeholder="Travail - Paix - Patrie" value="{{ old('slogan', $school->slogan) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" for='english_slogan'>Slogan / Devise (EN)</label>
                        <input type="text" name="english_slogan" id='english_slogan'
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            placeholder="Work - Peace - Fatherland"
                            value="{{ old('english_slogan', $school->english_slogan) }}">
                    </div>

                    <!-- Contacts & Adresse -->
                    <div>
                        <label class="block text-sm font-medium mb-2" for='telephone'>Téléphone</label>
                        <input type="text" name="telephone" id='telephone'
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            value="{{ old('telephone', $school->telephone) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" for='email'>Email</label>
                        <input type="email" name="email" id='email'
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5"
                            value="{{ old('email', $school->email) }}">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2" for='adresse'>Adresse physique</label>
                        <textarea name="adresse" rows="2" class="w-full bg-secondary border-border rounded-lg px-4 py-2.5 resize-none"
                            id='adresse'>{{ old('adresse', $school->adresse) }}</textarea>
                    </div>

                    <!-- Localisation Administrative (FR) -->
                    <div>
                        <label class="block text-sm font-medium mb-2" for='region'>Région (FR)</label>
                        <input type="text" name="region" id='region'
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5" placeholder="Ex: Ouest"
                            value="{{ old('region', $school->region) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" for='department'>Département (FR)</label>
                        <input type="text" name="department" id='department'
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5" placeholder="Ex: Mifi"
                            value="{{ old('department', $school->department) }}">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2" for='sub_division'>Arrondissement / Sub-Division
                            (FR)</label>
                        <input type="text" name="sub_division" id='sub_division'
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5" placeholder="Ex: Bafoussam 1er"
                            value="{{ old('sub_division', $school->sub_division) }}">
                    </div>

                    <!-- Localisation Administrative (EN) -->
                    <div>
                        <label class="block text-sm font-medium mb-2" for='english_region'>Région (EN)</label>
                        <input type="text" name="english_region" id='english_region'
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5" placeholder="Ex: West"
                            value="{{ old('english_region', $school->english_region) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" for='english_department'>Département (EN)</label>
                        <input type="text" name="english_department" id='english_department'
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5" placeholder="Ex: Mifi"
                            value="{{ old('english_department', $school->english_department) }}">
                    </div>

                    {{-- <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2" for='english_sub_division'>Arrondissement /
                            Sub-Division (EN)</label>
                        <input type="text" name="english_sub_division" id='english_sub_division'
                            class="w-full bg-secondary border-border rounded-lg px-4 py-2.5" placeholder="Ex: Bafoussam I"
                            value="{{ old('english_sub_division', $school->english_sub_division) }}">
                    </div> --}}

                    <!-- Logo -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium">Logo actuel</label>
                        <div class="mt-2 mb-4">
                            @if ($school->logo)
                                <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo"
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
