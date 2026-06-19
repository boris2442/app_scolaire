<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Bulletin de Notes - {{ $inscription->eleve_nom }}</title>
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

        /* Tableaux de structures */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
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
            padding: 5px;
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
            height: 75px;
            vertical-align: top;
            padding: 4px;
            font-size: 9.5px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="en-tete">
        <div class="bloc-gauche">
            REPUBLIQUE DU CAMEROUN<br>
            Paix-Travail-Patrie<br>
            MINISTERE DES ENSEIGNEMENTS SECONDAIRES<br>
            <span style="text-transform: uppercase;">{{ $etablissement->nom ?? 'Établissement Scolaire' }}</span><br>
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
                {{ $inscription->date_naissance ? date('d/m/Y', strtotime($inscription->date_naissance)) : 'N/A' }} À
                {{ strtoupper($inscription->lieu_naissance ?? 'N/A') }}</td>
        </tr>
        <tr>
            <td><strong>TITULAIRE :</strong> {{ $classe->titulaire_nom ?? 'N/A' }}</td>
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
                $totalCoefG1 = 0;
                $totalPointsG1 = 0;

                // 1. On extrait de manière sécurisée les deux séquences du trimestre
                $seq1 = $sequences->values()->get(0);
                $seq2 = $sequences->values()->get(1);

                // 2. On stocke leurs IDs dans les variables
                $seq1Id = $seq1 ? $seq1->id : null;
                $seq2Id = $seq2 ? $seq2->id : null;

                $totalPointsSeq1 = 0;
                $totalPointsSeq2 = 0;
                $totalPointsTrimestre = 0;
                $totalCoefficientsClasse = 0;
            @endphp

            @foreach ($matieres as $matiere)
                @php
                    $idMat = $matiere->matiere_id ?? ($matiere->id ?? null);

                    // Gestion sécurisée du coefficient
                    $coef = 1;
                    if (isset($coefficients) && is_array($coefficients) && isset($coefficients[$idMat])) {
                        $coef = $coefficients[$idMat];
                    } elseif (isset($coefficients) && method_exists($coefficients, 'get')) {
                        $coef = $coefficients->get($idMat, $matiere->coefficient ?? 1);
                    } else {
                        $coef = $matiere->coefficient ?? 1;
                    }

                    // Récupération des notes directes grâce aux IDs extraits plus haut
                    $noteSeq1 = $seq1Id && isset($notes[$idMat][$seq1Id]) ? $notes[$idMat][$seq1Id] : null;
                    $noteSeq2 = $seq2Id && isset($notes[$idMat][$seq2Id]) ? $notes[$idMat][$seq2Id] : null;

                    // Valeurs par défaut à 0 pour les calculs physiques si la note est absente
                    $valNoteSeq1 = $noteSeq1 ?? 0;
                    $valNoteSeq2 = $noteSeq2 ?? 0;

                    // Calcul propre de la moyenne de la matière pour le trimestre
                    if ($noteSeq1 !== null && $noteSeq2 !== null) {
                        $moyenneMatiere20 = ($valNoteSeq1 + $valNoteSeq2) / 2;
                    } else {
                        $moyenneMatiere20 = $noteSeq1 ?? ($noteSeq2 ?? 0);
                    }

                    // Accumulation globale pour le tableau de statistiques du bas
                    $totalPointsSeq1 += $valNoteSeq1 * $coef;
                    $totalPointsSeq2 += $valNoteSeq2 * $coef;
                    $totalPointsTrimestre += $moyenneMatiere20 * $coef;
                    $totalCoefficientsClasse += $coef;
                @endphp

                <tr>
                    <td class="text-left"><strong>{{ $matiere->matiere_nom ?? $matiere->nom }}</strong></td>
                    <td class="text-center">{{ $noteSeq1 !== null ? number_format($noteSeq1, 2) : '-' }}</td>
                    <td class="text-center">{{ $noteSeq2 !== null ? number_format($noteSeq2, 2) : '-' }}</td>
                    <td class="text-center" style="font-weight: bold; background-color: #f9f9f9;">
                        {{ number_format($moyenneMatiere20, 2) }}</td>
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
                    <td class="text-left"
                        style="font-size: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $matiere->prof_nom ?? ($matiere->enseignant_nom ?? 'Non assigné') }}
                    </td>
                </tr>
            @endforeach

            <tr style="font-weight: bold; background-color: #f4f4f4;">
                <td class="text-left" style="text-transform: uppercase;">TOTAL SÉQUENTIEL</td>
                @foreach ($sequences as $seq)
                    <td class="text-center">-</td>
                @endforeach
                <td class="text-center">-</td>
                <td class="text-center">{{ $totalCoefficientsClasse }}</td>
                <td class="text-center">{{ number_format($totalPointsTrimestre, 2) }}</td>
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
            <tr>
                @php
                    // Formule stricte : Somme (Note * Coef) / Coefficient Total
                    $moyenneSeq1Finale = $totalCoefficientsClasse > 0 ? $totalPointsSeq1 / $totalCoefficientsClasse : 0;
                    $moyenneSeq2Finale = $totalCoefficientsClasse > 0 ? $totalPointsSeq2 / $totalCoefficientsClasse : 0;
                    $moyenneTrimFinale =
                        $totalCoefficientsClasse > 0 ? $totalPointsTrimestre / $totalCoefficientsClasse : 0;
                @endphp

                <td>{{ number_format($moyenneSeq1Finale, 2) }}</td>

                <td>{{ number_format($moyenneSeq2Finale, 2) }}</td>

                <td style="font-weight: bold; background-color: #f9f9f9;">
                    {{ number_format($moyenneTrimFinale, 2) }}
                </td>

                <td>{{ $bilan->rang ?? 'X' }}e / {{ $totalElevesClasse ?? 0 }}</td>

                <td style="font-size: 9px;">
                    @if ($moyenneTrimFinale >= 10)
                        Passable
                    @else
                        Insuffisant
                    @endif
                </td>

                <td>{{ isset($bilan->moyenne_classe) ? number_format($bilan->moyenne_classe, 2) : '0.00' }}</td>

                <td style="font-size: 8px; font-style: italic;">
                    {{ $moyenneTrimFinale >= 10 ? 'Passable, du courage !' : 'Doit redoubler d\'efforts.' }}
                </td>
            </tr>
        </tbody>
    </table>

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
                <span style="font-weight: normal; font-size: 8px;">Fait à {{ $etablissement->ville ?? 'Bafoussam' }},
                    le {{ date('d/m/Y') }}</span>
            </td>
        </tr>
    </table>

</body>

</html>
