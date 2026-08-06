<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Rapport Statistique - {{ $classe->nom }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        /* En-tête officiel */
        .header {
            width: 100%;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .header td {
            vertical-align: top;
        }

        .school-info {
            font-size: 11px;
            color: #555;
            text-transform: uppercase;
        }

        .doc-title {
            text-align: right;
        }

        .doc-title h2 {
            margin: 0;
            color: #1a365d;
            font-size: 18px;
            text-transform: uppercase;
        }

        .doc-title p {
            margin: 5px 0 0;
            font-size: 12px;
            color: #666;
        }

        /* Section Informations Classe & Période */
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }

        .meta-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-box td {
            font-size: 13px;
        }

        /* Tableaux des KPIs (Indicateurs Clés) */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1a365d;
            border-left: 4px solid #1a365d;
            padding-left: 8px;
            margin-bottom: 10px;
            margin-top: 20px;
            text-transform: uppercase;
        }

        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .kpi-table th,
        .kpi-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            text-align: center;
        }

        .kpi-table th {
            background-color: #1a365d;
            color: #ffffff;
            font-size: 11px;
            text-transform: uppercase;
        }

        .kpi-table td {
            font-size: 13px;
            font-weight: bold;
            color: #1e293b;
        }

        /* Tableaux de répartition */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            text-align: left;
        }

        .data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 11px;
            text-transform: uppercase;
        }

        .data-table td {
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Pied de page */
        .footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <!-- En-tête -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 50%;">
                    <div class="school-info">
                        <strong>{{ $etablissement->nom ?? 'Établissement Scolaire' }}</strong><br>
                        {{ $etablissement->bp ?? '' }} - Tél: {{ $etablissement->telephone ?? '' }}<br>
                        Année Scolaire : {{ $etablissement->annee_courante ?? '2025/2026' }}
                    </div>
                </td>
                <td style="width: 50%;" class="doc-title">
                    <h2>Rapport Statistique</h2>
                    <p>Bilan Académique Global</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Informations Générales de la Classe -->
    <div class="meta-box">
        <table>
            <tr>
                <td><strong>Classe :</strong> {{ $classe->nom }}</td>
                <td><strong>Période :</strong> {{ $trimestre->nom ?? 'Trimestre' }}</td>
                <td class="text-right"><strong>Effectif Total :</strong> {{ $statsGlobales['total_eleves'] }} Élèves</td>
            </tr>
        </table>
    </div>

    <!-- Indicateurs Clés de Performance (KPIs) -->
    <!-- Indicateurs Clés de Performance (KPIs) -->
    <div class="section-title">Indicateurs Principaux de la Classe</div>
    <table class="kpi-table">
        <thead>
            <tr>
                <th>Moyenne Générale</th>
                <th>Note Maximale (Major)</th>
                <th>Note Minimale</th>
                <th>Taux de Réussite</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="color: #2563eb;">{{ $statsGlobales['moyenne_generale'] }} / 20</td>
                <td style="color: #16a34a;">
                    <strong>{{ $statsGlobales['note_max'] }} / 20</strong><br>
                    <span style="font-size: 10px; color: #475569; font-weight: normal;">
                        ({{ $statsGlobales['major_nom'] }} {{ $statsGlobales['major_prenom'] }})
                    </span>
                </td>
                <td style="color: #dc2626;">{{ $statsGlobales['note_min'] }} / 20</td>
                <td style="color: {{ $statsGlobales['taux_reussite'] >= 50 ? '#16a34a' : '#dc2626' }};">
                    {{ $statsGlobales['taux_reussite'] }} %
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Synthèse Admis / Refusés -->
    <table class="kpi-table" style="margin-top: 10px;">
        <thead>
            <tr>
                <th>Élèves Admis (≥ 10/20)</th>
                <th>Élèves Refusés (< 10/20)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="color: #16a34a;">{{ $statsGlobales['admis'] }} élève(s)</td>
                <td style="color: #dc2626;">{{ $statsGlobales['refuses'] }} élève(s)</td>
            </tr>
        </tbody>
    </table>

    <!-- Répartition par Tranches de Moyennes -->
    <div class="section-title" style="margin-top: 30px;">Répartition par Tranches de Notes</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tranche de Moyenne</th>
                <th class="text-center">Effectif (Nombre d'élèves)</th>
                <th class="text-right">Pourcentage / Effectif</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Excellence</strong> [16 - 20]</td>
                <td class="text-center">{{ $statsGlobales['tranches']['excellence'] }}</td>
                <td class="text-right">
                    {{ $statsGlobales['total_eleves'] > 0 ? round(($statsGlobales['tranches']['excellence'] / $statsGlobales['total_eleves']) * 100, 1) : 0 }}
                    %
                </td>
            </tr>
            <tr>
                <td><strong>Bien</strong> [14 - 15.99]</td>
                <td class="text-center">{{ $statsGlobales['tranches']['bien'] }}</td>
                <td class="text-right">
                    {{ $statsGlobales['total_eleves'] > 0 ? round(($statsGlobales['tranches']['bien'] / $statsGlobales['total_eleves']) * 100, 1) : 0 }}
                    %
                </td>
            </tr>
            <tr>
                <td><strong>Assez Bien</strong> [12 - 13.99]</td>
                <td class="text-center">{{ $statsGlobales['tranches']['assez_bien'] }}</td>
                <td class="text-right">
                    {{ $statsGlobales['total_eleves'] > 0 ? round(($statsGlobales['tranches']['assez_bien'] / $statsGlobales['total_eleves']) * 100, 1) : 0 }}
                    %
                </td>
            </tr>
            <tr>
                <td><strong>Passable</strong> [10 - 11.99]</td>
                <td class="text-center">{{ $statsGlobales['tranches']['passable'] }}</td>
                <td class="text-right">
                    {{ $statsGlobales['total_eleves'] > 0 ? round(($statsGlobales['tranches']['passable'] / $statsGlobales['total_eleves']) * 100, 1) : 0 }}
                    %
                </td>
            </tr>
            <tr style="background-color: #fef2f2;">
                <td><strong>Échec / Ajournés</strong> [&lt; 10]</td>
                <td class="text-center" style="color: #dc2626;">{{ $statsGlobales['tranches']['echec'] }}</td>
                <td class="text-right" style="color: #dc2626;">
                    {{ $statsGlobales['total_eleves'] > 0 ? round(($statsGlobales['tranches']['echec'] / $statsGlobales['total_eleves']) * 100, 1) : 0 }}
                    %
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Signature / Cachet de l'administration -->
    <div style="margin-top: 50px; width: 100%;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: center;">
                    <strong> La Direction</strong><br><br><br><br>
                    __________________________________
                </td>
            </tr>
        </table>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        Document généré officiellement par AcademiaPro — {{ date('d/m/Y à H:i') }}
    </div>

</body>

</html>
