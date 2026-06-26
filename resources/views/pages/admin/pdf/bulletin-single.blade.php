<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Bulletin - {{ $bulletins[0]['inscription']->eleve_nom ?? 'Classe' }}</title>
    <style>
        /* Configuration de la page A4 et marges minimales pour forcer la page unique */
        @page {
            margin: 15px 20px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.15;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* En-tête officiel MINESEC */
        .en-tete {
            width: 100%;
            margin-bottom: 8px;
        }

        .bloc-gauche {
            float: left;
            width: 40%;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            line-height: 1.1;
        }

        .bloc-centre {
            float: left;
            width: 20%;
            text-align: center;
        }

        .bloc-centre img {
            max-height: 40px;
            max-width: 60px;
        }

        .bloc-droite {
            float: right;
            width: 40%;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            line-height: 1.1;
        }

        .clear {
            clear: both;
        }

        /* Titre du Bulletin */
        .titre-bulletin {
            text-align: center;
            margin: 5px 0;
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 3px 0;
        }

        .titre-bulletin h2 {
            margin: 0;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .titre-bulletin p {
            margin: 1px 0 0 0;
            font-size: 11px;
            font-weight: bold;
        }

        /* ==========================================================================
           MODIFICATIONS ICI : Protection contre le débordement de page
           ========================================================================== */

        /* Tableaux de structures */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            /* Réduit de 6px à 5px */
            page-break-inside: avoid;
            /* RECOMMANDÉ : Empêche un tableau de se couper en deux */
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: middle;
        }

        /* Infos élève */
        .table-eleve td {
            font-size: 9.5px;
            padding: 2px 4px;
        }

        /* Tableau des Notes */
        .table-notes th {
            background-color: #ffffff;
            font-size: 10px;
            font-weight: bold;
        }

        .table-notes td {
            font-size: 9.5px;
        }

        .bg-groupe {
            background-color: #f0f4f8;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Bloc Statistiques & Décisions */
        .table-stats th {
            font-size: 9px;
            font-weight: bold;
        }

        .table-stats td {
            font-size: 10px;
            padding: 4px;
            /* Réduit de 5px à 4px */
            font-weight: bold;
        }

        /* Discipline */
        .table-discipline td {
            font-size: 9px;
            padding: 4px;
            text-transform: uppercase;
            font-weight: bold;
        }

        /* Signatures (Bas de page) */
        .table-signatures td {
            border: 1px solid #000;
            height: 55px;
            /* IMPORTANT : Réduit de 75px à 55px pour laisser respirer le bas de page */
            vertical-align: top;
            padding: 4px;
            font-size: 9.5px;
            font-weight: bold;
        }

        /* Gestion de la structure de page par étudiant */
        .page-bulletin {

            page-break-inside: avoid;
            /* CRITIQUE : Dit à DomPDF que TOUT le bulletin doit tenir sur une seule page */
        }

        .page-bulletin:last-child {
            page-break-after: avoid !important;
            /* Supprime la page blanche finale du document */
        }







        @page {
            margin: 10mm 15mm 10mm 15mm;
            /* Réduit un peu les marges haut/bas de la feuille */
        }

        body {
            margin: 0;
            padding: 0;
        }

        .page-bulletin {

            /* Fait un saut de page après chaque élève */
            page-break-inside: avoid;
            /* Interdit de couper un bulletin en deux */
        }

        /* LA SÉCURITÉ : Désactive le saut de page pour le tout dernier élève */
        .page-bulletin:last-child {
            page-break-after: avoid !important;
            break-after: avoid !important;
        }

        /* Empêche les tableaux de sauter une page à l'intérieur s'ils manquent de place */
        table {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    @foreach ($bulletins as $b)
        @php

            // On extrait les variables pour que ton code en dessous ne change pas
            $inscription = $b['inscription'];
            $totalElevesClasse = $b['totalElevesClasse'];
            $matieres = $b['matieres'];
            $notes = $b['notes'];
            $coefficients = $b['coefficients'];
            $bilan = $b['bilan'];
        @endphp

        <div class="page-bulletin">






            <div class="en-tete">
                <div class="bloc-gauche">
                    REPUBLIQUE DU CAMEROUN<br>
                    Paix-Travail-Patrie<br>
                    MINISTERE DES ENSEIGNEMENTS SECONDAIRES<br>
                    <span
                        style="text-transform: uppercase;">{{ $etablissement->nom ?? 'Établissement Scolaire' }}</span><br>
                    <span
                        style="font-style: italic; font-weight: normal; font-size: 7.5px;">"{{ $etablissement->slogan }}"</span><br>
                    <span style="font-weight: normal; font-size: 7.5px;"> {{ $etablissement->adresse }} —
                        {{ $etablissement->telephone }}</span>
                </div>

                <div class="bloc-centre">
                    @php
                        $vraiCheminDansPublic = 'storage/' . $etablissement->logo;
                    @endphp

                    @if ($etablissement->logo && file_exists(public_path($vraiCheminDansPublic)))
                        <img src="{{ public_path($vraiCheminDansPublic) }}"
                            style="max-height: 45px; max-width: 65px; object-fit: contain;">
                    @else
                        <div
                            style="border: 1px solid #000; width: 50px; height: 35px; margin: 0 auto; line-height: 35px; font-size: 7px; font-weight: bold;">
                            {{ $etablissement->code_ecole ?? 'LOGO' }}
                        </div>
                    @endif
                </div>

                <div class="bloc-droite">
                    REPUBLIC OF CAMEROON<br>
                    Peace-Work-Fatherland<br>
                    MINISTRY OF SECONDARY EDUCATION<br>
                    <span style="text-transform: uppercase;">{{ $etablissement->nom ?? 'School Complex' }}</span><br>
                    <span
                        style="font-style: italic; font-weight: normal; font-size: 7.5px;">"{{ $etablissement->slogan }}"</span><br>
                    <span style="font-weight: normal; font-size: 7.5px;">📩 {{ $etablissement->email }}</span>
                </div>
                <div class="clear"></div>
            </div>

            <div class="titre-bulletin">
                <h2>BULLETIN DE NOTES DU {{ $trimestre->nom }}</h2>
                <p>ANNÉE SCOLAIRE : {{ $inscription->annee_libelle }}</p>
            </div>

            <table class="table-eleve">
                <tr>
                    <td width="60%"><strong>NOM ET PRENOM :</strong> {{ $inscription->eleve_nom }}
                        {{ $inscription->eleve_prenom }}</td>
                    <td width="40%"><strong>NÉ(E) LE :</strong>
                        {{ $inscription->date_naissance ? date('d/m/Y', strtotime($inscription->date_naissance)) : 'N/A' }}
                        À
                        {{ strtoupper($inscription->lieu_naissance ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td><strong>Redoublant :</strong> {{ $inscription->inscription->est_redoublant ?? 'N/A' }}</td>
                    <td>
                        <table style="width:100%; margin:0; border:none;">
                            <tr style="border:none;">
                                <td style="border:none; padding:0;" width="33%"><strong>CLASSE :</strong>
                                    {{ $inscription->classe_nom }}</td>
                                <td style="border:none; padding:0;" width="33%"><strong>SEXE :</strong>
                                    {{ $inscription->sexe ?? 'N/A' }}</td>
                                <td style="border:none; padding:0;" width="34%"><strong>MATRICULE :</strong>
                                    {{ $inscription->matricule ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class="table-notes">
                <thead>
                    <tr>
                        <th width="28%">Matières</th>
                        @foreach ($sequences as $seq)
                            <th width="9%">{{ $seq->nom }}</th>
                        @endforeach
                        <th width="10%">Moy/20</th>
                        <th width="6%">Coeff</th>
                        <th width="10%">Total (N*C)</th>
                        <th width="13%">Appréciation</th>
                        <th width="15%">Professeur</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // 1. Initialisation des totaux globaux du bulletin
                        // 1. Initialisation des variables pour éviter l'erreur "Undefined variable"
                        $totalPointsSeq1 = 0;
                        $totalPointsSeq2 = 0;
                        $totalPointsTrimestre = 0;
                        $totalCoefficientsClasse = 0;

                        // Initialisation des totaux globaux pour toutes les matières
                        $totalPointsGlobal = 0;
                        $totalCoeffGlobal = 0;

                        // 2. Récupération sécurisée des IDs des séquences
                        $seq1 = $sequences->values()->get(0);
                        $seq2 = $sequences->values()->get(1);
                        $seq1Id = $seq1 ? $seq1->id : null;
                        $seq2Id = $seq2 ? $seq2->id : null;

                    @endphp

                    {{-- Boucle 1 : On parcourt les groupes --}}
                    @foreach ($matieres as $groupeId => $matieresDuGroupe)
                        @php
                            $sousTotalPoints = 0;
                            $sousTotalCoeffs = 0;
                        @endphp
                        {{-- Ligne de titre du groupe --}}
                        {{-- On utilise le premier élément du groupe pour récupérer le nom du groupe --}}
                        <tr style="background-color: #e5e7eb;">
                            <td colspan="8" style="font-weight: 800;">
                                {{ $matieresDuGroupe->first()->groupe_nom ?? 'AUTRES MATIÈRES' }}
                            </td>
                        </tr>

                        {{-- Boucle 2 : On parcourt les matières DANS le groupe --}}
                        @foreach ($matieresDuGroupe as $matiere)
                            @php
                                $idMat = $matiere->matiere_id;
                                $coef = $matiere->coefficient ?? 1;

                                // Récupération des notes
                                $noteSeq1 = $seq1Id && isset($notes[$idMat][$seq1Id]) ? $notes[$idMat][$seq1Id] : null;
                                $noteSeq2 = $seq2Id && isset($notes[$idMat][$seq2Id]) ? $notes[$idMat][$seq2Id] : null;

                                $valNoteSeq1 = $noteSeq1 ?? 0;
                                $valNoteSeq2 = $noteSeq2 ?? 0;

                                // Calcul moyenne matière
                                if ($noteSeq1 !== null && $noteSeq2 !== null) {
                                    $moyenneMatiere20 = ($valNoteSeq1 + $valNoteSeq2) / 2;
                                } else {
                                    $moyenneMatiere20 = $noteSeq1 ?? ($noteSeq2 ?? 0);
                                }

                                $pointsMatiere = $moyenneMatiere20 * $matiere->coefficient;

                                // Cumul pour le sous-total du groupe
                                $sousTotalPoints += $pointsMatiere;
                                $sousTotalCoeffs += $matiere->coefficient;
                            @endphp

                            <tr>
                                <td class="text-left" style="padding-left: 20px;">{{ $matiere->matiere_nom }}</td>
                                <td class="text-center">{{ $noteSeq1 !== null ? number_format($noteSeq1, 2) : '-' }}
                                </td>
                                <td class="text-center">{{ $noteSeq2 !== null ? number_format($noteSeq2, 2) : '-' }}
                                </td>
                                <td class="text-center" style="font-weight: bold; background-color: #f9f9f9;">
                                    {{ number_format($moyenneMatiere20, 2) }}
                                </td>
                                <td class="text-center">{{ $coef }}</td>
                                <td class="text-center">{{ number_format($moyenneMatiere20 * $coef, 2) }}</td>
                                <td class="text-center" style="font-size: 8px;">
                                    @if ($noteSeq1 === null && $noteSeq2 === null)
                                        -
                                    @elseif($moyenneMatiere20 >= 16)
                                        Très bien
                                    @elseif($moyenneMatiere20 >= 14)
                                        Bien
                                    @elseif($moyenneMatiere20 >= 12)
                                        Assez bien
                                    @elseif($moyenneMatiere20 >= 10)
                                        Passable
                                    @else
                                        Insuffisant
                                    @endif
                                </td>
                                <td class="text-left" style="font-size: 8px;">{{ $matiere->prof_nom ?? 'Non assigné' }}
                                </td>


                            </tr>
                        @endforeach

                        {{-- LIGNE DU SOUS-TOTAL DU GROUPE --}}
                        <tr style="background-color: #f9f9f9; font-weight: bold;">
                            {{-- On utilise colspan="5" pour sauter : Nom, Seq1, Seq2, Moy/20, Coeff --}}
                            {{-- Total coef sous groupe --}}

                            <td colspan="4" class="text-right">SOUS-TOTAL
                                {{ $matieresDuGroupe->first()->groupe_nom }}</td>
                            <td class="text-center">{{ $sousTotalCoeffs }}</td>
                            {{-- Affiche la somme des Points (Total N*C) --}}
                            <td class="text-center">{{ number_format($sousTotalPoints, 2) }}</td>

                            {{-- Le reste des colonnes vides --}}
                            <td colspan="2"></td>
                        </tr>
                        @php
                            // C'est ICI que vous ajoutez les sous-totaux au total global
                            $totalPointsGlobal += $sousTotalPoints;
                            $totalCoeffGlobal += $sousTotalCoeffs;
                        @endphp
                    @endforeach

                    {{-- Ligne Totale finale --}}
                    <tr style="font-weight: bold; background-color: #f4f4f4;">
                        <td class="text-left" style="text-transform: uppercase;">TOTAL GÉNÉRAL</td>
                        <td colspan="3"></td>
                        <td class="text-center">{{ $totalCoeffGlobal }}</td>
                        <td class="text-center">{{ number_format($totalPointsGlobal, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
            <table class="table-stats text-center">
                <thead>
                    <tr>
                        <th width="14%">MOY SEQ 1</th>
                        <th width="14%">MOY SEQ 2</th>
                        <th width="14%">MOY TRIM</th>
                        <th width="14%">Rang</th>
                        <th width="14%">Mention</th>
                        <th width="14%">Moy Classe</th>
                        <th width="16%">Décisions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($bulletins as $bulletin) --}}
                    <tr>
                        @php
                            // Formule stricte : Somme (Note * Coef) / Coefficient Total
                            $moyenneSeq1Finale =
                                $totalCoefficientsClasse > 0 ? $totalPointsSeq1 / $totalCoefficientsClasse : 0;
                            $moyenneSeq2Finale =
                                $totalCoefficientsClasse > 0 ? $totalPointsSeq2 / $totalCoefficientsClasse : 0;
                            $moyenneTrimFinale =
                                $totalCoefficientsClasse > 0 ? $totalPointsTrimestre / $totalCoefficientsClasse : 0;
                        @endphp

                        <td>{{ number_format($moyenneSeq1Finale, 2) }}</td>

                        <td>{{ number_format($moyenneSeq2Finale, 2) }}</td>

                        <td style="font-weight: bold; background-color: #f9f9f9;">
                            @php

                                $moyTrimestre = $totalPointsGlobal / $totalCoeffGlobal;
                            @endphp
                            {{ number_format($moyTrimestre, 2) }}
                            {{-- {{ number_format($stats['moyenne'], 2) }} --}}

                        </td>

                        <td style="font-size: 9px;">
                           {{ $b['rang'] }} / {{ count($bulletins) }}
                        </td>
                        <td style="font-size: 9px;">
                            @if ($moyenneTrimFinale < 10)
                                Insuffisant
                            @elseif ($moyenneTrimFinale < 12)
                                Passable
                            @elseif ($moyenneTrimFinale < 14)
                                Assez bien
                            @elseif ($moyenneTrimFinale < 16)
                                Bien
                            @elseif ($moyenneTrimFinale < 18)
                                Très bien
                            @elseif ($moyenneTrimFinale < 19)
                                Excellent
                            @else
                                Parfait
                            @endif
                        </td>

                        <td>{{ number_format($stats['moyenne'], 2) }}
                        </td>
                        {{-- {{ number_format($stats['moyenne'], 2) }} --}}
                        <td style="font-size: 8px; font-style: italic;">
                            {{ $moyenneTrimFinale >= 10 ? 'Passable, du courage !' : 'Doit redoubler d\'efforts.' }}
                        </td>
                    </tr>


                    {{-- @endforeach --}}


                </tbody>
            </table>

            <div class="stats-footer">
                <table border="1" style="width: 100%; text-align: center; margin-top: 10px;">
                    <tr>
                        <td>Moyenne Classe : {{ number_format($stats['moyenne'], 2) }}</td>
                        <td>Moyenne Max : {{ number_format($stats['max'], 2) }}</td>
                        <td>Moyenne Min : {{ number_format($stats['min'], 2) }}</td>
                        <td>Taux de réussite : {{ number_format($stats['taux_reussite'], 2) }}%</td>
                    </tr>
                </table>
            </div>





            <table class="table-discipline text-center">
                <tr>
                    <td width="20%">Retards : _____</td>
                    <td width="20%">Absences : _____</td>
                    <td width="20%"> Consignes : _____</td>
                    <td width="20%">Avert Conduite</td>
                    <td width="20%"> Exclusion</td>
                </tr>
            </table>

            <table class="table-signatures">
                <tr>
                    <td width="33%" class="text-center">Nom et visa du Prof principal</td>
                    <td width="33%" class="text-center">Visa du Parent</td>
                    <td width="34%" class="text-center">
                        Nom et visa du Principal<br><br><br><br>
                        <span style="font-weight: normal; font-size: 8px;">Fait à
                            {{ $etablissement->ville ?? 'Bafoussam' }},
                            le {{ date('d/m/Y') }}</span>
                    </td>
                </tr>
            </table>


        </div>
        @if (!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
</body>

</html>
