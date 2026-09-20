<x-app-layout>
    <x-slot name="header" class='relative'>
        <div class="flex items-center gap-3">
            {{-- <div class="absolute top-0 left-0"> --}}
                <!-- Bouton de retour -->
                <a href="{{ route('after.login.page') }}"
                    class="p-1.5 rounded-full text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                    title="Retour">
                    <x-lucide-arrow-left class="w-5 h-5 text-bold" />
                </a>

                <!-- Titre -->
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
            {{-- </div> --}}
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
