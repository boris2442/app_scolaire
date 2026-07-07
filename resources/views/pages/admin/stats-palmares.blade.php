@extends('layouts.admin.admin-layout')

@section('content')
    <div class="p-6 max-w-7xl mx-auto space-y-6">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Palmarès & Statistiques</h1>
                <p class="text-sm text-muted-foreground opacity-70">Analyse des performances par séquence</p>
            </div>

            <form method="GET" class="flex items-center gap-2">
                <select name="sequence_id" onchange="this.form.submit()"
                    class="bg-card border-border text-foreground text-sm rounded-lg px-4 py-2 focus:ring-ring">
                    <option value="">Choisir une séquence</option>
                    @foreach ($sequences as $s)
                        <option value="{{ $s->id }}" {{ request('sequence_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->nom }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if ($stats['general'])
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="bg-card p-5 rounded-2xl border border-border shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium opacity-60 uppercase">Effectif Total</span>
                        <div class="p-2 bg-primary/10 text-primary rounded-lg text-xs">
                            <x-lucide-users class="w-4 h-4" />
                        </div>
                    </div>

                    <div class="mt-3 flex flex-col">
                        <span class="text-3xl font-bold text-foreground">
                            {{ $stats['general']->effectif_total }}
                        </span>

                        <div class="flex gap-2 text-[10px] font-medium mt-1">
                            <span class="text-blue-500">{{ $stats['general']->total_garcons }} Garçons</span>
                            <span class="text-muted-foreground">/</span>
                            <span class="text-pink-500">{{ $stats['general']->total_filles }} Filles</span>
                        </div>
                    </div>
                </div>

                <div class="bg-card p-5 rounded-2xl border border-border shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium opacity-60 uppercase">Taux de Réussite</span>
                        <div class="p-2 bg-success/10 text-success rounded-lg text-xs">
                            <x-lucide-chart-line class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="mt-3">
                        @php
                            $admis = $stats['general']->total_admis ?? 0;
                            $total = max($stats['general']->effectif_total, 1); // Évite la division par zéro
                            $taux = round(($admis / $total) * 100, 1);
                        @endphp
                        <span class="text-3xl font-bold">{{ $taux }}%</span>
                        <div class="w-full bg-secondary h-1.5 mt-2 rounded-full overflow-hidden">
                            <div class="bg-success h-full" style="width: {{ $taux }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-card p-5 rounded-2xl border border-border shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium opacity-60 uppercase">Moyenne École</span>
                        <div class="p-2 bg-warning/10 text-warning rounded-lg text-xs">
                            <x-lucide-award class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="mt-3">
                        <span
                            class="text-3xl font-bold text-foreground">{{ number_format($stats['general']->moyenne_generale, 2) }}</span>
                        <p class="text-[10px] opacity-50 mt-1">Sur 20 points</p>
                    </div>
                </div>

                <div class="bg-primary text-primary-foreground p-5 rounded-2xl shadow-lg border border-primary/20">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium opacity-80 uppercase">Major Établissement</span>
                        <div class="p-2 bg-white/20 rounded-lg text-xs">
                            <x-lucide-crown class="w-4 h-4 text-yellow-300" />
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-xl font-bold block truncate">{{ $stats['majors'][0]->nom ?? 'N/A' }}</span>
                        <span class="text-2xl font-black">{{ number_format($stats['majors'][0]->valeur ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">

                <div class="bg-card p-5 rounded-2xl border border-border shadow-sm">
                    <div class="flex items-center justify-between text-xs font-medium opacity-60 uppercase">
                        <span>Inscrits</span>
                        <x-lucide-users class="w-4 h-4 text-primary" />
                    </div>
                    <div class="mt-3">
                        <span class="text-3xl font-bold">{{ $stats['general']->effectif_total }}</span>
                        <div class="flex gap-2 mt-1 text-[10px] font-medium">
                            <span class="text-[10px] opacity-50">{{ $stats['general']->total_garcons }}G /
                                {{ $stats['general']->total_filles }}F</span>
                            <span class="text-pink-500">{{ $stats['general']->total_filles }} Filles</span>
                        </div>
                    </div>
                </div>

                <div class="bg-card p-5 rounded-2xl border border-border shadow-sm">
                    <div class="flex items-center justify-between text-xs font-medium opacity-60 uppercase">
                        <span>Bilan Réussite</span>
                    <x-lucide-check-check class="w-4 h-4 text-success" />
                    </div>
                    <div class="mt-2 space-y-1">
                        <div class="flex justify-between items-end">
                            <span class="text-2xl font-bold text-success">{{ $stats['general']->total_admis }} <span
                                    class="text-xs opacity-50">Admis</span></span>
                            <span class="text-xl font-bold text-danger">{{ $stats['general']->total_echoues }} <span
                                    class="text-xs opacity-50">Échecs</span></span>
                        </div>
                        @php $taux = ($stats['general']->total_admis / max($stats['general']->effectif_total, 1)) * 100; @endphp
                        <div class="w-full bg-secondary h-1 rounded-full overflow-hidden">
                            <div class="bg-success h-full" style="width: {{ $taux }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-card p-5 rounded-2xl border border-border shadow-sm">
                    <div class="flex items-center justify-between text-xs font-medium opacity-60 uppercase">
                        <span>Réussite par Sexe</span>
           <x-lucide-home class="w-10 h-10 text-red-500" />
                    </div>

                    <div class="mt-4 space-y-3">
                        <div>
                            <div class="flex justify-between text-[11px] mb-1">
                                <span class="text-muted-foreground">Garçons admis</span>
                                <span class="font-bold text-foreground">
                                    {{ $stats['general']->garcons_admis }} / {{ $stats['general']->total_garcons }}
                                </span>
                            </div>
                            @php
                                $tauxG =
                                    $stats['general']->total_garcons > 0
                                        ? ($stats['general']->garcons_admis / $stats['general']->total_garcons) * 100
                                        : 0;
                            @endphp
                            <div class="w-full bg-secondary h-1 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full" style="width: {{ $tauxG }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-[11px] mb-1">
                                <span class="text-muted-foreground">Filles admises</span>
                                <span class="font-bold text-foreground">
                                    {{ $stats['general']->filles_admis }} / {{ $stats['general']->total_filles }}
                                </span>
                            </div>
                            @php
                                $tauxF =
                                    $stats['general']->total_filles > 0
                                        ? ($stats['general']->filles_admis / $stats['general']->total_filles) * 100
                                        : 0;
                            @endphp
                            <div class="w-full bg-secondary h-1 rounded-full overflow-hidden">
                                <div class="bg-pink-500 h-full" style="width: {{ $tauxF }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-primary text-primary-foreground p-5 rounded-2xl shadow-lg relative overflow-hidden">
                    <div class="relative z-10">
                        <span class="text-xs font-medium opacity-80 uppercase">Meilleure Moyenne</span>
                        <div class="mt-3">
                            <span class="text-xl font-bold block truncate">{{ $stats['majors'][0]->nom ?? '---' }}</span>
                            <span
                                class="text-3xl font-black">{{ number_format($stats['general']->meilleure_note, 2) }}</span>
                        </div>
                    </div>
                    <x-lucide-crown class="absolute -right-2 -bottom-2 text-6xl opacity-10 rotate-12" />
                </div>
            </div>




            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-1 bg-card rounded-2xl border border-border p-5">
                    <h3 class="text-sm font-bold mb-4 flex items-center gap-2">
                        <x-lucide-trophy class="w-4 h-4 text-warning" /> Tableau d'Excellence
                    </h3>
                    <div class="space-y-4">
                        @foreach ($stats['majors'] as $key => $major)
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-bold text-muted-foreground w-4">#{{ $key + 1 }}</span>
                                    <div>
                                        <p class="text-sm font-bold">{{ $major->nom }}</p>
                                        <p class="text-[10px] opacity-50 uppercase">{{ $major->classe_complete }}</p>
                                    </div>
                                </div>
                                <span
                                    class="text-sm font-mono font-bold text-primary">{{ number_format($major->valeur, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-2 bg-card rounded-2xl border border-border overflow-hidden">
                    <div class="p-5 border-b border-border flex justify-between items-center">
                        <h3 class="text-sm font-bold">Performance par Classe</h3>
                        <span
                            class="text-[10px] bg-secondary px-2 py-1 rounded text-secondary-foreground font-bold">RÉUSSITE</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-secondary/30 text-[10px] uppercase text-muted-foreground font-bold">
                                <tr>
                                    <th class="px-6 py-3 text-foreground">Classe</th>
                                    <th class="px-6 py-3">Moyenne</th>
                                    <th class="px-6 py-3 text-center">Taux Succès</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                {{-- @foreach ($stats['par_classe'] as $c)
                                    @php $tauxClass = round(($c->reussite / $c->total) * 100); @endphp
                                    <tr class="hover:bg-secondary/20 transition-colors">
                                        <td class="px-6 py-4 font-bold">{{ $c->nom_complet_classe }}</td>
                                        <td class="px-6 py-4 font-mono">{{ number_format($c->moyenne_classe, 2) }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-3">
                                                <div class="w-24 bg-secondary h-1.5 rounded-full overflow-hidden">
                                                    <div class="h-full {{ $tauxClass >= 50 ? 'bg-success' : 'bg-danger' }}"
                                                        style="width: {{ $tauxClass }}%"></div>
                                                </div>
                                                <span
                                                    class="text-[10px] font-bold w-8 text-right">{{ $tauxClass }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach --}}

                                @foreach ($stats['par_classe'] as $c)
                                    <tr class="hover:bg-secondary/20 transition-colors">
                                        <td class="px-6 py-4 font-bold">{{ $c->nom_complet_classe }}</td>
                                        <td class="px-6 py-4 font-mono">{{ number_format($c->moyenne_classe, 2) }}</td>

                                        {{-- CORRECTION ICI --}}
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                // On calcule le taux : (Admis / Total de la classe) * 100
                                                $taux = $c->total > 0 ? ($c->reussite / $c->total) * 100 : 0;
                                            @endphp

                                            <span
                                                class="px-2 py-1 rounded-full text-[10px] font-bold {{ $taux >= 50 ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">
                                                {{ number_format($taux, 1) }}%
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-right">
                                            {{-- Ton bouton pour voir les détails --}}
                                            <a href="{{ route('admin.statistiques.classe.detail', ['classe_id' => $c->id, 'sequence_id' => request('sequence_id')]) }}"
                                                class="p-2 hover:bg-primary/10 text-muted-foreground hover:text-primary rounded-lg transition-colors">
                                            <x-lucide-more-vertical class="w-4 h-4" />
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-card border border-dashed border-border rounded-2xl p-20 text-center">
                <div class="text-4xl mb-4">📊</div>
                <h2 class="text-lg font-bold">En attente de données</h2>
                <p class="text-sm opacity-50">Veuillez sélectionner une séquence pour générer le palmarès.</p>
            </div>
        @endif
    </div>
@endsection
