@extends('layouts.admin.admin-layout')

@section('content')
    <h1 class="text-2xl font-bold text-foreground mb-4">Départements Scolaire</h1>
    <p class="text-sm text-gray-500 mb-6">Gérez les différentes
        structures scolaire de votre établissement. Ajoutez, modifiez ou supprimez des départements selon les besoins.
    </p>

    <div class="">
        <a href="{{ route('admin.departements.create') }}"
            class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all mb-6">
            <i class="fas fa-plus mr-2"></i> Ajouter un Département
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($departements as $dept)
            <div
                class="bg-card rounded-2xl border border-border p-5 hover:shadow-lg hover:border-primary/30 transition-all duration-300 group relative">

                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-primary bg-primary/10 px-2 py-0.5 rounded">
                            {{ $dept->code }}
                        </span>
                        <h3 class="text-lg font-bold text-foreground mt-2 group-hover:text-primary transition-colors">
                            {{ $dept->nom }}
                        </h3>
                    </div>

                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="p-2 rounded-lg hover:bg-secondary text-gray-400 hover:text-foreground transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            class="absolute right-0 mt-2 w-48 rounded-xl bg-card border border-border shadow-xl z-50 overflow-hidden">
                            <div class="py-1">
                                <a href="{{ route('admin.departements.edit', $dept->id) }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-primary/5 hover:text-primary transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Modifier
                                </a>

                                <button type="button" onclick="confirmDelete({{ $dept->id }})"
                                    class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Supprimer
                                </button>

                                <form action="{{ route('admin.departements.destroy', $dept->id) }}" method="POST"
                                    id="delete-form-{{ $dept->id }}" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-gray-500 text-sm line-clamp-2 mb-4 h-10">
                    {{ $dept->description ?? 'Aucune description fournie.' }}
                </p>

                <div
                    class="pt-4 border-t border-border flex justify-between items-center text-[10px] text-gray-400 uppercase font-semibold">
                    <div class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Créé le : {{ \Carbon\Carbon::parse($dept->created_at)->format('d/m/Y') }}
                    </div>
                    {{-- <div class="flex items-center">
                        <div
                            class="w-5 h-5 rounded-full bg-secondary flex items-center justify-center mr-1 text-[8px] text-primary">
                            {{ substr($dept->createur_nom ?? 'A', 0, 1) }}
                        </div>
                        Par : {{ $dept->createur_nom ?? 'Admin' }}
                    </div> --}}
                </div>

            </div>
        @endforeach
    </div>
    <script>
        function confirmDelete(id) {
            if (confirm('Voulez-vous vraiment supprimer ce département ?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endsection
