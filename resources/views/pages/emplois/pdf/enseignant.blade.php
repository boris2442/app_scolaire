<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Emploi du temps - {{ $enseignant->name }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        p.subtitle {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        .heure {
            background-color: #f9fafb;
            font-weight: bold;
            font-size: 10px;
            width: 12%;
        }

        .cours {
            background-color: #eff6ff;
            border-radius: 4px;
            padding: 4px;
        }

        .matiere {
            font-weight: bold;
            color: #1e40af;
        }

        .classe {
            font-size: 10px;
            color: #4b5563;
            margin-top: 2px;
        }

        .libre {
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>

<body>

    <h1>Emploi du temps individuel</h1>
    <p class="subtitle">Enseignant : <strong>{{ $enseignant->name }}</strong></p>

    <table>
        <thead>
            <tr>
                <th>Créneaux / Jours</th>
                @foreach ($jours as $jour)
                    <th>{{ $jour->nom }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($creneaux as $creneau)
                <tr>
                    <td class="heure">
                        {{ \Carbon\Carbon::parse($creneau->heure_debut)->format('H:i') }}<br>
                        à<br>
                        {{ \Carbon\Carbon::parse($creneau->heure_fin)->format('H:i') }}
                    </td>

                    @foreach ($jours as $jour)
                        <td>
                            @php
                                $seance = $seances
                                    ->where('jour_id', $jour->id)
                                    ->where('creneau_id', $creneau->id)
                                    ->first();
                            @endphp

                            @if ($seance)
                                <div class="cours">
                                    <div class="matiere">{{ $seance->matiere->nom ?? 'Matière' }}</div>
                                    <div class="classe">
                                        {{ $seance->classe->niveau->nom ?? '' }} {{ $seance->classe->nom ?? '' }}
                                    </div>
                                </div>
                            @else
                                <span class="libre">- Libre -</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
