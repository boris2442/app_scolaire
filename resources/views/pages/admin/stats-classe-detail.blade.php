@extends('layouts.admin.admin-layout')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{route('admin.statistiques.index') }}" class="btn-back">
                <x-lucide-arrow-left class="w-4 h-4" />
            </a>
            <h1 class="text-2xl font-bold text-foreground">Détails :{{ $classe->niveau->nom }} {{ $classe->nom }}</h1>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach ($appreciations as $label => $count)
                <div class="bg-card p-4 rounded-xl border border-border text-center">
                    <span class="text-[10px] uppercase opacity-60 font-bold block">{{ $label }}</span>
                    <span
                        class="text-2xl font-black {{ $count > 0 ? 'text-primary' : 'opacity-20' }}">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        <div class="bg-secondary/10 border border-white/5 p-6 rounded-2xl">
            <h2 class="text-lg font-bold text-primary mb-4">Total: ({{ $totalInscrits }} inscrits)</h2>
         </div>

        <div class="bg-card rounded-2xl border border-border overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-secondary/30 text-[10px] uppercase font-bold">
                    <tr>
                        <th class="px-6 py-3">Élève</th>
                        <th class="px-6 py-3">Moyenne</th>
                        <th class="px-6 py-3">Appréciation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($moyennes as $m)
                        <tr class="border-t border-border">
                            <td class="px-6 py-4 font-bold">{{ $m->nom }} {{ $m->prenom }}</td>
                            <td class="px-6 py-4 font-mono">{{ number_format($m->valeur, 2) }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $note = $m->valeur;
                                    $appreciation = 'Médiocre';
                                    $color = 'text-danger bg-danger/10';

                                    if ($note >= 18) {
                                        $appreciation = 'Excellent';
                                        $color = 'text-primary bg-primary/10';
                                    } elseif ($note >= 16) {
                                        $appreciation = 'Très Bien';
                                        $color = 'text-primary bg-primary/10';
                                    } elseif ($note >= 14) {
                                        $appreciation = 'Bien';
                                        $color = 'text-success bg-success/10';
                                    } elseif ($note >= 12) {
                                        $appreciation = 'Assez Bien';
                                        $color = 'text-success bg-success/10';
                                    } elseif ($note >= 10) {
                                        $appreciation = 'Passable';
                                        $color = 'text-warning bg-warning/10';
                                    }
                                @endphp

                                <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $color }}">
                                    {{ $appreciation }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
