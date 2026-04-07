@extends('layouts.admin.admin-layout')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-xl font-black  text-foreground">Nouveau Personnel Enseignant</h1>
            <p class="text-[10px] text-muted-foreground font-bold uppercase tracking-widest">Création du compte d'accès et du
                profil professionnel</p>
        </div>

        <form action="{{ route('admin.enseignants.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-card p-8 rounded-2xl border border-border shadow-sm">

                <div class="space-y-4">
                    <h3 class="text-[10px] font-black  text-primary border-b border-border pb-2">Compte Utilisateur
                    </h3>
                    <div>
                        <label class="text-[10px] font-black  ml-1">Nom Complet</label>
                        <input type="text" name="name" required
                            class="w-full bg-secondary border-transparent rounded-xl py-3 px-4 mt-1 text-sm font-bold uppercase focus:ring-2 focus:ring-primary/20 transition-all"
                            placeholder="EX: Hello Dupont">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase ml-1">Adresse Email</label>
                        <input type="email" name="email" required
                            class="w-full bg-secondary border-transparent rounded-xl py-3 px-4 mt-1 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all"
                            placeholder="enseignant@academiapro.com">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase ml-1">Contact</label>
                        <input type="text" name="phone" required
                            class="w-full bg-secondary border-transparent rounded-xl py-3 px-4 mt-1 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all"
                            placeholder="EX: +237  6 77 88 99 00">
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-[10px] font-black uppercase text-primary border-b border-border pb-2">Profil Enseignant
                    </h3>
                    <div>
                        <label class="text-[10px] font-black uppercase ml-1">Matricule</label>
                        <input type="text" name="matricule" required
                            class="w-full bg-secondary border-transparent rounded-xl py-3 px-4 mt-1 text-sm font-bold uppercase focus:ring-2 focus:ring-primary/20 transition-all"
                            placeholder="ENS-2026-001">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase ml-1 text-primary">Département d'attache</label>
                        <select name="departement_id" required
                            class="w-full bg-secondary border-transparent rounded-xl py-3 px-4 mt-1 text-sm font-bold uppercase focus:ring-2 focus:ring-primary/20 transition-all">
                            <option value="">-- Choisir un département --</option>
                            @foreach ($departements as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nom }} ({{ $dept->code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="md:col-span-2 pt-4 flex justify-end gap-3">
                    <a href="{{ route('admin.enseignants.index') }}"
                        class="px-6 py-3 rounded-xl font-black uppercase text-[10px] bg-secondary hover:bg-border transition-all">Annuler</a>
                    <button type="submit"
                        class="px-6 py-3 rounded-xl font-black uppercase text-[10px] bg-primary text-white shadow-lg shadow-primary/20 hover:scale-105 transition-all">
                        Enregistrer l'enseignant
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
