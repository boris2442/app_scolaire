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

    <!-- 1. EN-TÊTE MINESEC (Calqué sur image_3d3e18.png) -->
    <div class="en-tete">
        <div class="bloc-gauche">
            REPUBLIQUE DU CAMEROUN<br>
            Paix-Travail-Patrie<br>
            MINISTERE ENSEIGNEMENT SECONDAIRE<br>
            DELEGATION REGIONALE DU LITTORAL<br>
            DELEGATION DEPARTEMENTALE DU WOURI<br>
            COMPLEXE SCOLAIRE BILINGUE RAINBOW<br>
            "Discipline-Travail-Succès"
        </div>
        <div class="bloc-centre">
            <!-- Emplacement Logo École -->
            <div
                style="border: 1px solid #333; width: 50px; height: 40px; margin: 0 auto; line-height: 40px; font-size: 8px; font-weight: bold;">
                LOGO</div>
        </div>
        <div class="bloc-droite">
            REPUBLIC OF CAMEROON<br>
            Peace-Work-Fatherland<br>
            MINISTRY OF SECONDARY EDUCATION<br>
            REGIONAL DELEGATION OF LITTORAL<br>
            DEPARTEMENTAL DELEGATION OF WOURI<br>
            COMPLEXE SCOLAIRE BILINGUE RAINBOW<br>
            "Discipline-Travail-Succès"
        </div>
        <div class="clear"></div>
    </div>

    <!-- 2. TITRE DE LA PERIODE -->
    <div class="titre-bulletin">
        <h2>BULLETIN DE NOTES DU {{ $trimestre->nom }}</h2>
        <p>ANNÉE SCOLAIRE : {{ $inscription->annee_libelle }}</p>
    </div>

    <!-- 3. INFORMATIONS DE L'ÉLÈVE -->
    <table class="table-eleve">
        <tr>
            <td width="60%"><strong>NOM ET PRENOM :</strong> {{ $inscription->eleve_nom }}
                {{ $inscription->eleve_prenom }}</td>
            <td width="40%"><strong>NÉ(E) LE :</strong> 2000-09-10 À DOUALA</td> {{-- Exemple statique à dynamiser selon tes colonnes élèves --}}
        </tr>
        <tr>
            <td><strong>TITULAIRE :</strong> {{ $classe->titulaire_nom ?? 'N/A' }}</td>
            <td>
                <table style="width:100%; margin:0; border:none;">
                    <tr style="border:none;">
                        <td style="border:none; padding:0;" width="33%"><strong>CLASSE :</strong>
                            {{ $inscription->classe_nom }}</td>
                        <td style="border:none; padding:0;" width="33%"><strong>SEXE :</strong> M</td>
                        <td style="border:none; padding:0;" width="34%"><strong>MATRICULE :</strong>
                            {{ $inscription->matricule ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 4. TABLEAU DES NOTES UNIQUE -->
    <table class="table-notes">
        <thead>
            <tr>
                <th width="35%">Matières</th>
                <th width="10%">Notes</th>
                <th width="8%">Coeff</th>
                <th width="10%">N*C</th>
                <th width="15%">Appréciation</th>
                <th width="22%">Nom du professeur</th>
            </tr>
        </thead>
        <tbody>
            {{-- Note d'ingénieur : Simulation des groupes d'enseignement comme sur l'image --}}

            <!-- EXEMPLE GROUPE 1 -->
            @php
                $totalNoteG1 = 0;
                $totalCoefG1 = 0;
                $totalPointsG1 = 0;
                $hasG1 = false;
            @endphp
            @foreach ($matieres as $matiere)
                {{-- Ici tu pourras filtrer par groupe si tu ajoutes une colonne groupe_id. Pour l'exemple on liste --}}
                @php
                    $noteValeur = $notes[$matiere->matiere_id][$sequences->first()->id ?? 0] ?? 0; // À adapter selon ton calcul de moyenne trimestrielle
                    $coef = $coefficients[$matiere->matiere_id] ?? 1;
                    $nc = $noteValeur * $coef;

                    // Accumulateurs globaux pour le total final
                    $totalCoefG1 += $coef;
                    $totalPointsG1 += $nc;
                @endphp
                <tr>
                    <td class="text-left"><strong>{{ $matiere->matiere_nom }}</strong></td>
                    <td class="text-center">{{ number_format($noteValeur, 2) }}</td>
                    <td class="text-center">{{ $coef }}</td>
                    <td class="text-center">{{ number_format($nc, 2) }}</td>
                    <td class="text-center" style="font-size: 8.5px;">
                        @if ($noteValeur >= 16)
                            Très bien
                        @elseif($noteValeur >= 14)
                            Bien
                        @elseif($noteValeur >= 12)
                            Assez bien
                        @elseif($noteValeur >= 10)
                            Passable
                        @else
                            Insuffisant
                        @endif
                    </td>
                    <td class="text-left" style="font-size: 8.5px;">{{ $matiere->prof_nom }}</td>
                </tr>
            @endforeach

            <!-- LIGNE TOTAL GROUPE 1 -->
            <tr class="bg-groupe">
                <td class="text-left">Total Groupe 1</td>
                <td class="text-center">-</td>
                <td class="text-center">{{ $totalCoefG1 }}</td>
                <td class="text-center">{{ number_format($totalPointsG1, 2) }}</td>
                <td colspan="2"></td>
            </tr>

            <!-- LIGNE TOTAL SÉQUENTIEL / GÉNÉRAL -->
            <tr style="font-weight: bold;">
                <td class="text-left" style="text-transform: uppercase;">Total séquentiel</td>
                <td class="text-center">-</td>
                <td class="text-center">{{ $totalCoefG1 }}</td>
                <td class="text-center">{{ number_format($totalPointsG1, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <!-- 5. BLOC STATISTIQUES, RANG ET DECISIONS -->
    <table class="table-stats text-center">
        <thead>
            <tr>
                <th width="16%">MOY SEQ {{ $sequences->first()->id ?? 'X' }}</th>
                <th width="16%">MOY TRIM</th>
                <th width="16%">Rang</th>
                <th width="16%">Mention</th>
                <th width="16%">Moy Gen</th>
                <th width="20%">Décisions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                {{-- Calculs à lier dynamiquement avec ta table 'bilans' --}}
                @php
                    $moyenneEleve = $totalCoefG1 > 0 ? $totalPointsG1 / $totalCoefG1 : 0;
                @endphp
                <td>{{ number_format($moyenneEleve, 2) }}</td>
                <td>{{ number_format($moyenneEleve, 2) }}</td>
                <td>3e / 11</td> {{-- Remplacer par $bilan->rang --}}
                <td style="font-size: 9px;">
                    @if ($moyenneEleve >= 10)
                        Passable
                    @else
                        Insuffisant
                    @endif
                </td>
                <td>10.03</td> {{-- Moyenne générale de la classe --}}
                <td style="font-size: 9px; font-style: italic; font-weight: normal;">
                    {{ $moyenneEleve >= 10 ? 'Travail passable, du courage !' : 'Doit redoubler d\'efforts.' }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- 6. BLOC DISCIPLINE -->
    <table class="table-discipline text-center">
        <tr>
            <td width="20%">☐ Retards : _____</td>
            <td width="20%">☐ Absences : _____</td>
            <td width="20%">☐ Consignes : _____</td>
            <td width="20%">☐ Avert Conduite</td>
            <td width="20%">☐ Exclusion</td>
        </tr>
    </table>

    <!-- 7. ZONE DES SIGNATURES -->
    <table class="table-signatures">
        <tr>
            <td width="33%" class="text-center">Nom et visa du Prof principal</td>
            <td width="33%" class="text-center">Visa du Parent</td>
            <td width="34%" class="text-center">
                Nom et visa du Principal<br><br><br><br>
                <span style="font-weight: normal; font-size: 8px;">Fait à Douala, le {{ date('d/m/Y') }}</span>
            </td>
        </tr>
    </table>

</body>

</html>
