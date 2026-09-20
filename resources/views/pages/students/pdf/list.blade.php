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
                {{ $school->nom }}<br><em>"{{ $school->slogan }}"</em>
            </td>
            <td class="header-col">
                @php
                    $vraiCheminDansPublic = 'storage/' . $school->logo;
                @endphp
                @if ($school->logo && file_exists(public_path($vraiCheminDansPublic)))
                    <img src="{{ public_path($vraiCheminDansPublic) }}" width="60">
                @endif
            </td>
            <td class="header-col">
                <strong>Republic of Cameroon</strong><br>Peace - Work - Fatherland<br>
                <strong>Ministry of Secondary Education</strong><br>
                {{ $school->english_name }}<br><em>"{{ $school->english_slogan }}"</em>
            </td>
        </tr>
    </table>

    <div class="title-box">
        <h2>LISTE DES ÉLÈVES : {{ strtoupper($classe->nom) }}</h2>
        <p>Année Scolaire : {{ $actifYear->libelle }}</p>
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
            @foreach ($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->matricule }}</td>
                    <td style="text-align: left;">{{ strtoupper($student->nom) }} {{ $student->prenom }}</td>
                    <td>{{ $student->date_naissance }}</td>
                    <td>{{ $student->lieu_naissance }}</td>
                    <td>{{ $student->sexe }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
