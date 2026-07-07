@extends('layouts.admin.admin-layout')

@section('content')
    @if ($errors->any())
        <div class="p-4 mb-4 bg-red-500/10 border border-red-500 rounded-xl text-red-600 text-[10px] font-black uppercase">
            @foreach ($errors->all() as $error)
                <p><x-lucide-alert-triangle class="w-4 h-4 inline-block mr-2" /> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="mb-8">
        <h1 class="text-xl   text-foreground tracking-tight">Inscription d'un Nouvel Élève</h1>
        <p class="text-xs text-muted-foreground   tracking-tighter text-primary">Année Scolaire :
            {{ $anneeActive->libelle }}</p>
        <div class="">
            <a href="{{ route('admin.students.index') }}"
                class="inline-flex items-center gap-2 bg-secondary/50 text-secondary-foreground px-4 py-2 rounded-xl font-bold text-[10px] tracking-widest hover:bg-secondary/70 transition-all">
                <x-lucide-arrow-left class="w-4 h-4" /> Retour à la liste
            </a>
        </div>
    </div>

    <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-card p-8 rounded-2xl border border-border shadow-sm">
                    <h2 class="text-[10px] font-black  text-primary mb-6 tracking-widest flex items-center gap-2">
                        <x-lucide-user-graduate class="w-4 h-4" /> État Civil de l'Élève
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-bold  text-muted-foreground ml-1">Nom de famille</label>
                            <input type="text" name="nom" placeholder="ex: SIMO"
                                class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm font-bold  outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                                required>

                        </div>

                        <div>
                            <label class="text-[10px] font-bold  text-muted-foreground ml-1">Prénoms</label>
                            <input type="text" name="prenom" placeholder="ex: Boris Aubin"
                                class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm font-bold outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                            @error('prenom')
                                <span class="text-xs text-danger mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="text-[10px] font-bold  text-muted-foreground ml-1">Date de
                                naissance</label>
                            <input type="date" name="date_naissance"
                                class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                                required>
                            @error('date_naissance')
                                <span class="text-xs text-danger mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="text-[10px] font-bold  text-muted-foreground ml-1">Genre / Sexe</label>
                            <select name="sexe"
                                class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm font-bold outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                                required>
                                <option value="" disabled selected>Choisir...</option>
                                @foreach ($sexes as $sexe)
                                    <option value="{{ $sexe }}">
                                        {{ $sexe == 'M' ? 'Masculin' : ($sexe == 'F' ? 'Féminin' : $sexe) }}
                                        ({{ $sexe }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Lieu de naissance -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Lieu de naissance
                            </label>

                            <input type="text" name="lieu_naissance" placeholder="Ex : Bafoussam"
                                class="w-full bg-secondary border border-gray-300 rounded-lg py-3 px-4 text-sm outline-none focus:ring-2 focus:ring-primary/20">

                            @error('lieu_naissance')
                                <span class="text-xs text-danger mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Statut élève -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Statut de l'élève (Redoublement)
                            </label>

                            <div class="border border-gray-300 rounded-lg px-4 py-3 h-[50px] flex items-center">
                                <div class="flex items-center gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <x-lucide-circle class="w-4 h-4 text-primary" />
                                        <span class="text-sm text-gray-600">Nouvel élève</span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="est_redoublant" value="1"
                                            class="w-4 h-4 text-primary">
                                        <span class="text-sm text-gray-600">Redoublant</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>




                </div>




                <div class="bg-card p-8 rounded-2xl border border-border shadow-sm">
                    <h2 class="text-[10px] font-black  text-primary mb-6 tracking-widest flex items-center gap-2">
                        <x-lucide-phone class="w-4 h-4" /> Contact Urgence (Parents)
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-bold  text-muted-foreground ml-1">Téléphone
                                Parent</label>
                            <input type="tel" name="telephone_parent" placeholder="ex: 6xx xx xx xx"
                                class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                        </div>
                        @error('telephone_parent')
                            <span class="text-xs text-danger mt-1">{{ $message }}</span>
                        @enderror
                        <div>
                            <label class="text-[10px] font-bold  text-muted-foreground ml-1">Adresse /
                                Quartier</label>
                            <input type="text" name="adresse" placeholder="ex: Djeleng V"
                                class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                            @error('adresse')
                                <span class="text-xs text-danger mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-card p-8 rounded-2xl border border-border shadow-sm">
                    <h2 class="text-[10px] font-black uppercase text-primary mb-6 tracking-widest flex items-center gap-2">
                        <x-lucide-school class="w-4 h-4" /> Affectation
                    </h2>

                    <div>
                        <label class="text-[10px] font-bold  text-muted-foreground ml-1">Classe de
                            destination</label>
                        <select name="classe_id"
                            class="w-full bg-secondary border-border rounded-xl py-3 px-4 mt-1 text-sm font-black  outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                            required>
                            @foreach ($niveaux as $niv)
                                <optgroup label="{{ $niv->nom }}">
                                    @foreach ($niv->classes as $classe)
                                        <option value="{{ $classe->id }}">{{ $niv->nom }} {{ $classe->nom }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-8 border-t border-border pt-6">
                        <button type="submit"
                            class="w-full bg-primary text-white font-black py-4 rounded-xl shadow-lg shadow-primary/30 hover:scale-[1.02] transition-all  text-xs tracking-widest">
                            Valider l'Inscription
                        </button>
                    </div>
                </div>

                <div class="bg-card p-8 rounded-2xl border border-border shadow-sm text-center">
                    <h2 class="text-[10px] font-black  text-muted-foreground mb-4 tracking-widest text-left">Photo
                        d'identité</h2>
                    <div
                        class="w-32 h-32 bg-secondary rounded-2xl mx-auto mb-4 border-2 border-dashed border-border flex items-center justify-center overflow-hidden">
                        <x-lucide-camera class="w-8 h-8 text-muted-foreground/30" />
                    </div>
                    <input type="file" name="photo" class="text-[10px] text-muted-foreground">
                </div>
            </div>
        </div>
    </form>
@endsection
