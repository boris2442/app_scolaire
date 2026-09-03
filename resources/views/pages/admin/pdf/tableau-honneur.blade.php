<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Tableau d'Honneur</title>
    <style>
        /* CONFIGURATION PAGE DOMPDF */
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #f18ec2;
            font-family: Arial, Helvetica, sans-serif;
            color: #111111;
        }

        .certificate-container {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 12mm 15mm;
            background-color: #f18ec2;
            page-break-after: always;
            page-break-inside: avoid;
        }

        /* ENCADREMENT ET BORDURES */
        .outer-border {
            position: absolute;
            top: 6mm;
            left: 6mm;
            right: 6mm;
            bottom: 6mm;
            border: 10px double #431d36;
            z-index: 1;
        }

        .inner-border {
            position: absolute;
            top: 11mm;
            left: 11mm;
            right: 11mm;
            bottom: 11mm;
            border: 1.5px solid #2d1223;
            z-index: 2;
        }

        /* ROSACES AUX COINS */
        .corner-flower {
            position: absolute;
            width: 12mm;
            height: 12mm;
            font-size: 24px;
            color: #3d1b31;
            text-align: center;
            line-height: 12mm;
            z-index: 10;
        }

        .top-left {
            top: 11.5mm;
            left: 11.5mm;
        }

        .top-right {
            top: 11.5mm;
            right: 11.5mm;
        }

        .bottom-left {
            bottom: 11.5mm;
            left: 11.5mm;
        }

        .bottom-right {
            bottom: 11.5mm;
            right: 11.5mm;
        }

        /* CONTENU DU DIPLÔME */
        .content {
            position: relative;
            z-index: 20;
            width: 100%;
            height: 100%;
            padding: 4mm 6mm;
        }

        /* EN-TÊTE */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 4mm;
        }

        .header-table td {
            vertical-align: top;
            text-align: center;
        }

        .col-header {
            width: 38%;
        }

        .col-logo {
            width: 24%;
        }

        .txt-country {
            font-size: 9px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .txt-motto {
            font-family: "Times New Roman", serif;
            font-size: 8.5px;
            font-style: italic;
            margin: 1px 0;
        }

        .txt-admin {
            font-size: 8px;
            margin: 1px 0;
            text-transform: uppercase;
            line-height: 1.15;
        }

        .txt-school {
            font-size: 9px;
            font-weight: bold;
            margin-top: 1mm;
            text-transform: uppercase;
        }

        .stars-line {
            font-size: 6px;
            letter-spacing: 0.5px;
            margin: 0.5mm 0;
        }

        .logo-img {
            width: 26mm;
            height: 24mm;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* TITRE PRINCIPAL */
        .title-container {
            text-align: center;
            margin: 4mm 0 5mm 0;
        }

        .main-title {
            font-family: "Helvetica", "Arial Black", sans-serif;
            font-size: 26px;
            font-weight: 900;
            letter-spacing: 1px;
            color: #0d0d0d;
            text-transform: uppercase;
            text-shadow: 1px 1px 0px #ffffff, 2px 2px 0px #000000;
        }

        /* INTRO */
        .intro-block {
            margin-left: 2mm;
            margin-bottom: 5mm;
            font-size: 9.5px;
            line-height: 1.35;
        }

        .intro-en {
            font-family: "Times New Roman", serif;
            font-style: italic;
            font-weight: bold;
        }

        /* FORMULAIRE */
        .form-area {
            width: 100%;
        }

        .student-row {
            width: 60%;
            border-collapse: collapse;
            margin-bottom: 5mm;
        }

        .student-label {
            width: 15%;
            font-size: 9.5px;
            vertical-align: middle;
        }

        .label-bold {
            font-weight: bold;
            display: block;
        }

        .label-italic {
            font-family: "Times New Roman", serif;
            font-style: italic;
            font-size: 9px;
            display: block;
        }

        .student-box {
            width: 85%;
            border: 1.5px solid #000;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 2.5mm 4mm;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* .period-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
            font-size: 9.5px;
        }

        .period-table td {
            vertical-align: middle;
        }

        .underline-value {
            border-bottom: 1.5px solid #000;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            padding: 0 2mm;
        }

        .term-italic {
            font-family: "Times New Roman", serif;
            font-style: italic;
            font-size: 12px;
            font-weight: bold;
        } */




        .period-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4mm;
            font-size: 8.5px;
            /* Un peu plus petit pour aérer */
            table-layout: fixed;
        }

        .period-table td {
            vertical-align: top;
        }

        /* Alignement et style des valeurs soulignées */
        .period-table td.underline-value {
            vertical-align: bottom;
            border-bottom: 1.2px solid #000;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            padding-bottom: 1px;
        }

        .label-bold {
            display: block;
            font-weight: bold;
            font-size: 8.5px;
            line-height: 1.1;
        }

        .label-italic {
            display: block;
            font-family: "Times New Roman", Georgia, serif;
            font-style: italic;
            font-size: 8px;
            line-height: 1.1;
        }

        .term-italic {
            font-style: italic;
        }








        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
        }

        .results-table td {
            vertical-align: middle;
        }

        .avg-box {
            border: 1.5px solid #000;
            padding: 2mm 5mm;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .rank-text {
            font-size: 14px;
            font-weight: bold;
        }

        .students-text {
            font-size: 12px;
            font-weight: bold;
            margin-left: 3mm;
        }

        .distinctions-table {
            width: 85%;
            margin-left: 2mm;
            border-collapse: collapse;
            margin-bottom: 4mm;
        }

        .distinctions-table td {
            width: 50%;
            vertical-align: middle;
        }

        .checkbox-square {
            display: inline-block;
            width: 7mm;
            height: 6.5mm;
            border: 1.5px solid #000;
            vertical-align: middle;
            margin-right: 3mm;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .dist-title {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .honor-calligraphy {
            text-align: center;
            font-family: "Times New Roman", Georgia, serif;
            font-style: italic;
            font-size: 13px;
            margin: 3mm 0 4mm 0;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
        }

        .footer-table td {
            vertical-align: top;
        }

        .date-col {
            width: 45%;
            font-size: 9.5px;
            line-height: 1.6;
        }

        .sig-col {
            width: 55%;
            text-align: center;
            font-size: 9.5px;
        }

        .dotted-line {
            display: inline-block;
            border-bottom: 1px dotted #000;
            min-width: 35mm;
            text-align: center;
            font-weight: bold;
        }

        .sig-head {
            font-weight: bold;
            font-size: 10.5px;
            text-transform: uppercase;
        }

        .sig-sub {
            font-family: "Times New Roman", serif;
            font-style: italic;
            font-size: 9.5px;
        }
    </style>
</head>

<body>

    @foreach ($resultats as $eleve)
        @php
            $nomEleve = is_array($eleve) ? $eleve['nom'] ?? '' : $eleve->nom ?? '';
            $prenomEleve = is_array($eleve) ? $eleve['prenom'] ?? '' : $eleve->prenom ?? '';
            $moyenneEleve = is_array($eleve) ? $eleve['moyenne'] ?? 0 : $eleve->moyenne ?? ($moyenne ?? 0);
            $positionEleve = is_array($eleve)
                ? $eleve['position'] ?? ($eleve['rang'] ?? null)
                : $eleve->position ?? ($eleve->rang ?? null);
            $nombreEleves = is_array($eleve)
                ? $eleve['total_eleves'] ?? ($eleve['effectif'] ?? null)
                : $eleve->total_eleves ?? ($eleve->effectif ?? null);

            $nomTrimestre = isset($trimestre) ? optional($trimestre)->nom ?? 'Deuxième' : 'Deuxième';
            $anneeScolaire = optional($anneeActive)->libelle ?? '2023-2024';

            $logoPath = !empty($etablissement->logo)
                ? public_path('storage/' . $etablissement->logo)
                : public_path('images/logoeducation.jpeg');
        @endphp

        <div class="certificate-container">

            <div class="outer-border"></div>
            <div class="inner-border"></div>

            <div class="corner-flower top-left">❀</div>
            <div class="corner-flower top-right">❀</div>
            <div class="corner-flower bottom-left">❀</div>
            <div class="corner-flower bottom-right">❀</div>

            <div class="content">

                <table class="header-table">
                    <tr>
                        <td class="col-header">
                            <p class="txt-country">RÉPUBLIQUE DU CAMEROUN</p>
                            <p class="txt-motto">Paix - Travail - Patrie</p>
                            <div class="stars-line">**********************</div>
                            <p class="txt-admin">RÉGION DE {{ strtoupper($etablissement->region ?? 'L’OUEST') }}</p>
                            <p class="txt-admin">DÉLÉGATION RÉGIONALE DES ENSEIGNEMENTS SECONDAIRES</p>
                            <div class="stars-line">**********************</div>
                            <p class="txt-admin">DÉLÉGATION DÉPARTEMENTALE DES
                                {{ strtoupper($etablissement->department ?? 'HAUTS PLATEAUX') }}</p>
                            <p class="txt-school">{{ strtoupper($etablissement->nom ?? 'LYCÉE BILINGUE DE BANGOU') }}
                            </p>
                            <div class="stars-line">**********************</div>
                        </td>

                        <td class="col-logo">
                            @if (file_exists($logoPath))
                                <img src="{{ $logoPath }}" class="logo-img" alt="Logo">
                            @endif
                        </td>

                        <td class="col-header">
                            <p class="txt-country">REPUBLIC OF CAMEROON</p>
                            <p class="txt-motto">Peace - Work - Fatherland</p>
                            <div class="stars-line">**********************</div>
                            <p class="txt-admin">{{ $etablissement->english_region }} REGION</p>
                            <p class="txt-admin">REGIONAL DELEGATION OF SECONDARY EDUCATION</p>
                            <div class="stars-line">**********************</div>
                            <p class="txt-admin">DIVISIONAL DELEGATION OF
                                {{ strtoupper($etablissement->english_department ?? 'UPPER PLATEAUX') }}</p>
                            <p class="txt-school">
                                {{ strtoupper($etablissement->english_name ?? 'GOVERNMENT BILINGUAL HIGH SCHOOL BANGOU') }}
                            </p>
                            <div class="stars-line">**********************</div>
                        </td>
                    </tr>
                </table>

                <div class="title-container">
                    <span class="main-title">TABLEAU D'HONNEUR &nbsp;/&nbsp; HONOUR ROLL</span>
                </div>

                <div class="intro-block">
                    <div class="intro-fr">
                        En vertu des pouvoirs qui lui sont conférés, le Conseil de Classe décerne le <strong>TABLEAU
                            D'HONNEUR</strong>
                    </div>
                    <div class="intro-en">
                        By virtue of the power conferred on it, the class council awards an <strong>HONOUR ROLL</strong>
                    </div>
                </div>

                <div class="form-area">

                    <table class="student-row">
                        <tr>
                            <td class="student-label">
                                <span class="label-bold">A l'élève :</span>
                                <span class="label-italic">To the student:</span>
                            </td>
                            <td class="student-box">
                                {{ strtoupper($nomEleve) }} {{ strtoupper($prenomEleve) }}
                            </td>
                        </tr>
                    </table>

                    <table class="period-table">
                        <tr>
                            <!-- 1. LIBELLÉ CLASSE -->
                            <td style="width: 15%;">
                                <span class="label-bold">de la classe de:</span>
                                <span class="label-italic">of Form:</span>
                            </td>
                            <!-- 2. VALEUR CLASSE (Resserré) -->
                            <td style="width: 10%;" class="underline-value">
                                {{ $classe->nom ?? '6e A' }}
                            </td>

                            <!-- 3. LIBELLÉ TRIMESTRE -->
                            <td style="width: 27%; text-align: right; padding-right: 3mm;">
                                <span class="label-bold">pour son travail durant le:</span>
                                <span class="label-italic">for his/her performances during</span>
                            </td>
                            <!-- 4. VALEUR TRIMESTRE (Resserré) -->
                            <td style="width: 14%;" class="underline-value term-italic">
                                {{ $nomTrimestre }}
                            </td>

                            <!-- 5. LIBELLÉ ANNÉE -->
                            <td style="width: 20%; text-align: right; padding-right: 3mm;">
                                <span class="label-bold">Trimestre de l'année :</span>
                                <span class="label-italic">term of the school year</span>
                            </td>
                            <!-- 6. VALEUR ANNÉE (Élargi à 14% pour éviter la coupure) -->
                            <td style="width: 14%;" class="underline-value">
                                {{ $anneeScolaire }}
                            </td>
                        </tr>
                    </table>
                    <table class="results-table">
                        <tr>
                            <td style="width: 17%;">
                                <span class="label-bold">Moyenne Obtenue:</span>
                                <span class="label-italic">Average scored</span>
                            </td>
                            <td style="width: 22%;">
                                <div class="avg-box">
                                    {{ number_format((float) $moyenneEleve, 2, ',', ' ') }} /20
                                </div>
                            </td>
                            <td style="width: 16%;">
                                <span class="label-bold">Position occupée:</span>
                                <span class="label-italic">Rank</span>
                            </td>
                            <td style="width: 45%;">
                                <span class="rank-text">{{ $positionEleve ? $positionEleve . 'e/' : '---/' }}</span>
                                <span class="students-text">{{ $nombreEleves ?? '------------------------   ' }} Élèves</span>
                                <br>
                                <span class="label-italic" style="margin-left: 8mm;">on
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    Students</span>
                            </td>
                        </tr>
                    </table>

                    <table class="distinctions-table">
                        <tr>
                            <td>
                                <div class="checkbox-square"></div>
                                <div style="display: inline-block; vertical-align: middle;">
                                    <span class="dist-title">Avec ENCOURAGEMENTS</span><br>
                                    <span class="label-italic">With CREDITS</span>
                                </div>
                            </td>
                            <td>
                                <div class="checkbox-square"></div>
                                <div style="display: inline-block; vertical-align: middle;">
                                    <span class="dist-title">Avec FELICITATIONS</span><br>
                                    <span class="label-italic">With DISTINCTIONS</span>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div class="honor-calligraphy">
                        Tableau d'Honneur
                    </div>

                    <table class="footer-table">
                        <tr>
                            <td class="date-col">
                                <strong>Fait à :</strong> <span class="dotted-line"></span><br>
                                <span class="label-italic">Done at</span><br>
                                <strong>Le :</strong> <span class="dotted-line"></span><br>
                                <span class="label-italic">On the</span>
                            </td>
                            <td class="sig-col">
                                <div class="sig-head">POUR LE CHEF D'ETABLISSEMENT</div>
                                <div class="sig-sub">President du Conseil de Classe</div>
                                <div class="sig-sub" style="margin-top: 1mm;">The Principal, President of the Class
                                    Council</div>
                            </td>
                        </tr>
                    </table>

                </div>

            </div>
        </div>
    @endforeach

</body>

</html>
