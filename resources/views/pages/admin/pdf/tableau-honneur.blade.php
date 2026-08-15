<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tableau d'honneur</title>

    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            width: 297mm;
            height: 210mm;
            background: #ffffff;
            font-family: "Times New Roman", Georgia, serif;
            color: #111827;
        }

        .certificate {
            width: 297mm;
            height: 208mm;
            /* Réduit à 208mm pour éviter le dépassement et la double page sous DomPDF */
            max-height: 208mm;
            margin: 0;
            background: #fff;
            position: relative;
            overflow: hidden;
            padding: 4mm 8mm;
            /* Ajusté légèrement */
            page-break-after: always;
            page-break-inside: avoid;
            break-after: page;
            break-inside: avoid;
        }

        /* =========================================
       BORDURES
       ========================================= */
        .border {
            position: absolute;
            left: 6mm;
            right: 6mm;
            height: 4mm;
            background: radial-gradient(circle, #111 0 1.3mm, transparent 1.4mm);
            background-size: 4mm 4mm;
            background-repeat: repeat-x;
        }

        .border.top {
            top: 2mm;
        }

        .border.bottom {
            bottom: 2mm;
        }

        /* =========================================
       HEADER (Tableau pour compatibilité PDF)
       ========================================= */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            position: relative;
            z-index: 2;
            margin-top: 2mm;
        }

        .header-left {
            width: 34%;
            text-align: center;
            vertical-align: top;
            padding-top: 1mm;
        }

        .header-left p {
            margin: 0;
            font-size: 10px;
            font-weight: 700;
            font-style: italic;
            line-height: 1.3;
        }

        .header-left .stars {
            letter-spacing: 2px;
            font-size: 8px;
            margin: 1px 0;
        }

        .header-center {
            width: 32%;
            text-align: center;
            vertical-align: top;
        }

        .school-logo {
            width: 32mm;
            height: 24mm;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .school-motto {
            margin-top: 1mm;
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .school-motto strong {
            display: block;
            font-size: 11px;
            letter-spacing: 1px;
            color: #333;
        }

        .school-motto span {
            display: block;
            font-size: 6px;
            font-weight: 700;
            color: #555;
            margin-top: 1px;
        }

        .header-right {
            width: 34%;
            text-align: center;
            vertical-align: top;
            padding-top: 1mm;
        }

        .header-right h1 {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 13px;
            font-weight: 800;
            font-style: italic;
            line-height: 1.2;
        }

        .header-right .tel {
            margin-top: 3px;
            font-size: 10px;
            font-weight: 700;
            font-style: italic;
        }

        /* =========================================
       TITRE
       ========================================= */
        .title-container {
            width: 50%;
            height: 12mm;
            margin: 8mm auto 8mm;
            /* Augmenté pour espacer du header */
            border: 1px solid #d1d5db;
            background: #f9f9f9;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .title {
            margin: 0;
            line-height: 12mm;
            font-family: Arial, sans-serif;
            font-size: 20px;
            letter-spacing: 2px;
            font-weight: 800;
            color: #f4c542;
        }

        /* =========================================
       FILIGRANE
       ========================================= */
        .watermark {
            position: absolute;
            left: 50%;
            top: 55%;
            transform: translate(-50%, -50%);
            width: 100mm;
            text-align: center;
            z-index: 1;
            opacity: 0.10;
            pointer-events: none;
        }

        .watermark img {
            display: block;
            width: 70mm;
            height: 55mm;
            margin: 0 auto;
            object-fit: contain;
        }

        .watermark-name {
            margin-top: -2mm;
            font-family: Arial, sans-serif;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #555;
        }

        .watermark-motto {
            margin-top: 1mm;
            font-family: Arial, sans-serif;
            font-size: 9px;
            font-weight: 700;
            color: #555;
        }

        /* =========================================
       CONTENU
       ========================================= */
        .content {
            position: relative;
            z-index: 2;
            padding: 0 12mm;
            margin-top: 6mm;
            /* Ajout d'un espace avant le texte */
        }

        .student-line {
            font-size: 15px;
            font-style: italic;
            margin-bottom: 5mm;
        }

        .student-name {
            font-weight: 700;
            display: inline-block;
            min-width: 80mm;
            border-bottom: 1px dotted #111;
            margin: 0 4px;
            color: #102A43;
        }

        .class-name {
            display: inline-block;
            min-width: 25mm;
            border-bottom: 1px dotted #111;
            margin-left: 4px;
            font-weight: 700;
            color: #102A43;
        }

        .achievement {
            font-size: 15px;
            font-style: italic;
            line-height: 1.5;
        }

        .grade {
            display: inline-block;
            min-width: 25mm;
            border-bottom: 1px dotted #111;
            font-weight: 700;
            margin: 0 4px;
            color: #102A43;
        }

        .honor {
            font-weight: 800;
            color: #102A43;
        }

        /* =========================================
       ILLUSTRATION DIPLÔME
       ========================================= */
        .diploma {
            position: absolute;
            left: 18mm;
            bottom: 14mm;
            width: 35mm;
            object-fit: contain;
            z-index: 2;
        }

        /* =========================================
       SIGNATURE
       ========================================= */
        .signature {
            position: absolute;
            right: 20mm;
            bottom: 22mm;
            width: 50mm;
            text-align: center;
            z-index: 3;
        }

        .signature-title {
            font-size: 13px;
            font-weight: 800;
            text-decoration: underline;
            color: #102A43;
            margin-bottom: 8mm;
        }

        /* =========================================
       SLOGAN & ANNÉE SCOLAIRE
       ========================================= */
        .slogan {
            position: absolute;
            left: 16mm;
            bottom: 12mm;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 11px;
            color: #087fba;
            font-weight: bold;
            font-style: italic;
            z-index: 3;
        }

        .academic-year {
            position: absolute;
            right: 18mm;
            bottom: 12mm;
            font-family: Arial, sans-serif;
            font-size: 10px;
            font-weight: 700;
            z-index: 3;
        }
    </style>
</head>

<body>
    @foreach ($resultats as $eleve)
        <div class="certificate">
            <div class="border top"></div>
            <div class="border bottom"></div>

            <table class="header-table">
                <tr>
                    <td class="header-left">
                        <p>PAIX-TRAVAIL-PATRIE</p>
                        <div class="stars">********************</div>
                        <p>MINISTÈRE DES ENSEIGNEMENTS SECONDAIRES</p>
                        <div class="stars">********************</div>
                        <p>DÉLÉGATION RÉGIONALE DU {{ strtoupper($etablissement->region ?? 'LITTORAL') }}</p>
                        <div class="stars">********************</div>
                        <p>DÉLÉGATION DÉPARTEMENTALE DU {{ strtoupper($etablissement->department ?? 'WOURI') }}</p>
                        <div class="stars">********************</div>
                    </td>
                    <td class="header-center">
                        @if (!empty($etablissement->logo))
                            <img src="{{ public_path('storage/' . $etablissement->logo) }}" class="school-logo"
                                alt="Logo" />
                        @else
                            <img src="{{ public_path('images/logoeducation.jpeg') }}" class="school-logo"
                                alt="Logo" />
                        @endif
                        <div class="school-motto">
                            <strong>{{ strtoupper($etablissement->code_ecole ?? 'COLTECHNI') }}</strong>
                            <span>DEVISE :
                                {{ strtoupper($etablissement->slogan ?? 'RIGUEUR - TRAVAIL - SUCCÈS') }}</span>
                        </div>
                    </td>
                    <td class="header-right">
                        <h1>
                            {{ strtoupper($etablissement->nom ?? 'COLLÈGE TECHNIQUE DE L’INNOVATION') }}<br />
                            ({{ strtoupper($etablissement->code_ecole ?? 'COLTECHNI') }})
                        </h1>
                        <div class="tel">Tél : {{ $etablissement->telephone ?? '675.284.263 / 654.221.441' }}</div>
                    </td>
                </tr>
            </table>

            <div class="title-container">
                <h2 class="title">TABLEAU D’HONNEUR</h2>
            </div>

            <div class="watermark">
                @if (!empty($etablissement->logo))
                    <img src="{{ public_path('storage/' . $etablissement->logo) }}" alt="Filigrane" />
                @else
                    <img src="{{ public_path('images/logoeducation.jpeg') }}" alt="Filigrane" />
                @endif
                <div class="watermark-name">{{ strtoupper($etablissement->code_ecole ?? 'COLTECHNI') }}</div>
                <div class="watermark-motto">DEVISE :
                    {{ strtoupper($etablissement->slogan ?? 'RIGUEUR - TRAVAIL - SUCCÈS') }}</div>
            </div>

            <main class="content">
                <div class="student-line">
                    L’élève
                    <span class="student-name">
                        {{ strtoupper(is_array($eleve) ? $eleve['nom'] : $eleve->nom) }}
                        {{ strtoupper(is_array($eleve) ? $eleve['prenom'] : $eleve->prenom) }}
                    </span>
                    de la classe de
                    <span class="class-name">{{ strtoupper($classe->nom) }}</span>
                </div>

                <div class="achievement">
                    a obtenu la note de
                    <span class="grade">
                        {{ number_format(is_array($eleve) ? $eleve['moyenne'] : $moyenne ?? 0, 2) }}/20
                    </span>, son abnégation au travail et son assiduité tout au long de cette année lui confèrent le
                    mérite d’un
                    <span class="honor"> TABLEAU D’HONNEUR. </span>
                </div>
            </main>

            @if (file_exists(public_path('images/diploma.jpeg')))
                <img src="{{ public_path('images/diploma.jpeg') }}" class="diploma" alt="Diplôme" />
            @endif

            <div class="signature">
                <div class="signature-title">Le principal</div>
            </div>

            <div class="slogan">On ne recrute pas l’élite, on la forme !!!</div>
            <div class="academic-year">Année scolaire : {{ optional($anneeActive)->libelle ?? '2025-2026' }}</div>
       
        </div>
    @endforeach
</body>

</html>
