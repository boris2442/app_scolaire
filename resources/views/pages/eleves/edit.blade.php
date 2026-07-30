@extends('layouts.admin.admin-layout')

@section('content')
    @if ($errors->any())
        <div class="p-4 mb-4 bg-red-500/10 border border-red-500 rounded-xl text-red-600 text-[10px] font-black uppercase">
            @foreach ($errors->all() as $error)
                <p><x-lucide-alert-triangle class="w-4 h-4 inline-block mr-2" /> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-black uppercase text-foreground tracking-tight">Modifier le Dossier : {{ $eleve->nom }}
            </h1>
            <p class="text-xs text-muted-foreground font-bold uppercase tracking-tighter text-primary">
                Matricule : {{ $eleve->matricule }}
            </p>
        </div>
        <a href="{{ route('admin.students.show', $eleve->id) }}"
            class="text-[10px] font-black uppercase bg-secondary px-4 py-2 rounded-lg hover:bg-border transition-all">
            <x-lucide-arrow-left class="w-4 h-4 mr-2" /> Retour
        </a>
    </div>

    <form action="{{ route('admin.students.update', $eleve->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-card p-8 rounded-2xl border border-border shadow-sm">
                    <h2 class="text-[10px] font-black uppercase text-primary mb-6 tracking-widest flex items-center gap-2">
                        État Civil de l'Élève
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-bold uppercase text-muted-foreground ml-1">Nom de famille</label>
                            <input type="text" name="nom" value="{{ old('nom', $eleve->nom) }}"
                                class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm font-bold uppercase outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                                required>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold uppercase text-muted-foreground ml-1">Prénoms</label>
                            <input type="text" name="prenom" value="{{ old('prenom', $eleve->prenom) }}"
                                class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm font-bold outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                        </div>

                        <div>
                            <label class="text-[10px] font-bold uppercase text-muted-foreground ml-1">Date de
                                naissance</label>
                            <input type="date" name="date_naissance"
                                value="{{ old('date_naissance', $eleve->date_naissance) }}"
                                class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                                required>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold uppercase text-muted-foreground ml-1">Genre / Sexe</label>
                            <select name="sexe"
                                class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm font-bold outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                                required>
                                @foreach ($sexes as $sexe)
                                    <option value="{{ $sexe }}"
                                        {{ old('sexe', $eleve->sexe) == $sexe ? 'selected' : '' }}>
                                        {{ $sexe == 'M' ? 'Masculin' : ($sexe == 'F' ? 'Féminin' : $sexe) }}
                                        ({{ $sexe }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="text-[10px] font-bold uppercase text-muted-foreground ml-1">Lieu de naissance</label>
                        <input type="text" name="lieu_naissance"
                            value="{{ old('lieu_naissance', $eleve->lieu_naissance) }}"
                            class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                </div>

                <div class="bg-card p-8 rounded-2xl border border-border shadow-sm">
                    <h2 class="text-[10px] font-black uppercase text-primary mb-6 tracking-widest flex items-center gap-2">
                        <x-lucide-phone class="w-4 h-4" /> Contact Urgence (Parents)
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-bold uppercase text-muted-foreground ml-1">Téléphone
                                Parent</label>
                            <input type="tel" name="telephone_parent"
                                value="{{ old('telephone_parent', $eleve->telephone_parent) }}"
                                class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase text-muted-foreground ml-1">Adresse /
                                Quartier</label>
                            <input type="text" name="adresse" value="{{ old('adresse', $eleve->adresse) }}"
                                class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-card p-8 rounded-2xl border border-border shadow-sm">
                    <h2 class="text-[10px] font-black uppercase text-primary mb-6 tracking-widest flex items-center gap-2">
                        <x-lucide-school class="w-4 h-4" /> Affectation Actuelle
                    </h2>

                    @php
                        // On récupère la classe actuelle pour l'année scolaire active
$currentClasseId = $eleve->inscriptions->where('annee_scolaire_id', $anneeActive->id)->first()
                            ?->classe_id;
                    @endphp

                    <div>
                        <label class="text-[10px] font-bold uppercase text-muted-foreground ml-1">Changer la classe</label>
                        <select name="classe_id"
                            class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm font-black uppercase outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                            required>
                            <option value="">-- Choisir une classe --</option>

                            @foreach ($classes as $classe)
                                <option value="{{ $classe->id }}"
                                    {{ old('classe_id', $currentClasseId ?? '') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-8 border-t border-border pt-6">
                        <button type="submit"
                            class="w-full bg-primary text-white font-black py-4 rounded-xl shadow-lg shadow-primary/30 hover:scale-[1.02] transition-all uppercase text-xs tracking-widest">
                            Enregistrer les Modifications
                        </button>
                    </div>
                </div>

                <div class="bg-card p-8 rounded-2xl border border-border shadow-sm text-center">
                    <h2 class="text-[10px] font-black uppercase text-muted-foreground mb-4 tracking-widest text-left">Photo
                        d'identité</h2>

                    <div class="relative group w-32 h-32 mx-auto mb-4">
                        <div
                            class="w-32 h-32 bg-secondary rounded-2xl border-2 border-dashed border-border flex items-center justify-center overflow-hidden">
                            @if ($eleve->photo)
                                <img id="preview" src="{{ asset('storage/' . $eleve->photo) }}"
                                    class="w-full h-full object-cover">
                            @else
                                <i id="icon-cam" class="fas fa-camera text-2xl text-muted-foreground/30"></i>
                                <img id="preview" src="#" class="hidden w-full h-full object-cover">
                            @endif
                        </div>
                    </div>

                    <input type="file" name="photo" id="photo-input" class="text-[10px] text-muted-foreground"
                        onchange="previewImage(this)">
                    <p class="text-[9px] text-muted-foreground mt-2 uppercase font-bold">Laissez vide pour conserver la
                        photo actuelle</p>
                </div>
            </div>
        </div>
    </form>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const icon = document.getElementById('icon-cam');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (icon) icon.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
