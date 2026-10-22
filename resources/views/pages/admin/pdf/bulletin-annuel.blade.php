<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Bulletin Annuel - {{ $reportCards[0]['inscription']->eleve_nom ?? 'Classe' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm 8mm 10mm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8.5px;
            line-height: 1.15;
            color: #000;
            margin: 0;
            padding: 0;
        }


        .space-head {
            padding: 2px 0px !important;
        }

        .devise-class {
            font-family: 'Times New Roman';
            font-style: italic;
        }

        /* En-tête officiel MINESEC */
        .en-tete {
            width: 100%;
            margin-bottom: 12px;
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

        .titre-bulletin {
            text-align: center;
            margin: 4px 0 6px 0;
            padding: 3px 0;
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
        }

        .titre-bulletin h2 {
            margin: 0;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .titre-bulletin p {
            margin: 1px 0 0 0;
            font-size: 9px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            page-break-inside: avoid;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px 3px;
            vertical-align: middle;
        }

        .table-eleve td {
            padding: 4px 5px;
            font-size: 8px;
        }

        .photo-placeholder {
            width: 100%;
            height: 45px;
            border: 1px dashed #666;
            line-height: 45px;
            font-size: 7px;
            color: #666;
            text-align: center;
        }

        .photo-eleve {
            max-width: 50px;
            max-height: 50px;
            display: block;
            margin: 0 auto;
        }

        .table-notes {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            page-break-inside: avoid;
            table-layout: fixed;
            /* Force le respect strict des largeurs (%) */
        }

        .table-notes th,
        .table-notes td {
            border: 1px solid #000;
            padding: 2px 2px;
            vertical-align: middle;
            word-wrap: break-word;
            /* Évite tout débordement de texte */
            overflow: hidden;
        }






        .table-notes th {
            background-color: #f4f4f4;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
        }

        .table-notes td {
            font-size: 8px;
            text-align: center;
        }

        .text-left {
            text-align: left !important;
            padding-left: 5px !important;
        }

        .table-stats th {
            font-size: 7.5px;
            font-weight: bold;
            background-color: #f4f4f4;
        }

        .table-stats td {
            font-size: 8px;
            padding: 3px;
            text-align: center;
        }

        .table-signatures td {
            border: 1px solid #000;
            height: 65px;
            vertical-align: top;
            padding: 6px;
            font-size: 8px;
            font-weight: bold;
        }

        .page-bulletin {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    @foreach ($reportCards as $b)
        @php
            $enrollment = $b['inscription'];
            $totalStudentsInClass = $b['totalStudentsInClass'];
            $matieres = $b['matieres'];
            $notesSequences = $b['notesSequences'];
            $notesTrimestres = $b['notesTrimestres'];

            $moyenneEleve = $b['moyenneEleve'] ?? 0;
            $studentRank = $b['rang'] ?? 'N/A';

            $isAnglophone = isset($enrollment->section) && strtolower($enrollment->section) === 'anglophone';

            $photoPath = $enrollment->student_picture ?? '';
            $defaultAvatar = $enrollment->sexe === 'F' ? 'images/defaultpictureF.png' : 'images/defaultpicture.png';
            $imageSrc =
                !empty($photoPath) && file_exists(public_path($photoPath))
                    ? public_path($photoPath)
                    : (file_exists(public_path($defaultAvatar))
                        ? public_path($defaultAvatar)
                        : null);
        @endphp

        <div class="page-bulletin">

            <!-- EN-TÊTE OFFICIEL BILINGUE -->
            <!-- EN-TÊTE OFFICIEL -->
            <div class="en-tete" style="line-height: 1.45;">
                <div class="bloc-gauche" style="line-height: 1.45;">
                    <div class="space-head">REPUBLIQUE DU CAMEROUN</div>
                    <div class='devise-class space-head'>Paix-Travail-Patrie</div>
                    <div class="space-head">MINISTERE DES ENSEIGNEMENTS SECONDAIRES</div>
                    <div class="space-head">
                        <span
                            style="text-transform: uppercase; font-weight: bold;">{{ $school->nom ?? 'Établissement Scolaire' }}</span>
                    </div>
                    @if (!empty($school->slogan))
                        <div class="space-head">
                            <span
                                style="font-style: italic; font-weight: normal; font-size: 7.5px;">"{{ $school->slogan }}"</span>
                        </div>
                    @endif
                    <div class="space-head">
                        <span style="font-weight: normal; font-size: 7.5px;">{{ $school->adresse ?? '' }} —
                            {{ $school->telephone ?? '' }}</span>
                    </div>
                </div>

                <div class="bloc-centre">
                    @php
                        $vraiCheminDansPublic = $school->logo ?? '';
                    @endphp

                    @if ($school->logo && file_exists(public_path($vraiCheminDansPublic)))
                        <img src="{{ public_path($vraiCheminDansPublic) }}"
                            style="max-height: 45px; max-width: 65px; object-fit: contain;">
                    @else
                        <div
                            style="border: 1px solid #000; width: 50px; height: 35px; margin: 0 auto; line-height: 35px; font-size: 7px; font-weight: bold;">
                            {{ $school->code_ecole ?? 'LOGO' }}
                        </div>
                    @endif
                </div>

                <div class="bloc-droite" style="line-height: 1.45;">
                    <div class="space-head"> REPUBLIC OF CAMEROON</div>
                    <div class='devise-class space-head'>Peace-Work-Fatherland</div>
                    <div class="space-head">MINISTRY OF SECONDARY EDUCATION</div>
                    <div class="space-head">
                        <span
                            style="text-transform: uppercase; font-weight: bold;">{{ $school->english_name ?? 'School Complex' }}</span>
                    </div>
                    @if (!empty($school->english_slogan))
                        <div class="space-head">
                            <span
                                style="font-style: italic; font-weight: normal; font-size: 7.5px;">"{{ $school->english_slogan }}"</span>
                        </div>
                    @endif
                    <div class="space-head">
                        <span style="font-weight: normal; font-size: 7.5px;">{{ $school->email ?? '' }}</span>
                    </div>
                </div>
                <div class="clear"></div>
            </div>

            <!-- TITRE DU BULLETIN BILINGUE -->
            <div class="titre-bulletin">
                <h2>{{ $isAnglophone ? 'ANNUAL PROGRESS REPORT CARD' : 'BULLETIN RÉCAPITULATIF ANNUEL DE NOTES' }}</h2>
                <p>{{ $isAnglophone ? 'SCHOOL YEAR :' : 'ANNÉE SCOLAIRE :' }} {{ $enrollment->annee_libelle }}</p>
            </div>

            <!-- INFOS ÉLÈVE -->
            <table class="table-eleve">
                <tr>
                    <td colspan="2" style="text-transform: uppercase; width: 45%;">
                        <strong>{{ $isAnglophone ? 'NAME :' : 'NOM ET PRENOM :' }}</strong>
                        {{ $enrollment->eleve_nom }} {{ $enrollment->eleve_prenom }}
                    </td>
                    <td colspan="2" style="width: 40%;">
                        <strong>{{ $isAnglophone ? 'DATE OF BIRTH :' : 'NÉ(E) LE :' }}</strong>
                        {{ $enrollment->date_naissance ? date('d/m/Y', strtotime($enrollment->date_naissance)) : 'N/A' }}
                        {{ $isAnglophone ? 'AT' : 'À' }} {{ strtoupper($enrollment->lieu_naissance ?? 'N/A') }}
                    </td>
                    <td rowspan="2" style="width: 55px; text-align: center; vertical-align: middle; padding: 2px;">
                        @if (!empty($imageSrc))
                            <img src="{{ $imageSrc }}" class="photo-eleve" alt="Photo Élève">
                        @else
                            <div class="photo-placeholder">PHOTO</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 18%;">
                        <strong>{{ $isAnglophone ? 'REPEATER :' : 'REDOUBLANT :' }}</strong>
                        {{ $isAnglophone ? ($enrollment->est_redoublant ? 'Yes' : 'No') : ($enrollment->est_redoublant ? 'Oui' : 'Non') }}
                    </td>
                    <td style="width: 27%;">
                        <strong>{{ $isAnglophone ? 'STUDENT ID :' : 'MATRICULE :' }}</strong>
                        {{ $enrollment->matricule ?? 'N/A' }}
                    </td>
                    <td style="width: 25%;">
                        <strong>{{ $isAnglophone ? 'CLASS :' : 'CLASSE :' }}</strong>
                        {{ $enrollment->classe_nom }}
                        @if (!empty($enrollment->section))
                            <em
                                style="font-style: italic; font-size: 0.85em;">({{ ucfirst($enrollment->section) }})</em>
                        @endif
                    </td>
                    <td style="width: 15%;">
                        <strong>{{ $isAnglophone ? 'SEX :' : 'SEXE :' }}</strong>
                        {{ $enrollment->sexe ?? 'N/A' }}
                    </td>
                </tr>
            </table>

            <!-- TABLEAU DES NOTES (6 SÉQUENCES DIRECTES + MOYENNE ANNUELLE) -->
            <!-- TABLEAU DES NOTES (RÉPARTITION OPTIMISÉE) -->
            <table class="table-notes">
                <thead>
                    <tr>
                        <th style="width: 16%;">{{ $isAnglophone ? 'Subjects' : 'Matières' }}</th>
                        @foreach ($trimestres as $t)
                            @php $seqs = $sequencesParTrimestre[$t->id] ?? collect(); @endphp
                            @foreach ($seqs as $s)
                                <th style="width: 5.5%;">{{ $s->nom }}</th>
                            @endforeach
                        @endforeach
                        <th style="width: 7%;">{{ $isAnglophone ? 'Ann. Avg' : 'Moy. Ann.' }}</th>
                        <th style="width: 4%;">{{ $isAnglophone ? 'Coeff' : 'Coeff' }}</th>
                        <th style="width: 8%;">{{ $isAnglophone ? 'Total (A*C)' : 'Total (M*C)' }}</th>
                        <th style="width: 14%;">{{ $isAnglophone ? 'Skills' : 'Compétences' }}</th>
                        <th style="width: 18%;">{{ $isAnglophone ? 'Teacher & Visa' : 'Professeur & Visa' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPointsGlobal = 0;
                        $totalCoeffGlobal = 0;
                    @endphp

                    @foreach ($matieres as $groupeId => $matieresDuGroupe)
                        @php
                            $sousTotalPoints = 0;
                            $sousTotalCoeffs = 0;
                        @endphp

                        <tr style="background-color: #e5e7eb;">
                            <td colspan="13" style="text-align: left; font-weight: bold; padding-left: 5px;">
                                {{ $matieresDuGroupe->first()->groupe_nom ?? ($isAnglophone ? 'MISCELLANEOUS SUBJECTS' : 'MATIÈRES DIVERSES') }}
                            </td>
                        </tr>

                        @foreach ($matieresDuGroupe as $matiere)
                            @php
                                $idMat = $matiere->matiere_id;
                                $coef = $matiere->coefficient ?? 1;

                                $sommeNotesSeq = 0;
                                $nbNotesValides = 0;

                                foreach ($trimestres as $t) {
                                    $seqs = $sequencesParTrimestre[$t->id] ?? collect();
                                    foreach ($seqs as $s) {
                                        if (
                                            isset($notesSequences[$idMat][$s->id]) &&
                                            $notesSequences[$idMat][$s->id] !== null
                                        ) {
                                            $sommeNotesSeq += $notesSequences[$idMat][$s->id];
                                            $nbNotesValides++;
                                        }
                                    }
                                }

                                $moyenneAnnuelleMatiere = $nbNotesValides > 0 ? $sommeNotesSeq / $nbNotesValides : 0;
                                $pointsMatiere = $moyenneAnnuelleMatiere * $coef;

                                $sousTotalPoints += $pointsMatiere;
                                $sousTotalCoeffs += $coef;
                            @endphp

                            <tr>
                                <td class="text-left" style="font-size: 7.5px;">{{ $matiere->matiere_nom }}</td>

                                @foreach ($trimestres as $t)
                                    @php $seqs = $sequencesParTrimestre[$t->id] ?? collect(); @endphp
                                    @foreach ($seqs as $s)
                                        <td>{{ isset($notesSequences[$idMat][$s->id]) ? number_format($notesSequences[$idMat][$s->id], 2) : '-' }}
                                        </td>
                                    @endforeach
                                @endforeach

                                <td style="font-weight: bold; background-color: #f8fafc;">
                                    {{ number_format($moyenneAnnuelleMatiere, 2) }}
                                </td>
                                <td>{{ $coef }}</td>
                                <td>{{ number_format($pointsMatiere, 2) }}</td>
                                <td style="font-size: 7px; font-style: italic;">
                                    @if ($nbNotesValides === 0)
                                        -
                                    @elseif ($moyenneAnnuelleMatiere >= 14)
                                        {{ $isAnglophone ? 'Acquired' : 'Acquis' }}
                                    @elseif ($moyenneAnnuelleMatiere >= 10)
                                        {{ $isAnglophone ? 'Being Acquired' : 'En cours' }}
                                    @else
                                        {{ $isAnglophone ? 'Not Acquired' : 'Non acquis' }}
                                    @endif
                                </td>
                                <td style="text-align: left; padding-left: 3px; font-size: 7px;">
                                    M {{ $matiere->prof_nom ?? ($isAnglophone ? 'Unassigned' : 'N/A') }}
                                </td>
                            </tr>
                        @endforeach

                        <!-- SOUS-TOTAL (ALIGNE SUR COEFF ET TOTAL) -->
                        <tr style="background-color: #f9f9f9; font-weight: bold;">
                            <td colspan="8" style="text-align: right; padding-right: 5px; font-size: 7.5px;">
                                {{ $isAnglophone ? 'SUBTOTAL ' : 'SOUS-TOTAL ' }}{{ strtoupper($matieresDuGroupe->first()->groupe_nom ?? '') }}
                            </td>
                            <td>{{ $sousTotalCoeffs }}</td>
                            <td>{{ number_format($sousTotalPoints, 2) }}</td>
                            <td colspan="3"></td>
                        </tr>

                        @php
                            $totalPointsGlobal += $sousTotalPoints;
                            $totalCoeffGlobal += $sousTotalCoeffs;
                        @endphp
                    @endforeach

                    <!-- TOTAL GÉNÉRAL -->
                    <tr style="font-weight: bold; background-color: #eaeaea;">
                        <td colspan="8"
                            style="text-align: left; text-transform: uppercase; font-size: 7.5px; padding-left: 5px;">
                            {{ $isAnglophone ? 'ANNUAL GRAND TOTAL' : 'TOTAL GÉNÉRAL ANNUEL' }}
                        </td>
                        <td>{{ $totalCoeffGlobal }}</td>
                        <td>{{ number_format($totalPointsGlobal, 2) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>



            <!-- TABLEAU RÉCAPITULATIF DISCIPLINE & ASSIDUITÉ ANNUELLE -->
            <!-- TABLEAU RÉCAPITULATIF DISCIPLINE & ASSIDUITÉ ANNUELLE -->
          <!-- TABLEAU RÉCAPITULATIF DISCIPLINE & ASSIDUITÉ ANNUELLE -->



            @php
                if ($moyenneEleve === null || $moyenneEleve == 0) {
                    $moyenneEleve = $totalCoeffGlobal > 0 ? $totalPointsGlobal / $totalCoeffGlobal : 0;
                }
            @endphp

            <!-- RÉCAPITULATIF DES MOYENNES TRIMESTRIELLES ET ANNUELLES EN BAS -->
            <table class="table-stats">
                <thead>
                    <tr>
                        <th width="16%">{{ $isAnglophone ? 'TERM 1 AVG' : 'MOYENNE TRIM 1' }}</th>
                        <th width="16%">{{ $isAnglophone ? 'TERM 2 AVG' : 'MOYENNE TRIM 2' }}</th>
                        <th width="16%">{{ $isAnglophone ? 'TERM 3 AVG' : 'MOYENNE TRIM 3' }}</th>
                        <th width="18%">{{ $isAnglophone ? 'ANNUAL AVERAGE' : 'MOYENNE GÉNÉRALE ANNUELLE' }}</th>
                        <th width="15%">{{ $isAnglophone ? 'ANNUAL RANK' : 'RANG ANNUEL' }}</th>
                        <th width="19%">{{ $isAnglophone ? 'ANNUAL GRADE' : 'MENTION ANNUELLE' }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach ($trimestres as $t)
                            <td style="font-weight: bold;">
                                {{ isset($notesTrimestres[$t->id]) && $notesTrimestres[$t->id] !== null ? number_format($notesTrimestres[$t->id], 2, ',', ' ') . ' / 20' : '-' }}
                            </td>
                        @endforeach
                        <td style="font-weight: bold; font-size: 10px; background-color: #f8fafc;">
                            {{ number_format($moyenneEleve, 2, ',', ' ') }} / 20
                        </td>
                        <td style="font-weight: bold; font-size: 10px;">
                            {{ $studentRank }}{{ $studentRank == 1 ? 'st' : 'th' }} / {{ $totalStudentsInClass }}
                        </td>
                        <td style="font-weight: bold;">
                            @if ($moyenneEleve < 10)
                                {{ $isAnglophone ? 'Fail' : 'Insuffisant' }}
                            @elseif ($moyenneEleve < 12)
                                {{ $isAnglophone ? 'Fair' : 'Passable' }}
                            @elseif ($moyenneEleve < 14)
                                {{ $isAnglophone ? 'Fairly Good' : 'Assez Bien' }}
                            @elseif ($moyenneEleve < 16)
                                {{ $isAnglophone ? 'Good' : 'Bien' }}
                            @elseif ($moyenneEleve < 18)
                                {{ $isAnglophone ? 'Very Good' : 'Très Bien' }}
                            @else
                                {{ $isAnglophone ? 'Excellent' : 'Excellent' }}
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>


<table class="table-stats" style="margin-bottom: 4px;">
    <thead>
        <tr>
            <th width="16%">ABSENCES (HRS)</th>
            <th width="16%">
                {{ $isAnglophone ? 'LATE (TIMES)' : 'RETARDS' }}
            </th>
            <th width="17%">
                {{ $isAnglophone ? 'WARNINGS' : 'AVERTISSEMENTS' }}
            </th>
            <th width="17%">
                {{ $isAnglophone ? 'BLAMES' : 'BLÂMES' }}
            </th>
            <th width="17%">SUSPENSIONS</th>
            <th width="17%">EXCLUSIONS</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td style="font-weight: bold;">
                {{ $b['disciplineStats']->total_absences ?? 0 }} h
            </td>

            <td>
                {{ $b['disciplineStats']->total_retards ?? 0 }}
            </td>

            <td>
                {{ $b['disciplineStats']->total_avertissements ?? 0 }}
            </td>

            <td>
                {{ $b['disciplineStats']->total_blames ?? 0 }}
            </td>

            <td>
                {{ $b['disciplineStats']->total_suspensions ?? 0 }}
            </td>

            <td>
                {{ $b['disciplineStats']->total_exclusions ?? 0 }}
            </td>
        </tr>
    </tbody>
</table>






            <!-- DÉCISION DU CONSEIL DE CLASSE (LAISSÉ VIDE POUR DÉCISION OFFICIELLE) -->
            <table class="table-stats" style="margin-top: 4px;">
                <tr>
                    <td style="text-align: left; padding: 6px; font-weight: bold;">
                        {{ $isAnglophone ? 'CLASS COUNCIL DECISION :' : 'DÉCISION DU CONSEIL DE CLASSE :' }}
                        <span style="font-weight: normal; font-style: italic; margin-left: 10px;">
                            ...........................................................................................................................................................................................................................................................
                        </span>
                    </td>
                </tr>
            </table>

            <!-- SIGNATURES BILINGUES -->
            <table class="table-signatures" style="margin-top: 4px;">
                <tr>
                    <td width="33%" class="text-center">
                        <strong>{{ $isAnglophone ? 'The Class Teacher' : 'Le Professeur Principal' }}</strong><br><br>
                        M {{ $b['professeurPrincipal']->name ?? ($isAnglophone ? 'Not Assigned' : 'Non désigné') }}
                    </td>
                    <td width="33%" class="text-center">
                        <strong>{{ $isAnglophone ? 'Parent / Guardian Signature' : 'Visa du Parent / Tuteur' }}</strong>
                    </td>
                    <td width="34%" class="text-center">
                        <strong>{{ $isAnglophone ? 'The Head of School' : "Le Chef d'Établissement" }}</strong><br><br>
                        <span style="font-weight: normal; font-size: 7px;">
                            {{ $isAnglophone ? 'Done at' : 'Fait à' }} ..................,
                            {{ $isAnglophone ? 'on' : 'le' }} ......../......../2026
                        </span>
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
