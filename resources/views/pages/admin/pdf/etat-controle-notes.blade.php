<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <title>
        État de contrôle des notes -
        {{ $classe->nom }}
    </title>

    <style>
        @page {
            size: A3 landscape;
            margin: 12mm 10mm 12mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #111827;
            margin: 0;
            padding: 0;
        }

        /* =====================================================
           EN-TÊTE OFFICIEL
        ===================================================== */

        .header {
            width: 100%;
            text-align: center;
            margin-bottom: 8px;
        }

        .republique {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .devise {
            font-size: 9px;
            font-weight: bold;
            margin-top: 2px;
        }

        .etablissement {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .adresse {
            font-size: 8px;
            margin-top: 2px;
        }

        .separator {
            border-top: 1px solid #111827;
            margin: 5px 0;
        }

        .document-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .document-subtitle {
            font-size: 9px;
            margin-top: 3px;
        }

        /* =====================================================
           INFORMATIONS CLASSE
        ===================================================== */

        .infos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        .infos td {
            padding: 3px 5px;
            border: 1px solid #9ca3af;
        }

        .infos .label {
            font-weight: bold;
            background: #f3f4f6;
            width: 11%;
        }

        .infos .value {
            width: 22%;
        }

        /* =====================================================
           TABLEAU PRINCIPAL
        ===================================================== */

        .notes-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .notes-table th,
        .notes-table td {
            border: 0.6px solid #6b7280;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
        }

        /* Première ligne */

        .notes-table thead th {
            background: #e5e7eb;
            font-weight: bold;
        }

        /* Matière */

        .matiere-header {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            height: 25px;
        }

        /* Enseignant */

        .enseignant-header {
            font-size: 6.5px;
            font-weight: normal;
            font-style: italic;
            background: #f9fafb !important;
            height: 22px;
        }

        /* Séquences */

        .sequence-header {
            font-size: 7px;
            height: 18px;
        }

        /* Numéro */

        .col-numero {
            width: 25px;
        }

        /* Élève */

        .col-eleve {
            width: 145px;
            text-align: left !important;
        }

        .eleve-name {
            text-align: left;
            font-size: 7.5px;
            font-weight: bold;
            padding-left: 5px !important;
        }

        /* Note */

        .note {
            font-size: 8px;
            height: 22px;
        }

        .note-vide {
            color: #dc2626;
            font-weight: bold;
        }

        /* =====================================================
           GROUPES DE MATIÈRES
        ===================================================== */

        .groupe-header {
            background: #d1d5db !important;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* =====================================================
           PIED DE PAGE
        ===================================================== */

        .footer {
            margin-top: 8px;
            width: 100%;
            font-size: 7px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            padding: 3px;
            vertical-align: top;
        }

        .legend {
            text-align: left;
        }

        .signature {
            text-align: right;
        }

        /* =====================================================
           IMPRESSION
        ===================================================== */

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        .page-number:after {
            content: counter(page);
        }
    </style>
</head>

<body>

    {{-- ======================================================
         EN-TÊTE OFFICIEL
    ======================================================= --}}

    <div class="header">

        <div class="republique">
            RÉPUBLIQUE DU CAMEROUN
        </div>

        <div class="devise">
            Paix – Travail – Patrie
        </div>

        <div class="separator"></div>

        <div class="etablissement">
            {{ $etablissement->nom ?? 'NOM DE L’ÉTABLISSEMENT' }}
        </div>

        @if (isset($etablissement->adresse) && $etablissement->adresse)
            <div class="adresse">
                {{ $etablissement->adresse }}
            </div>
        @endif

        <div class="document-title">
            ÉTAT RÉCAPITULATIF ET CONTRÔLE DES NOTES
        </div>

        <div class="document-subtitle">
            Vérification des notes avant impression des bulletins
        </div>

    </div>


    {{-- ======================================================
         INFORMATIONS
    ======================================================= --}}

    <table class="infos">

        <tr>
            <td class="label">
                Année scolaire
            </td>

            <td class="value">
                {{ $anneeActive->libelle }}
            </td>

            <td class="label">
                Classe
            </td>

            <td class="value">
                {{ $classe->nom }}
            </td>

            <td class="label">
                Trimestre
            </td>

            <td class="value">
                {{ $trimestre->nom }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Nombre d'élèves
            </td>

            <td class="value">
                {{ $eleves->count() }}
            </td>

            <td class="label">
                Nombre de matières
            </td>

            <td class="value">
                {{ $matieres->count() }}
            </td>

            <td class="label">
                Nbre Évaluations
            </td>

            <td class="value">
                {{ $sequences->count() }}
            </td>
        </tr>

    </table>


    {{-- ======================================================
         TABLEAU DES NOTES
    ======================================================= --}}

    <table class="notes-table">

        <thead>

            {{-- ------------------------------------------------
                 LIGNE 1 : MATIÈRES
            ------------------------------------------------- --}}

            <tr>

                <th rowspan="3" class="col-numero">
                    N°
                </th>

                <th rowspan="3" class="col-eleve">
                    ÉLÈVE
                </th>

                @php
                    $groupes = $matieres->groupBy('groupe_id');
                @endphp

                @foreach ($groupes as $groupeId => $matieresGroupe)
                    @if ($groupeId)
                        <th colspan="{{ $matieresGroupe->count() * $sequences->count() }}" class="groupe-header">
                            {{ $matieresGroupe->first()->groupe_nom }}
                        </th>
                    @else
                        <th colspan="{{ $matieresGroupe->count() * $sequences->count() }}" class="groupe-header">
                            MATIÈRES
                        </th>
                    @endif
                @endforeach

            </tr>


            {{-- ------------------------------------------------
                 LIGNE 2 : NOM MATIÈRE + ENSEIGNANT
            ------------------------------------------------- --}}

            <tr>

                @foreach ($groupes as $groupeId => $matieresGroupe)
                    @foreach ($matieresGroupe as $matiere)
                        <th colspan="{{ $sequences->count() }}" class="matiere-header">
                            {{ $matiere->matiere_nom }}
                        </th>
                    @endforeach
                @endforeach

            </tr>


            {{-- ------------------------------------------------
                 LIGNE 3 : ENSEIGNANT + SÉQUENCES
            ------------------------------------------------- --}}

            <tr>

                @foreach ($groupes as $groupeId => $matieresGroupe)
                    @foreach ($matieresGroupe as $matiere)
                        @foreach ($sequences as $sequence)
                            <th class="sequence-header">

                                {{ $sequence->nom ?? ($sequence->libelle ?? 'Séq. ' . $loop->iteration) }}

                                <br>

                                @if ($loop->first)
                                    <span style="font-size: 5.5px;">
                                        {{ $matiere->enseignant_nom ?: 'Non affecté' }}
                                    </span>
                                @endif

                            </th>
                        @endforeach
                    @endforeach
                @endforeach

            </tr>

        </thead>


        {{-- ==================================================
             CORPS DU TABLEAU
        =================================================== --}}

        <tbody>

            @forelse($eleves as $index => $eleve)

                <tr>

                    {{-- Numéro --}}

                    <td class="col-numero">
                        {{ $index + 1 }}
                    </td>


                    {{-- Élève --}}

                    <td class="eleve-name">

                        {{ strtoupper($eleve->nom) }}

                        {{ $eleve->prenom }}

                    </td>


                    {{-- Notes --}}

                    @foreach ($groupes as $groupeId => $matieresGroupe)
                        @foreach ($matieresGroupe as $matiere)
                            @foreach ($sequences as $sequence)
                                @php

                                    $note = $notes[$eleve->inscription_id][$matiere->matiere_id][$sequence->id] ?? null;

                                @endphp

                                <td class="note">

                                    @if ($note !== null)
                                        {{ rtrim(rtrim(number_format((float) $note, 2, ',', ''), '0'), ',') }}
                                    @else
                                        <span class="note-vide">
                                            —
                                        </span>
                                    @endif

                                </td>
                            @endforeach
                        @endforeach
                    @endforeach

                </tr>

            @empty

                <tr>

                    <td colspan="{{ 2 + $matieres->count() * $sequences->count() }}" style="padding: 15px;">
                        Aucun élève inscrit dans cette classe.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- ======================================================
         PIED DE PAGE
    ======================================================= --}}

    <div class="footer">

        <table class="footer-table">

            <tr>

                <td class="legend">

                    <strong>Contrôle :</strong>
                    Le présent état est destiné à la vérification des notes
                    avant l'édition définitive des bulletins.

                    <br>

                    <strong>—</strong>
                    = aucune note enregistrée pour l'évaluation concernée.

                </td>

                <td class="signature">

                    Date de contrôle : ____________________

                    <br><br>

                    Visa / Signature :
                    ______________________________

                </td>

            </tr>

        </table>

        <div style="text-align:center; margin-top:5px;">
            Page <span class="page-number"></span>
        </div>

    </div>

</body>

</html>
