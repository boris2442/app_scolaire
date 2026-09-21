<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Statistiques Globales - {{ $trimester->nom }}</title>
    <style>
        @page {
            margin: 15mm 10mm 15mm 10mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.2;
            background-color: #ffffff;
        }

        /* EN-TÊTE */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
        }

        .school-info {
            width: 60%;
            vertical-align: top;
        }

        .school-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
        }

        .doc-title {
            width: 40%;
            text-align: right;
            vertical-align: top;
        }

        .doc-title h1 {
            font-size: 14px;
            margin: 0;
            text-transform: uppercase;
            color: #2563eb;
        }

        /* PODIUM ETAB */
        .podium-container {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .podium-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            text-align: center;
        }

        .podium-value {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }

        .podium-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }

        /* SECTIONS & TITRES */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            background-color: #e2e8f0;
            padding: 4px 8px;
            margin-top: 10px;
            margin-bottom: 8px;
            border-left: 4px solid #2563eb;
        }

        /* TABLEAUX */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 8px;
            text-transform: uppercase;
            padding: 5px 6px;
            border: 1px solid #0f172a;
            text-align: center;
        }

        .data-table td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            font-size: 9px;
            text-align: center;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-left {
            text-align: left !important;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-success {
            color: #16a34a;
            font-weight: bold;
        }

        .text-danger {
            color: #dc2626;
            font-weight: bold;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8px;
            text-align: center;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    <!-- EN-TÊTE -->
    <table class="header-table">
        <tr>
            <td class="school-info">
                <div class="school-name">{{ $school->nom ?? 'ÉTABLISSEMENT SCOLAIRE' }}</div>
                <div>Année Scolaire : <b>{{ $actifYear->libelle ?? 'N/A' }}</b></div>
            </td>
            <td class="doc-title">
                <h1>Statistiques Globales</h1>
                <div style="font-size: 10px; font-weight: bold; color: #475569;">{{ $trimester->nom }}</div>
            </td>
        </tr>
    </table>

 <!-- PODIUM ÉTABLISSEMENT -->

@if (isset($stats['etablissement']))
    @php $etab = $stats['etablissement']; @endphp

    <table class="podium-container">
        <tr>
            <td width="20%">
                <div class="podium-card">
                    <div class="podium-value">
                        {{ $etab['total_eleves'] }}
                    </div>
                    <div class="podium-label">
                        Effectif Évalué
                    </div>
                </div>
            </td>

            <td width="20%">
                <div class="podium-card">
                    <div class="podium-value">
                        {{ $etab['moyenne_generale'] }} / 20
                    </div>
                    <div class="podium-label">
                        Moyenne Générale
                    </div>
                </div>
            </td>

            <td width="20%">
                <div class="podium-card">
                    <div class="podium-value text-success">
                        {{ $etab['taux_reussite'] }} %
                    </div>
                    <div class="podium-label">
                        Taux de Réussite
                    </div>
                </div>
            </td>

            <td width="20%">
                <div class="podium-card">
                    <div class="podium-value" style="font-size: 10px; color: #2563eb;">
                        {{ $etab['major']
                            ? $etab['major']->nom . ' ' . $etab['major']->prenom
                            : 'N/A'
                        }}
                    </div>

                    <div class="podium-label">
                        Major ({{ $etab['major']->moyenne_trimestre ?? 0 }}/20)
                    </div>
                </div>
            </td>

            <td width="20%">
                <div class="podium-card">
                    <div class="podium-value" style="font-size: 10px; color: #dc2626;">
                        {{ $etab['dernier']
                            ? $etab['dernier']->nom . ' ' . $etab['dernier']->prenom
                            : 'N/A'
                        }}
                    </div>

                    <div class="podium-label">
                        Dernier ({{ $etab['dernier']->moyenne_trimestre ?? 0 }}/20)
                    </div>
                </div>
            </td>
        </tr>
    </table>
@endif

    <!-- STATISTIQUES PAR SECTION -->
    <div class="section-title">1. Statistiques par Section</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-left" width="18%">Section</th>
                <th width="10%">Effectif</th>
                <th width="10%">Admis</th>
                <th width="12%">Moyenne</th>
                <th width="12%">Taux Réussite</th>
                <th class="text-left" width="19%">Major</th>
                <th class="text-left" width="19%">Dernier</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats['sections'] as $nomSection => $sec)
                <tr>
                    <td class="text-left font-bold">{{ strtoupper($nomSection) }}</td>
                    <td>{{ $sec['total_eleves'] }}</td>
                    <td>{{ $sec['admis'] }}</td>
                    <td class="font-bold">{{ $sec['moyenne_generale'] }}</td>
                    <td class="{{ $sec['taux_reussite'] >= 50 ? 'text-success' : 'text-danger' }}">
                        {{ $sec['taux_reussite'] }} %
                    </td>
                    <td class="text-left">
                        <b>{{ $sec['major']->nom ?? '' }}</b> ({{ $sec['major']->classe_nom ?? '' }}) - <span
                            class="text-success">{{ $sec['major']->moyenne_trimestre ?? 0 }}</span>
                    </td>
                    <td class="text-left">
                        <b>{{ $sec['dernier']->nom ?? '' }}</b> ({{ $sec['dernier']->classe_nom ?? '' }}) - <span
                            class="text-danger">{{ $sec['dernier']->moyenne_trimestre ?? 0 }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aucune donnée disponible</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- STATISTIQUES PAR CYCLE -->
    <div class="section-title">2. Statistiques par Cycle</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-left" width="18%">Cycle</th>
                <th width="10%">Effectif</th>
                <th width="10%">Admis</th>
                <th width="12%">Moyenne</th>
                <th width="12%">Taux Réussite</th>
                <th class="text-left" width="19%">Major</th>
                <th class="text-left" width="19%">Dernier</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats['cycles'] as $nomCycle => $cyc)
                <tr>
                    <td class="text-left font-bold">{{ strtoupper($nomCycle) }}</td>
                    <td>{{ $cyc['total_eleves'] }}</td>
                    <td>{{ $cyc['admis'] }}</td>
                    <td class="font-bold">{{ $cyc['moyenne_generale'] }}</td>
                    <td class="{{ $cyc['taux_reussite'] >= 50 ? 'text-success' : 'text-danger' }}">
                        {{ $cyc['taux_reussite'] }} %
                    </td>
                    <td class="text-left">
                        <b>{{ $cyc['major']->nom ?? '' }}</b> ({{ $cyc['major']->classe_nom ?? '' }}) - <span
                            class="text-success">{{ $cyc['major']->moyenne_trimestre ?? 0 }}</span>
                    </td>
                    <td class="text-left">
                        <b>{{ $cyc['dernier']->nom ?? '' }}</b> ({{ $cyc['dernier']->classe_nom ?? '' }}) - <span
                            class="text-danger">{{ $cyc['dernier']->moyenne_trimestre ?? 0 }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aucune donnée disponible</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- STATISTIQUES PAR NIVEAU -->
    <div class="section-title">3. Statistiques par Niveau</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-left" width="18%">Niveau</th>
                <th width="10%">Effectif</th>
                <th width="10%">Admis</th>
                <th width="12%">Moyenne</th>
                <th width="12%">Taux Réussite</th>
                <th class="text-left" width="19%">Major</th>
                <th class="text-left" width="19%">Dernier</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats['niveaux'] as $nomNiveau => $niv)
                <tr>
                    <td class="text-left font-bold">{{ strtoupper($nomNiveau) }}</td>
                    <td>{{ $niv['total_eleves'] }}</td>
                    <td>{{ $niv['admis'] }}</td>
                    <td class="font-bold">{{ $niv['moyenne_generale'] }}</td>
                    <td class="{{ $niv['taux_reussite'] >= 50 ? 'text-success' : 'text-danger' }}">
                        {{ $niv['taux_reussite'] }} %
                    </td>
                    <td class="text-left">
                        <b>{{ $niv['major']->nom ?? '' }}</b> ({{ $niv['major']->classe_nom ?? '' }}) - <span
                            class="text-success">{{ $niv['major']->moyenne_trimestre ?? 0 }}</span>
                    </td>
                    <td class="text-left">
                        <b>{{ $niv['dernier']->nom ?? '' }}</b> ({{ $niv['dernier']->classe_nom ?? '' }}) - <span
                            class="text-danger">{{ $niv['dernier']->moyenne_trimestre ?? 0 }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aucune donnée disponible</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        Généré automatiquement le {{ date('d/m/Y à H:i') }} - Page 1 / 1
    </div>

</body>

</html>
