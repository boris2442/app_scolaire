<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .stats-table th,
        .stats-table td {
            border: 1px solid #333;
            padding: 10px;
            text-align: center;
        }

        .stats-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="text-transform: uppercase;">Statistiques de Matière</h2>
        <p>Année Scolaire : {{ $evaluation->anneeScolaire->libelle ?? 'En cours' }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>

                    Niveau:</strong> {{ $evaluation->classe->niveau->nom ?? '' }}

                Classe :</strong> {{ $evaluation->classe->nom }}
            </td>
            <td>
                <strong>Matière :</strong> {{ $evaluation->matiere->nom }}
            </td>
        </tr>
        <tr>
            <td><strong>Enseignant :</strong> {{ $evaluation->enseignant->user->name ?? 'N/A' }}</td>
            <td><strong>Date :</strong> {{ $date_impression }}</td>
        </tr>
    </table>
    {{-- Bloc de Suivi de la Progression du Programme --}}
    <table class="stats-table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr>
                <th colspan="3"
                    style="background-color: #e6e6e6; border: 1px solid #333; text-align: center; padding: 8px;">
                    PROGRESSION
                </th>
                <th colspan="3"
                    style="background-color: #f2f2f2; border: 1px solid #333; text-align: center; padding: 8px;">
                    PERFORMANCES (Moy >= 10 / Total)
                </th>
                <th colspan="3"
                    style="background-color: #e6e6e6; border: 1px solid #333; text-align: center; padding: 8px;">
                    TAUX DE RÉUSSITE
                </th>
            </tr>
            <tr>
                <!-- Sous-colonnes Progression -->
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">
                    NLP <span style="font-size: 7px;"><i>(Prévus)</i></span>
                </th>
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">
                    NLF <span style="font-size: 7px;"><i>(Finies)</i></span>
                </th>
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">Taux</th>

                <!-- Sous-colonnes Performances (Format X / Y) -->
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">G</th>
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">F</th>
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">Total</th>

                <!-- Sous-colonnes Taux -->
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">G (%)</th>
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">F (%)</th>
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">Total (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <!-- Données Progression -->
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">{{ $stats['lecons_totales'] }}
                </td>
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">{{ $stats['lecons_faites'] }}</td>
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">{{ $stats['taux_progression'] }}%
                </td>

                <!-- Données Performances sous forme de fraction (ex: 10 / 15) -->
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">
                    <b>{{ $stats['garcons_reussite'] }}</b> / {{ $stats['garcons_count'] }}
                </td>
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">
                    <b>{{ $stats['filles_reussite'] }}</b> / {{ $stats['filles_count'] }}
                </td>
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">
                    <b>{{ $stats['reussite_globale'] }}</b> / {{ $stats['total'] }}
                </td>

                <!-- Données Taux -->
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">{{ $stats['garcons_taux'] }}%
                </td>
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">{{ $stats['filles_taux'] }}%</td>
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">{{ $stats['taux_reussite'] }}%
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 13px;">
        <strong>Moyenne générale de la classe :</strong> {{ $stats['moyenne'] }} / 20
    </div>
</body>

</html>
