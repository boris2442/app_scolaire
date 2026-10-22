<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Récapitulatif Conseil de Classe - {{ $classe->nom }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm 10mm 10mm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            color: #000;
            line-height: 1.2;
        }

        /* En-tête officiel bilingue */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 5px;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: top;
            text-align: center;
        }

        .header-title-main {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-motto {
            font-size: 8px;
            font-style: italic;
            margin: 2px 0;
        }

        .header-sub {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-school {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .header-slogan {
            font-size: 8px;
            font-style: italic;
            color: #333;
        }

        .header-contact {
            font-size: 8px;
            margin-top: 2px;
        }

        .logo-img {
            max-width: 65px;
            max-height: 65px;
            object-fit: contain;
        }

        .header-divider {
            border-top: 1.5px solid #000;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        /* Titre de la fiche */
        .doc-title {
            text-align: center;
            margin-bottom: 12px;
        }

        .doc-title h2 {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }

        .doc-title p {
            margin: 3px 0 0 0;
            font-size: 10px;
            font-weight: bold;
        }

        /* Tableau récapitulatif */
        .recap-table {
            width: 100%;
            border-collapse: collapse;
        }

        .recap-table th,
        .recap-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            vertical-align: middle;
        }

        .recap-table th {
            background-color: #f2f2f2;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .text-left {
            text-align: left !important;
            padding-left: 6px !important;
        }

        /* Code couleur conditionnel */
        .moyenne-admis {
            color: #2563eb;
            /* Bleu */
            font-weight: bold;
        }

        .moyenne-echec {
            color: #dc2626;
            /* Rouge */
            font-weight: bold;
        }

        /* Pied de page / Signatures */
        .footer-signatures {
            margin-top: 15px;
            width: 100%;
        }

        .footer-signatures td {
            border: none;
            width: 50%;
            vertical-align: top;
            font-weight: bold;
            font-size: 9px;
        }
    </style>
</head>

<body>

    <!-- EN-TÊTE OFFICIEL BILINGUE (D'après la maquette) -->
    <table class="header-table">
        <tr>
            <!-- Section Gauche (FR) -->
            <td style="width: 40%;">
                <div class="header-title-main">RÉPUBLIQUE DU CAMEROUN</div>
                <div class="header-motto">Paix - Travail - Patrie</div>
                <div class="header-sub">MINISTÈRE DES ENSEIGNEMENTS SECONDAIRES</div>
                <div class="header-school">{{ $school->nom ?? 'LYCÉE BILINGUE DE BANCOU' }}</div>
                <div class="header-slogan">"{{ $school->slogan ?? 'volonté - travail - succès' }}"</div>
                <div class="header-contact">
                    {{ $school->ville ?? 'Bangou' }} — {{ $school->telephone ?? '+237 679 13 51 99' }}
                </div>
            </td>

            <!-- Logo Central -->
            <td style="width: 20%;">
                @if (!empty($school->logo) && file_exists(public_path($school->logo)))
                    <img src="{{ public_path($school->logo) }}" class="logo-img" alt="Logo">
                @else
                    <div style="font-size: 9px; font-weight: bold; border: 1px dashed #ccc; padding: 10px;">LOGO</div>
                @endif
            </td>

            <!-- Section Droite (EN) -->
            <td style="width: 40%;">
                <div class="header-title-main">REPUBLIC OF CAMEROON</div>
                <div class="header-motto">Peace - Work - Fatherland</div>
                <div class="header-sub">MINISTRY OF SECONDARY EDUCATION</div>
                <div class="header-school">{{ $school->nom_en ?? 'GBHS BANGOU' }}</div>
                <div class="header-slogan">"{{ $school->slogan_en ?? 'Willpower • Work • Success' }}"</div>
                <div class="header-contact">
                    {{ $school->email ?? 'lyceebilingue@gmail.com' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="header-divider"></div>

    <!-- TITRE DU DOCUMENT -->
    <div class="doc-title">
        <h2>Fiche Récapitulative du Conseil de Classe de Fin d'Année</h2>
        <p>CLASSE : {{ $classe->nom }} | ANNÉE SCOLAIRE : {{ $actifYear->libelle }}</p>
    </div>

    <!-- TABLEAU RÉCAPITULATIF DES ÉLÈVES -->
    <table class="recap-table">
        <thead>
            <tr>
                <th style="width: 4%;">N°</th>
                <th style="width: 12%;">Matricule</th>
                <th style="width: 28%;" class="text-left">Noms et Prénoms</th>
                <th style="width: 9%;">Date Naiss.</th>
                <th style="width: 9%;">Statut</th>
                <th style="width: 8%;">Moy. T1</th>
                <th style="width: 8%;">Moy. T2</th>
                <th style="width: 8%;">Moy. T3</th>
                <th style="width: 9%;">Moy. Annuelle</th>
                <th style="width: 5%;">Rang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recapData as $index => $row)
                @php
                    $ins = $row['inscription'];
                    $moyAnnuelle = $row['moyAnnuelle'];
                    // Règle des 10/20 : Bleu si >= 10, Rouge sinon
                    $classMoyenne = $moyAnnuelle >= 10 ? 'moyenne-admis' : 'moyenne-echec';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $ins->matricule ?? '-' }}</td>
                    <td class="text-left" style="font-weight: bold;">
                        {{ strtoupper($ins->nom) }} {{ $ins->prenom }}
                    </td>
                    <td>
                        {{ $ins->date_naissance ? date('d/m/Y', strtotime($ins->date_naissance)) : '-' }}
                    </td>
                    <td>
                        {{ $ins->est_redoublant ? 'Redoublant' : 'Régulier' }}
                    </td>
                    <td>
                        {{ $row['moyT1'] !== null ? number_format($row['moyT1'], 2, ',', ' ') : '-' }}
                    </td>
                    <td>
                        {{ $row['moyT2'] !== null ? number_format($row['moyT2'], 2, ',', ' ') : '-' }}
                    </td>
                    <td>
                        {{ $row['moyT3'] !== null ? number_format($row['moyT3'], 2, ',', ' ') : '-' }}
                    </td>
                    <td class="{{ $classMoyenne }}" style="font-size: 10.5px;">
                        {{ number_format($moyAnnuelle, 2, ',', ' ') }}
                    </td>
                    <td style="font-weight: bold;">
                        {{ $row['rang'] }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 15px;">
                        Aucun élève inscrit dans cette classe pour l'année scolaire active.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SIGNATURES EN BAS DE PAGE -->
    <table class="footer-signatures">
        <tr>
            <td style="text-align: left;">
                Le Professeur Principal
            </td>
            <td style="text-align: right;">
                Le Chef d'Établissement / Président du Conseil
            </td>
        </tr>
    </table>

</body>

</html>
