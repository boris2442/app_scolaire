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
            {{-- Ligne des grandes catégories --}}
            <tr>
                <th colspan="3"
                    style="background-color: #e6e6e6; border: 1px solid #333; text-align: center; padding: 8px;">
                    PROGRESSION</th>
                <th colspan="4"
                    style="background-color: #f2f2f2; border: 1px solid #333; text-align: center; padding: 8px;">
                    PERFORMANCES (Moy >= 10)</th>
                <th colspan="3"
                    style="background-color: #e6e6e6; border: 1px solid #333; text-align: center; padding: 8px;">
                    EFFECTIFS / TAUX</th>
            </tr>
            {{-- Ligne des sous-colonnes --}}
            <tr>
                {{-- Sous-colonnes Progression --}}
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">NLP
                    <span style="font-size: 7px;">
                        <i>
                            (Nombre Leçons Prevus)
                        </i></span>
                </th>

                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">NLF <span style="font-size: 7px;">
                        <i>
                            (Nombre Leçons Finies)
                        </i></span></th>
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">Taux</th>

                {{-- Sous-colonnes Performances --}}
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">G</th>
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">F</th>
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">T</th>
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">Effectif Total</th>

                {{-- Sous-colonnes Taux de réussite / Global --}}
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">G (%)</th>
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">F (%)</th>
                <th style="border: 1px solid #333; padding: 6px; font-size: 11px;">Total (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                {{-- Données Progression --}}
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">{{ $stats['lecons_totales'] }}
                </td>
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">{{ $stats['lecons_faites'] }}</td>
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">{{ $stats['taux_progression'] }}%
                </td>

                {{-- Données Performances (Réussites >= 10) --}}
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">{{ $stats['garcons_reussite'] }}
                </td>
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">{{ $stats['filles_reussite'] }}
                </td>
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">{{ $stats['reussite_globale'] }}
                </td>
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">{{ $stats['total'] }}</td>

                {{-- Données Taux --}}
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">
                    {{ $stats['garcons_count'] > 0 ? number_format(($stats['garcons_reussite'] / $stats['garcons_count']) * 100, 1) : 0 }}%
                </td>
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">
                    {{ $stats['filles_count'] > 0 ? number_format(($stats['filles_reussite'] / $stats['filles_count']) * 100, 1) : 0 }}%
                </td>
                <td style="border: 1px solid #333; text-align: center; padding: 8px;">
                    {{ $stats['taux_reussite'] }}%
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 13px;">
        <strong>Moyenne générale de la classe :</strong> {{ $stats['moyenne'] }} / 20
    </div>
</body>

</html>
