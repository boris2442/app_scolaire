<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-col {
            width: 33%;
            text-align: center;
            font-size: 9px;
            text-transform: uppercase;
            vertical-align: top;
        }

        .title-box {
            text-align: center;
            margin: 20px 0;
            border-top: 1px solid #000;
            padding-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td class="header-col">
                <strong>République du Cameroun</strong><br>Paix - Travail - Patrie<br>
                <strong>Ministère des Enseignements Secondaires</strong><br>
                {{ $etablissement->nom }}<br><em>"{{ $etablissement->slogan }}"</em>
            </td>
            <td class="header-col">
                @php
                    $vraiCheminDansPublic = 'storage/' . $etablissement->logo;
                @endphp
                @if ($etablissement->logo && file_exists(public_path($vraiCheminDansPublic)))
                    <img src="{{ public_path($vraiCheminDansPublic) }}" width="60">
                @endif
            </td>
            <td class="header-col">
                <strong>Republic of Cameroon</strong><br>Peace - Work - Fatherland<br>
                <strong>Ministry of Secondary Education</strong><br>
                {{ $etablissement->english_name }}<br><em>"{{ $etablissement->english_slogan }}"</em>
            </td>
        </tr>
    </table>

  <div class="title-box">
    <h2>LISTE DES ÉLÈVES : {{ strtoupper($classe->nom) }}</h2>
    <p>Année Scolaire : {{ $anneeActive->libelle }}</p>
</div>
    <table>
        <thead>
            <tr>
                <th>N°</th>
                <th>Matricule</th>
                <th>Nom et Prénom</th>
                <th>Date de Naissance</th>
                <th>Lieu de Naissance</th>
                <th>Sexe</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($eleves as $index => $eleve)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $eleve->matricule }}</td>
                    <td style="text-align: left;">{{ strtoupper($eleve->nom) }} {{ $eleve->prenom }}</td>
                    <td>{{ $eleve->date_naissance }}</td>
                    <td>{{ $eleve->lieu_naissance }}</td>
                    <td>{{ $eleve->sexe }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
