<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Emploi du temps - {{ $classe->nom }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        /* En-tête institutionnel */
        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .header td {
            vertical-align: middle;
        }

        .left-header {
            text-align: left;
            font-size: 11px;
        }

        .center-header {
            text-align: center;
        }

        .right-header {
            text-align: right;
            font-size: 11px;
        }

        .logo {
            max-height: 70px;
        }

        /* Titre du document */
        .title-block {
            text-align: center;
            margin-bottom: 20px;
        }

        .title-block h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .title-block p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #555;
        }

        /* Tableau de l'emploi du temps */
        table.emploi-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.emploi-table th,
        table.emploi-table td {
            border: 1px solid #444;
            padding: 8px;
            text-align: center;
        }

        table.emploi-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .heure-col {
            width: 120px;
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .cours-cell {
            background-color: #fdfdfd;
        }

        .matiere {
            font-weight: bold;
            color: #000;
            font-size: 13px;
        }

        .enseignant {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }

        .libre {
            color: #aaa;
            font-style: italic;
        }
    </style>
</head>

<body>

    <!-- EN-TÊTE : Logo, Établissement, Devise -->
    <!-- EN-TÊTE : Logo, Établissement, Slogan, etc. -->
    <div class="header">
        <table>
            <tr>
                <td class="left-header" style="width: 35%;">
                    <strong>RÉPUBLIQUE DU CAMEROUN</strong><br>
                    Paix - Travail - Patrie<br>
                    -------------------<br>
                    <strong>{{ $etablissement?->nom ?? 'Nom de l\'établissement' }}</strong><br>
                    @if ($etablissement?->adresse)
                        Adresse : {{ $etablissement->adresse }}<br>
                    @endif
                    @if ($etablissement?->telephone)
                        Tél : {{ $etablissement->telephone }}
                    @endif
                </td>

                <td class="center-header" style="width: 30%;">
                    <!-- Logo stocké (si tu stockes l'image dans public/storage/ ou public/images/) -->
                    @if ($etablissement?->logo)
                        <!-- Ajuste le chemin selon ta façon de stocker les images (ex: public_path('storage/' . $etablissement->logo)) -->
                        <img src="{{ public_path('storage/' . $etablissement->logo) }}" class="logo" alt="Logo">
                    @else
                        <h3 style="margin:0; font-size: 16px;">EMPLOI DU TEMPS</h3>
                    @endif

                    @if ($etablissement?->slogan)
                        <div style="font-size: 10px; font-style: italic; margin-top: 5px; color: #555;">
                            "{{ $etablissement->slogan }}"
                        </div>
                    @endif
                </td>

                <td class="right-header" style="width: 35%;">
                    @if ($etablissement?->code_ecole)
                        Code École : <strong>{{ $etablissement->code_ecole }}</strong><br>
                    @endif
                    @if ($etablissement?->email)
                        Email : {{ $etablissement->email }}<br>
                    @endif
                    Année Scolaire : <strong>{{ $anneeActive?->libelle ?? '2025/2026' }}</strong><br>
                    Date d'édition : {{ date('d/m/Y') }}
                </td>
            </tr>
        </table>
    </div>

    <!-- TITRE DE L'EMPLOI DU TEMPS -->
    <div class="title-block">
        <h2>Emploi du temps - Classe : {{ $classe->nom }}</h2>
        <p>Gestion officielle des séances de cours</p>
    </div>

    <!-- TABLEAU DE L'EMPLOI DU TEMPS -->
    <table class="emploi-table">
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
                    <!-- Heure -->
                    <td class="heure-col">
                        {{ \Carbon\Carbon::parse($creneau->heure_debut)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($creneau->heure_fin)->format('H:i') }}
                        @if ($creneau->libelle)
                            <div style="font-size: 9px; color: #666;">{{ $creneau->libelle }}</div>
                        @endif
                    </td>

                    <!-- Jours -->
                    @foreach ($jours as $jour)
                        @php
                            $seance = $seances->where('jour_id', $jour->id)->where('creneau_id', $creneau->id)->first();
                        @endphp

                        <td class="cours-cell">
                            @if ($seance)
                                <div class="matiere">
                                    {{ $seance->matiere->nom ?? 'Matière' }}
                                </div>
                                <div class="enseignant">
                               M / Mme     {{ $seance->enseignant->user->name ?? '' }}
                                </div>
                            @else
                                <span class="libre">Libre</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
