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
            <td><strong>Classe :</strong> {{ $evaluation->classe->nom }}</td>
            <td><strong>Matière :</strong> {{ $evaluation->matiere->nom }}</td>
        </tr>
        <tr>
            <td><strong>Enseignant :</strong> {{ $evaluation->enseignant->user->name ?? 'N/A' }}</td>
            <td><strong>Date :</strong> {{ $date_impression }}</td>
        </tr>
    </table>

    <table class="stats-table">
        <thead>
            <tr>
                <th>Indicateur</th>
                <th>Effectif</th>
                <th>Réussites</th>
                <th>Taux (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Garçons</td>
                <td>{{ $stats['garcons_count'] }}</td>
                <td>{{ $stats['garcons_reussite'] }}</td>
                <td>{{ $stats['garcons_count'] > 0 ? number_format(($stats['garcons_reussite'] / $stats['garcons_count']) * 100, 2) : 0 }}%
                </td>
            </tr>
            <tr>
                <td>Filles</td>
                <td>{{ $stats['filles_count'] }}</td>
                <td>{{ $stats['filles_reussite'] }}</td>
                <td>{{ $stats['filles_count'] > 0 ? number_format(($stats['filles_reussite'] / $stats['filles_count']) * 100, 2) : 0 }}%
                </td>
            </tr>
            <tr style="font-weight: bold; background: #eee;">
                <td>TOTAL</td>
                <td>{{ $stats['total'] }}</td>
                <td>{{ $stats['reussite_globale'] }}</td>
                <td>{{ $stats['taux_reussite'] }}%</td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 50px;"><strong>Moyenne de la classe :</strong> {{ $stats['moyenne'] }} / 20</p>
</body>

</html>
