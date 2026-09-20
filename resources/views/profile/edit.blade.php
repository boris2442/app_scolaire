<x-app-layout>
    <x-slot name="header" >
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex  items-center">
            {{-- <button onclick="window.history.back()" data-turbolinks="false" type="button" title="Retour" aria-label="Retour"
                class="text-blue-500 hover:text-blue-700 text-xs">
                &larr; Retour
            </button> --}}
             <a href="{{ route('after.login.page') }}"
                    class="p-1.5 rounded-full text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer "
                    title="Retour">
                    <x-lucide-arrow-left class="w-5 h-5 text-bold" />
                </a>

            {{ __('Profile') }}
        </h2>
    </x-slot>
<br/><br/><br/>
    {{-- <div class="py-12"> --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>


                <!-- 2. Fiche Administrative Enseignant (S'affiche uniquement si l'user a un profil enseignant) -->
                {{-- @if (auth()->user()->enseignant) --}}
                <div class="p-4 sm:p-8 bg-card shadow sm:rounded-lg border border-border">
                    <div class="max-w-3xl">
                        @include('profile.partials.update-teacher-info-form')
                    </div>
                </div>
                {{-- @endif --}}
            </div>



            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    {{-- </div> --}}
</x-app-layout>
