@extends('layouts.admin.admin-layout')

@section('content')
    <div class="mb-6">
        <a href="{{ route('settings.classes.index') }}" class="text-[10px] font-black  text-primary hover:underline">
            <x-lucide-arrow-left class="w-4 h-4 mr-1" /> Retour aux classes
        </a>
        <h1 class="text-xl font-black  text-foreground mt-2">Configuration : {{ $classe->nom_complet }}</h1>
        <p class="text-xs text-muted-foreground  font-bold">Cochez les matières et définissez les coefficients</p>
    </div>

    <form action="{{ route('settings.classes.matieres.update', $classe) }}" method="POST" class='relative'>
        @csrf
        <div class="bg-card rounded-2xl border border-border overflow-hidden shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-secondary/50 border-b border-border">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black  text-muted-foreground w-16">Actif</th>
                        <th class="px-6 py-4 text-[10px] font-black  text-muted-foreground">Matière</th>
                        <th class="px-6 py-4 text-[10px] font-black  text-muted-foreground w-32">Coefficient</th>
                        {{-- <th class="px-6 py-4 text-[10px] font-black uppercase text-muted-foreground w-32">Ordre</th> --}}
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($allMatieres as $m)
                        @php $isAttached = in_array($m->id, $matieresAttribuees); @endphp
                        <tr class="hover:bg-secondary/10 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" name="matieres[]" value="{{ $m->id }}"
                                    {{ $isAttached ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-foreground">{{ $m->nom }}</span>
                                <span
                                    class="block text-[10px] text-muted-foreground uppercase font-black">{{ $m->code }}</span>
                            </td>


                            <td class="px-6 py-4">
                                <input type="number" step="0.25" name="coefficients[{{ $m->id }}]"
                                    value="{{ $isAttached ? $classe->matieres->find($m->id)->pivot->coefficient ?? 1 : 1 }}"
                                    class="w-20 bg-secondary border-border rounded-lg text-xs px-2 py-1 {{ !$isAttached ? 'text-muted-foreground/30' : '' }}"
                                    min='0.25'>
                            </td>

                            {{-- <td class="px-6 py-4">
                                <input type="number" name="ordre[{{ $m->id }}]"
                                    value="{{ $isAttached ? $classe->matieres->find($m->id)->pivot->coefficient ?? 1 : 1 }}"
                                    class="w-20 bg-secondary border-border rounded-lg text-xs px-2 py-1 {{ !$isAttached ? 'text-muted-foreground/30' : '' }}">
                            </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="sticky bottom-4 z-20 mt-8 flex justify-end">
            <button
             type="submit"
                class="bg-primary text-white  px-5 py-3 rounded-full  text-xs  shadow-lg shadow-primary/20   inline-flex items-center justify-center gap-2
               text-secondary
               hover:opacity-90 transition font-bold">
                <x-lucide-save class="w-4 h-4" /> Enregistrer le programme scolaire
            </button>
        </div>

        {{--  <div class="sticky bottom-4 z-20 mt-8 flex justify-center">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2
               bg-primary text-secondary
               px-5 py-2.5 rounded-md
               text-xs font-semibold
               hover:opacity-90 transition">
                    <x-lucide-save class="w-4 h-4" />
                    <span>Enregistrer les notes</span>
                </button>
            </div> --}}
    </form>
@endsection
