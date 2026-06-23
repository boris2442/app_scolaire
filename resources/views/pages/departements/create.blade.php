@extends('layouts.admin.admin-layout')

@section('content')
    <div class="p-6 animate-fade-in-up">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Nouveau Département</h1>
                <p class="text-sm text-gray-500 mt-1">Ajouter une nouvelle unité structurelle à l'établissement.</p>
            </div>
            <a href="{{ route('admin.departments.index') }}"
                class="flex items-center px-4 py-2 bg-secondary text-gray-700 rounded-xl border border-border hover:bg-border transition-all duration-300 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Annuler
            </a>
        </div>

        <div class="max-w-3xl mx-auto">
            <form action="{{ route('admin.departments.store') }}" method="POST"
                class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden shadow-xl shadow-primary/5">
                @csrf

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 space-y-2">
                            <label for="nom" class="text-sm font-bold text-foreground ml-1">Nom complet</label>
                            <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required
                                class="w-full px-4 py-3 bg-secondary/30 border border-border rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none text-sm"
                                placeholder="ex: Département d'Informatique">
                            @error('nom')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="code" class="text-sm font-bold text-foreground ml-1">Code (Court)</label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                class="w-full px-4 py-3 bg-secondary/30 border border-border rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none text-sm uppercase"
                                placeholder="ex: INFO">
                            @error('code')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="description" class="text-sm font-bold text-foreground ml-1">Missions du
                            département</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full px-4 py-3 bg-secondary/30 border border-border rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none text-sm resize-none"
                            placeholder="Quelles sont les responsabilités de ce département ?">{{ old('description') }}</textarea>
                    </div>

                    <div
                        class="bg-secondary/20 p-4 rounded-xl border border-dashed border-border flex items-center space-x-4">
                        <div class="p-2 bg-primary/10 rounded-lg text-primary">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-foreground">Conseil d'expert</h4>
                            <p class="text-[10px] text-gray-400">Utilisez des codes explicites (ex: MATH, ALL, SVT) pour
                                faciliter les recherches futures dans les bulletins.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-secondary/30 border-t border-border flex items-center justify-end">
                    <button type="submit"
                        class="group relative px-10 py-3 bg-primary text-primary-foreground rounded-xl font-bold shadow-lg shadow-primary/20 hover:scale-[1.03] active:scale-[0.97] transition-all overflow-hidden">
                        <span class="relative z-10 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                            Créer le Département
                        </span>
                        <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }
    </style>
@endsection
