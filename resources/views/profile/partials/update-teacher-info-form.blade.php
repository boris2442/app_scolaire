<section class="space-y-6">
    <header class="mb-6">
        <h2 class="text-lg font-medium text-foreground">
            {{ __('Informations Administratives et Professionnelles (Enseignant)') }}
        </h2>
        <p class="mt-1 text-sm text-foreground/70">
            {{ __("Fiche signalétique officielle requise par l'établissement.") }}
        </p>
    </header>

    <form method="post" action="{{ route('enseignant.profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Indicateur d'étape -->
        <div class="flex justify-between mb-6 border-b border-border pb-3 text-sm font-semibold text-foreground/70">
            <span id="indicator-1" class="text-primary border-b-2 border-primary pb-3 -mb-[13px]">1. Identification</span>
            <span id="indicator-2" class="text-foreground/50 pb-3">2. Informations</span>
            <span id="indicator-3" class="text-foreground/50 pb-3">3. Coordonnées</span>
        </div>

        <!-- ================= ÉTAPE 1 : IDENTIFICATION ================= -->
        <div id="form-step-1" class="space-y-4">
            <h3 class="text-md font-bold text-foreground">Identification</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="matricule" :value="__('Matricule')" />
                    <x-text-input id="matricule" name="matricule" type="text"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('matricule', $user->enseignant->matricule ?? '')" required />
                </div>
                <div>
                    <x-input-label for="grade" :value="__('Grade')" />
                    <x-text-input id="grade" name="grade" type="text"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('grade', $user->enseignant->grade ?? '')" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <x-input-label for="birth_date" :value="__('Date de naissance')" />
                    <x-text-input id="birth_date" name="birth_date" type="date"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('birth_date', $user->enseignant->birth_date ?? '')" />
                </div>
                <div>
                    <x-input-label for="birth_place" :value="__('Lieu de naissance')" />
                    <x-text-input id="birth_place" name="birth_place" type="text"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('birth_place', $user->enseignant->birth_place ?? '')" />
                </div>
                <div>
                    <x-input-label for="marital_status" :value="__('Situation Familiale')" />
                    <select id="marital_status" name="marital_status"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring rounded shadow-sm">
                        <option value="C"
                            {{ old('marital_status', $user->enseignant->marital_status ?? '') == 'Célibataire' ? 'selected' : '' }}>
                            Célibataire</option>
                        <option value="M"
                            {{ old('marital_status', $user->enseignant->marital_status ?? '') == 'Marié' ? 'selected' : '' }}>
                            Marié(e)</option>
                        <option value="D"
                            {{ old('marital_status', $user->enseignant->marital_status ?? '') == 'Divorcé' ? 'selected' : '' }}>
                            Divorcé(e)</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="number_of_children" :value="__('Nombre d\'Enfants')" />
                    <x-text-input id="number_of_children" name="number_of_children" type="number"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('number_of_children', $user->enseignant->number_of_children ?? '')" min="0" max="30" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="previous_position" :value="__('Poste Antérieur')" />
                    <x-text-input id="previous_position" name="previous_position" type="text"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('previous_position', $user->enseignant->previous_position ?? '')" />
                </div>
                <div>
                    <x-input-label for="previous_school" :value="__('Ancien Établissement')" />
                    <x-text-input id="previous_school" name="previous_school" type="text"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('previous_school', $user->enseignant->previous_school ?? '')" />
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="button" onclick="goToStep(2)"
                    class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground border border-transparent rounded font-semibold text-xs  tracking-widest hover:opacity-90 focus:outline-none transition ease-in-out duration-150">
                    Suivant &rarr;
                </button>
            </div>
        </div>

        <!-- ================= ÉTAPE 2 : INFORMATIONS ================= -->
        <div id="form-step-2" class="space-y-4 hidden">
            <h3 class="text-md font-bold text-foreground">INFORMATIONS ADMINISTRATIVES</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="appointment_document_number" :value="__('N° Document d\'affectation')" />
                    <x-text-input id="appointment_document_number" name="appointment_document_number" type="text"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old(
                            'appointment_document_number',
                            $user->enseignant->appointment_document_number ?? '',
                        )" />
                </div>
                <div>
                    <x-input-label for="appointment_date" :value="__('Date d\'affectation')" />
                    <x-text-input id="appointment_date" name="appointment_date" type="date"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('appointment_date', $user->enseignant->appointment_date ?? '')" />
                </div>
                <div>
                    <x-input-label for="service_assumption_date" :value="__('Prise / Reprise de service')" />
                    <x-text-input id="service_assumption_date" name="service_assumption_date" type="date"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('service_assumption_date', $user->enseignant->service_assumption_date ?? '')" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="quality" :value="__('Qualité')" />
                    <x-text-input id="quality" name="quality" type="text"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('quality', $user->enseignant->quality ?? '')" />
                </div>
                <div>
                    <x-input-label for="diploma" :value="__('Diplôme')" />
                    <x-text-input id="diploma" name="diploma" type="text"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('diploma', $user->enseignant->diploma ?? '')" />
                </div>
                <div>
                    <x-input-label for="matiere_id" :value="__('Matière enseignée')" />
                    <select id="matiere_id" name="matiere_id"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring rounded shadow-sm">
                        <option value="">-- Sélectionner une matière --</option>
                        @foreach (\App\Models\Matiere::all() as $mat)
                            <option value="{{ $mat->id }}"
                                {{ old('matiere_id', $user->enseignant->matiere_id ?? '') == $mat->id ? 'selected' : '' }}>
                                {{ $mat->libelle ?? $mat->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="public_service_first_date" :value="__('1ère prise de service (Fonction Publique)')" />
                    <x-text-input id="public_service_first_date" name="public_service_first_date" type="date"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('public_service_first_date', $user->enseignant->public_service_first_date ?? '')" />
                </div>
                <div>
                    <x-input-label for="school_first_date" :value="__('1ère prise de service (Établissement)')" />
                    <x-text-input id="school_first_date" name="school_first_date" type="date"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('school_first_date', $user->enseignant->school_first_date ?? '')" />
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <button type="button" onclick="goToStep(1)"
                    class="inline-flex items-center px-4 py-2 bg-secondary text-secondary-foreground border border-border rounded font-semibold text-xs  tracking-widest hover:opacity-90 focus:outline-none transition ease-in-out duration-150">
                    &larr; Précédent
                </button>
                <button type="button" onclick="goToStep(3)"
                    class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground border border-transparent rounded font-semibold text-xs  tracking-widest hover:opacity-90 focus:outline-none transition ease-in-out duration-150">
                    Suivant &rarr;
                </button>
            </div>
        </div>

        <!-- ================= ÉTAPE 3 : COORDONNÉES ================= -->
        <div id="form-step-3" class="space-y-4 hidden">
            <h3 class="text-md font-bold text-foreground">COORDONNÉES ET CONTACTS</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="interruption_reason" :value="__('Motif Interruption de service')" />
                    <x-text-input id="interruption_reason" name="interruption_reason" type="text"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('interruption_reason', $user->enseignant->interruption_reason ?? '')" />
                </div>
                <div>
                    <x-input-label for="interruption_start_date" :value="__('Date Début Interruption')" />
                    <x-text-input id="interruption_start_date" name="interruption_start_date" type="date"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('interruption_start_date', $user->enseignant->interruption_start_date ?? '')" />
                </div>
                <div>
                    <x-input-label for="interruption_end_date" :value="__('Date Fin Interruption')" />
                    <x-text-input id="interruption_end_date" name="interruption_end_date" type="date"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('interruption_end_date', $user->enseignant->interruption_end_date ?? '')" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="secondary_phone" :value="__('Contact 2')" />
                    <x-text-input id="secondary_phone" name="secondary_phone" type="text"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('secondary_phone', $user->enseignant->secondary_phone ?? '')" />
                </div>
                <div>
                    <x-input-label for="address" :value="__('Adresse')" />
                    <x-text-input id="address" name="address" type="text"
                        class="mt-1 block w-full bg-card text-card-foreground border-border focus:border-ring focus:ring-ring"
                        :value="old('address', $user->enseignant->address ?? '')" />
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <button type="button" onclick="goToStep(2)"
                    class="inline-flex items-center px-4 py-2 bg-secondary text-secondary-foreground border border-border rounded font-semibold text-xs  tracking-widest hover:opacity-90 focus:outline-none transition ease-in-out duration-150">
                    &larr; Précédent
                </button>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground border border-transparent rounded font-semibold text-xs  tracking-widest hover:opacity-90 focus:outline-none transition ease-in-out duration-150">
                    {{ __('Enregistrer tout le profil') }}
                </button>
            </div>
        </div>

    </form>
</section>

<!-- Script de navigation ultra-fiable -->
<script>
    function goToStep(step) {
        // Masquer toutes les étapes
        document.getElementById('form-step-1').classList.add('hidden');
        document.getElementById('form-step-2').classList.add('hidden');
        document.getElementById('form-step-3').classList.add('hidden');

        // Afficher l'étape demandée
        document.getElementById('form-step-' + step).classList.remove('hidden');

        // Mettre à jour le style des indicateurs en haut
        for (let i = 1; i <= 3; i++) {
            const indicator = document.getElementById('indicator-' + i);
            if (i === step) {
                indicator.className = "text-primary border-b-2 border-primary pb-3 -mb-[13px]";
            } else {
                indicator.className = "text-foreground/50 pb-3";
            }
        }
    }
</script>
