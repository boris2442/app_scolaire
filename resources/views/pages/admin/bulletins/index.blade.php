@extends('layouts.admin.admin-layout')

@section('content')
    <div class="container mx-auto px-4 py-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4 border-b border-border pb-5">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Impression des Bulletins</h1>
                <p class="text-sm text-foreground/60">Sélectionnez une classe pour gérer et imprimer les livrets de notes.
                </p>
            </div>

            <form action="{{ route('admin.bulletins.index') }}" method="GET" class="w-full md:w-64">
                <label class="block text-xs font-medium text-foreground/60 uppercase mb-1">Période d'impression</label>
                <select name="trimestre_id" onchange="this.form.submit()"
                    class="w-full rounded-lg border border-border bg-card p-2.5 text-sm text-foreground focus:ring-2 focus:ring-primary focus:outline-none">
                    @foreach ($trimestres as $t)
                        <option value="{{ $t->id }}" {{ $trimestreId == $t->id ? 'selected' : '' }}>
                            {{ $t->nom }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($classes as $classe)
                <div
                    class="bg-card text-card-foreground rounded-xl border border-border p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between h-44">
                    <div>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-primary/10 text-primary mb-3">
                            {{ $classe->total_eleves }} élèves inscrit(s)
                        </span>
                        <h3 class="text-lg font-bold text-foreground truncate">
                         {{ $classe->classe_nom }}
                        </h3>
                    </div>

                    <a href="{{ route('admin.bulletins.classe', ['classe_id' => $classe->id, 'trimestre_id' => $trimestreId]) }}"
                        class="w-full text-center inline-flex items-center justify-center px-4 py-2.5 bg-secondary text-secondary-foreground hover:bg-secondary/80 text-sm font-medium rounded-lg transition">
                        <span>Ouvrir </span>
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            @endforeach
        </div>

    </div>
@endsection
