<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <button onclick="window.history.back()" data-turbolinks="false" type="button" title="Retour" aria-label="Retour"
                class="text-blue-500 hover:text-blue-700 text-xs">
                &larr; Retour
            </button>
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
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
    </div>
</x-app-layout>
