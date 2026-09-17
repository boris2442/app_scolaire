<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Bulletin - {{ $bulletins[0]['inscription']->eleve_nom ?? 'Classe' }}</title>
    <style>
        /* Configuration de la page A4 et marges minimales */
        @page {
            margin: 10mm 12mm 10mm 12mm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5px;
            line-height: 1.15;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* td {
            padding: 6px 0 !important;
        } */

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

        /* Titre du Bulletin */
        .titre-bulletin {
            text-align: center;
            margin: 8px 0 10px 0;
            /* au lieu de 4px 0 6px 0 */
            padding: 4px 0;
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;

        }

        .titre-bulletin h2 {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .titre-bulletin p {
            margin: 1px 0 0 0;
            font-size: 10px;
            font-weight: bold;
        }

        /* Tableaux généraux */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            /* au lieu de 5px */
            page-break-inside: avoid;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px 4px;
            vertical-align: middle;
        }

        /* Infos élève */
        .table-eleve td {
            font-size: 9px;
            padding: 2px 4px;
        }

        /* Tableau des Notes */
        .table-notes th {
            background-color: #f4f4f4;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }

        .table-notes td {
            font-size: 9px;
            padding: 6px 4px;
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

        /* Bloc Discipline & Stats */
        .table-stats th {
            font-size: 8.5px;
            font-weight: bold;
            background-color: #f4f4f4;
        }

        .table-stats td {
            font-size: 9px;
            padding: 3px;
        }

        .table-discipline td {
            font-size: 8.5px;
            padding: 3px;
            text-transform: uppercase;
        }

        /* Signatures (Bas de page) */
        .table-signatures td {
            border: 1px solid #000;
            height: 90px;
            /* Espace suffisant pour les cachets officiels */
            vertical-align: top;
            padding: 15px;
            font-size: 9px;
            font-weight: bold;
        }

        .page-bulletin {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    @foreach ($bulletins as $b)
        @php
            $inscription = $b['inscription'];
            $totalElevesClasse = $b['totalElevesClasse'];
            $matieres = $b['matieres'];
            $notes = $b['notes'];
            $coefficients = $b['coefficients'];
            $suivi = $b['suivi'];

            // Variables calculées dynamiquement
            $moyenneEleve = $b['moyenneEleve'] ?? 0;
            $rangEleve = $b['rang'] ?? 'N/A';
        @endphp




        <div class="page-bulletin">

            <!-- EN-TÊTE OFFICIEL -->
            <!-- EN-TÊTE OFFICIEL -->
            <div class="en-tete" style="line-height: 1.45;">
                <div class="bloc-gauche" style="line-height: 1.45;">
                    <div>REPUBLIQUE DU CAMEROUN</div>
                    <div>Paix-Travail-Patrie</div>
                    <div>MINISTERE DES ENSEIGNEMENTS SECONDAIRES</div>
                    <div>
                        <span
                            style="text-transform: uppercase; font-weight: bold;">{{ $etablissement->nom ?? 'Établissement Scolaire' }}</span>
                    </div>
                    @if (!empty($etablissement->slogan))
                        <div>
                            <span
                                style="font-style: italic; font-weight: normal; font-size: 7.5px;">"{{ $etablissement->slogan }}"</span>
                        </div>
                    @endif
                    <div>
                        <span style="font-weight: normal; font-size: 7.5px;">{{ $etablissement->adresse ?? '' }} —
                            {{ $etablissement->telephone ?? '' }}</span>
                    </div>
                </div>

                <div class="bloc-centre">
                    @php
                        $vraiCheminDansPublic = 'storage/' . ($etablissement->logo ?? '');
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

                <div class="bloc-droite" style="line-height: 1.45;">
                    <div>REPUBLIC OF CAMEROON</div>
                    <div>Peace-Work-Fatherland</div>
                    <div>MINISTRY OF SECONDARY EDUCATION</div>
                    <div>
                        <span
                            style="text-transform: uppercase; font-weight: bold;">{{ $etablissement->english_name ?? 'School Complex' }}</span>
                    </div>
                    @if (!empty($etablissement->english_slogan))
                        <div>
                            <span
                                style="font-style: italic; font-weight: normal; font-size: 7.5px;">"{{ $etablissement->english_slogan }}"</span>
                        </div>
                    @endif
                    <div>
                        <span style="font-weight: normal; font-size: 7.5px;">{{ $etablissement->email ?? '' }}</span>
                    </div>
                </div>
                <div class="clear"></div>
            </div>



            <!-- TITRE DU BULLETIN -->
            <div class="titre-bulletin">
                <h2>BULLETIN DE NOTES DU {{ $trimestre->nom }}</h2>
                <p>ANNÉE SCOLAIRE : {{ $inscription->annee_libelle }}</p>
            </div>

            <!-- INFOS ÉLÈVE -->
            <table class="table-eleve">
                <tr>
                    <td width="60%" style="text-transform:uppercase">
                        <strong>NOM ET PRENOM :</strong> {{ $inscription->eleve_nom }}
                        {{ $inscription->eleve_prenom }}
                    </td>
                    <td width="40%">
                        <strong>NÉ(E) LE :</strong>
                        {{ $inscription->date_naissance ? date('d/m/Y', strtotime($inscription->date_naissance)) : 'N/A' }}
                        À {{ strtoupper($inscription->lieu_naissance ?? 'N/A') }}
                    </td>
                </tr>
                <tr>
                    <td width="60%">
                        <table style="width:100%; margin:0; border:none;">
                            <tr style="border:none;">
                                <td style="border:none; padding:0;" width="50%">
                                    <strong>REDOUBLANT :</strong> {{ $inscription->est_redoublant ? 'Oui' : 'Non' }}
                                </td>
                                <td style="border:none; padding:0;" width="50%">
                                    <strong>MATRICULE :</strong> {{ $inscription->matricule ?? 'N/A' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="40%">
                        <table style="width:100%; margin:0; border:none;">
                            <tr style="border:none;">
                                <td style="border:none; padding:0;" width="60%">
                                    <strong>CLASSE :</strong> {{ $inscription->classe_nom }}
                                    @if (!empty($inscription->section))
                                        <em style="font-style: italic; font-size: 0.9em; opacity: 0.85;">
                                            ({{ ucfirst($inscription->section) }})
                                        </em>
                                    @endif
                                </td>
                                <td style="border:none; padding:0;" width="40%">
                                    <strong>SEXE :</strong> {{ $inscription->sexe ?? 'N/A' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- TABLEAU DES NOTES -->
            <table class="table-notes">
                <thead>
                    <tr>
                        <th width="28%">Matières</th>
                        @foreach ($sequences as $seq)
                            <th width="8%">{{ $seq->nom }}</th>
                        @endforeach
                        <th width="9%">Moy/20</th>
                        <th width="6%">Coeff</th>
                        <th width="10%">Total (N*C)</th>
                        <th width="14%">Compétences</th>
                        <th width="17%">Professeur & Visa</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPointsGlobal = 0;
                        $totalCoeffGlobal = 0;

                        $seq1 = $sequences->values()->get(0);
                        $seq2 = $sequences->values()->get(1);
                        $seq1Id = $seq1 ? $seq1->id : null;
                        $seq2Id = $seq2 ? $seq2->id : null;
                    @endphp

                    @foreach ($matieres as $groupeId => $matieresDuGroupe)
                        @php
                            $sousTotalPoints = 0;
                            $sousTotalCoeffs = 0;
                        @endphp

                        <tr style="background-color: #e5e7eb;">
                            <td colspan="8" style="font-weight: bold; font-size: 9px;">
                                {{ $matieresDuGroupe->first()->groupe_nom ?? 'MATIÈRES DIVERSE' }}
                            </td>
                        </tr>

                        @foreach ($matieresDuGroupe as $matiere)
                            @php
                                $idMat = $matiere->matiere_id;
                                $coef = $matiere->coefficient ?? 1;

                                $noteSeq1 = $seq1Id && isset($notes[$idMat][$seq1Id]) ? $notes[$idMat][$seq1Id] : null;
                                $noteSeq2 = $seq2Id && isset($notes[$idMat][$seq2Id]) ? $notes[$idMat][$seq2Id] : null;

                                if ($noteSeq1 !== null && $noteSeq2 !== null) {
                                    $moyenneMatiere20 = ($noteSeq1 + $noteSeq2) / 2;
                                } else {
                                    $moyenneMatiere20 = $noteSeq1 ?? ($noteSeq2 ?? 0);
                                }

                                $pointsMatiere = $moyenneMatiere20 * $coef;

                                $sousTotalPoints += $pointsMatiere;
                                $sousTotalCoeffs += $coef;
                            @endphp

                            <tr>
                                <td class="text-left" style="padding-left: 8px;">{{ $matiere->matiere_nom }}</td>
                                <td class="text-center">{{ $noteSeq1 !== null ? number_format($noteSeq1, 2) : '-' }}
                                </td>
                                <td class="text-center">{{ $noteSeq2 !== null ? number_format($noteSeq2, 2) : '-' }}
                                </td>
                                <td class="text-center" style="font-weight: bold; background-color: #fafafa;">
                                    {{ number_format($moyenneMatiere20, 2) }}
                                </td>
                                <td class="text-center">{{ $coef }}</td>
                                <td class="text-center">{{ number_format($pointsMatiere, 2) }}</td>
                                <td class="text-center" style="font-size: 7.5px; font-style: italic;">
                                    @if ($noteSeq1 === null && $noteSeq2 === null)
                                        -
                                    @elseif($moyenneMatiere20 >= 14)
                                        Acquis
                                    @elseif($moyenneMatiere20 >= 10)
                                        En cours d’acquisition
                                    @else
                                        Non acquis
                                    @endif
                                </td>
                                <td class="text-left" style="font-size: 7.5px;">
                                    M / Mme {{ $matiere->prof_nom ?? 'Non assigné' }}
                                </td>
                            </tr>
                        @endforeach

                        <!-- SOUS-TOTAL DU GROUPE -->
                        <tr style="background-color: #f9f9f9; font-weight: bold;">
                            <td colspan="4" class="text-right" style="font-size: 8.5px;">
                                SOUS-TOTAL {{ strtoupper($matieresDuGroupe->first()->groupe_nom) }}
                            </td>
                            <td class="text-center">{{ $sousTotalCoeffs }}</td>
                            <td class="text-center">{{ number_format($sousTotalPoints, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>

                        @php
                            $totalPointsGlobal += $sousTotalPoints;
                            $totalCoeffGlobal += $sousTotalCoeffs;
                        @endphp
                    @endforeach

                    <!-- TOTAL GÉNÉRAL -->
                    <tr style="font-weight: bold; background-color: #eaeaea;">
                        <td class="text-left" style="text-transform: uppercase;">TOTAL GÉNÉRAL</td>
                        <td colspan="3"></td>
                        <td class="text-center">{{ $totalCoeffGlobal }}</td>
                        <td class="text-center">{{ number_format($totalPointsGlobal, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>


            @php
                // Calcul de secours si $bilan->moyenne n'est pas encore en BDD
                if ($moyenneEleve === null || $moyenneEleve == 0) {
                    $moyenneEleve = $totalCoeffGlobal > 0 ? $totalPointsGlobal / $totalCoeffGlobal : 0;
                }
            @endphp

            <!-- RECAPITULATIF ELEVE & STATISTIQUES DE CLASSE -->
            <table class="table-stats text-center">
                <thead>
                    <tr>
                        <th width="15%">MOY TRIM</th>
                        <th width="15%">RANG</th>
                        <th width="20%">MENTION</th>
                        <th width="12.5%">MOY CLASSE</th>
                        <th width="12.5%">MOY MAX</th>
                        <th width="12.5%">MOY MIN</th>
                        <th width="12.5%">RÉUSSITE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        {{-- <td style="font-weight: bold; font-size: 11px; background-color: #f4f4f4;">
                            {{ number_format($moyenneEleve, 2) }} / 20
                        </td> --}}

                        <td style="font-weight: bold; font-size: 10px;">
                            {{ number_format($moyenneEleve, 2, ',', ' ') }} / 20
                        </td>


                        {{-- <td style="font-weight: bold; font-size: 10px;">
                            @if ($rangEleve !== 'N/A' && is_numeric($rangEleve))
                                {{ $rangEleve }}{{ $rangEleve == 1 ? 'er' : 'ème' }} / {{ $totalElevesClasse }}
                            @else
                                N/A
                            @endif
                        </td> --}}

                        <td style="font-weight: bold; font-size: 10px;">
                            {{ $rangEleve }}{{ $rangEleve == 1 ? 'er' : 'ème' }} / {{ $totalElevesClasse }}
                        </td>
                        <td style="font-weight: bold;">
                            @if ($moyenneEleve < 10)
                                Insuffisant
                            @elseif ($moyenneEleve < 12)
                                Passable
                            @elseif ($moyenneEleve < 14)
                                Assez Bien
                            @elseif ($moyenneEleve < 16)
                                Bien
                            @elseif ($moyenneEleve < 18)
                                Très Bien
                            @else
                                Excellent
                            @endif
                        </td>
                        <td>{{ number_format($stats['moyenne'] ?? 0, 2) }}</td>
                        <td>{{ number_format($stats['max'] ?? 0, 2) }}</td>
                        <td>{{ number_format($stats['min'] ?? 0, 2) }}</td>
                        <td>{{ number_format($stats['taux_reussite'] ?? 0, 2) }}%</td>
                    </tr>
                </tbody>
            </table>

            <!-- DISCIPLINE & TABLEAU D'HONNEUR -->
            <table style="width: 100%; border: none; margin-top: 4px;">
                <tr>
                    <td width="70%" style="border: none; padding: 0 5px 0 0;">
                        <table class="table-discipline text-center">
                            <tr style="background-color: #f4f4f4; font-weight: bold; font-size: 8px;">
                                <td width="16.6%">Retards (h)</td>
                                <td width="16.6%">Absences (h)</td>
                                <td width="16.6%">Suspensions</td>
                                <td width="16.6%">Avertiss.</td>
                                <td width="16.6%">Blâmes</td>
                                <td width="16.6%">Exclusions</td>
                            </tr>
                            <tr>
                                <td>{{ $suivi->retards ?? 0 }}</td>
                                <td>{{ $suivi->absences ?? 0 }}</td>
                                <td>{{ $suivi->suspensions ?? 0 }}</td>
                                <td>{{ $suivi->avertissements ?? 0 }}</td>
                                <td>{{ $suivi->blames ?? 0 }}</td>
                                <td>{{ $suivi->exclusions ?? 0 }}</td>
                            </tr>
                        </table>
                    </td>
                    <td width="30%" style="border: none; padding: 0;">
                        <table class="table-stats text-center">
                            <thead>
                                <tr>
                                    <th colspan="2">TABLEAU D'HONNEUR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td width="50%">
                                        <strong>OUI :</strong> {{ $moyenneEleve >= 12 ? '[ X ]' : '[   ]' }}
                                    </td>
                                    <td width="50%">
                                        <strong>NON :</strong> {{ $moyenneEleve < 12 ? '[ X ]' : '[   ]' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- SIGNATURES -->
            <table class="table-signatures" style="margin-top: 6px;">
                <tr>
                    <td width="33%" class="text-center">Nom et Visa du Titulaire</td>
                    <td width="33%" class="text-center">Visa du Parent / Tuteur</td>
                    <td width="34%" class="text-center">
                        Le Chef d'Établissement<br><br><br>
                        <span style="font-weight: normal; font-size: 7.5px;">
                            Fait à  ................................., le ..............
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
