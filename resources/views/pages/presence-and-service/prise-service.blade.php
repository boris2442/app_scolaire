```blade
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Certificat de Prise de Service</title>

    <style>
        @page {
            margin: 12mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5px;
            color: #000;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-weight: bold;
        }

        td {
            vertical-align: bottom;
            padding-bottom: 1px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-table td {
            text-transform: uppercase;
            vertical-align: top;
            font-size: 7.5px;
            font-weight: bold;
            line-height: 1.15;
            padding-bottom: 0;
        }

        /* PHOTO */
        .photo-box {
            position: absolute;
            top: 105px;
            right: 0px;
            width: 75px;
            height: 90px;
            text-align: center;
            background: #fff;
            z-index: 10;
        }

        /* ESPACE RÉSERVÉ À LA PHOTO */
        .title-container {
            margin-right: 80px;
        }

        /* TITRE */
        .title-box {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .title-box h2 {
            font-size: 13px;
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
            font-weight: bold;
        }

        .title-box .en-title {
            font-size: 10.5px;
            font-style: italic;
            font-weight: bold;
            margin-top: 2px;
        }

        .ref-line {
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 14px;
        }

        .label-fr {
            font-weight: bold;
            font-size: 9px;
        }

        .label-en {
            font-style: italic;
            font-size: 8px;
            color: #222;
        }

        .val-text {
            font-weight: bold;
            font-size: 9.5px;
            text-transform: uppercase;
        }

        .line-underline {
            border-bottom: 1px solid #000;
        }

        .footer-divider {
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            border-top: 1px solid #000;
            padding-top: 4px;
            font-size: 7.5px;
            text-align: center;
            color: #111;
        }
    </style>
</head>

<body>

    {{-- =========================================================
         PHOTO
    ========================================================== --}}
    <div class="photo-box">

        @if (isset($user->avatar) && $user->avatar && file_exists(storage_path('app/public/' . $user->avatar)))
            <img src="{{ storage_path('app/public/' . $user->avatar) }}"
                style="width: 73px; height: 88px; object-fit: cover;">
        @else
            <div style="font-size: 8px; padding-top: 38px;">
                PHOTO
            </div>
        @endif

    </div>


    {{-- =========================================================
         EN-TÊTE OFFICIEL BILINGUE
    ========================================================== --}}
    <table class="header-table" style="margin-bottom: 10px; font-weight:bold;">

        <tr style="font-weight: bold;">

            {{-- FRANÇAIS --}}
            <td style="width: 38%; text-align: center; font-weight: bold;">

                RÉPUBLIQUE DU CAMEROUN<br>

                <i>
                    Paix-Travail-Patrie
                </i><br>

                *******<br>

                RÉGION DE {{ $etablissement->region }}<br>

                *******<br>

                DÉPARTEMENT DE LA {{ $etablissement->department }}<br>

                *******<br>

                DÉLÉGATION DÉPARTEMENTALE DES ENSEIGNEMENTS SECONDAIRES<br>

                *******<br>

                ARRONDISSEMENT DE {{ $etablissement->sub_division }}<br>

                *******<br>

                <strong>
                    {{ $etablissement->nom }}
                </strong><br>

                BP : - - TEL : {{ $etablissement->telephone }}<br>

                Immatriculation :
                {{ $etablissement->code_ecole }}

            </td>


            {{-- LOGO --}}
            <td
                style="
                width: 18%;
                text-align: center;
                vertical-align: middle;
                font-weight:bold;
            ">

                @if (file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}" style="width: 55px; height: auto;">
                @endif

            </td>


            {{-- ENGLISH --}}
            <td
                style="
                width: 44%;
                text-align: center;
                padding-right: 80px;
            ">

                REPUBLIC OF CAMEROON<br>

                <i>
                    Peace-Work-Fatherland
                </i><br>

                *******<br>

                {{ $etablissement->english_region }} REGION<br>

                *******<br>

                {{ $etablissement->english_department }} DIVISION<br>

                *******<br>

                DIVISIONAL DELEGATION FOR SECONDARY EDUCATION<br>

                *******<br>

                {{ $etablissement->english_sub_division }} SUB DIVISION<br>

                *******<br>

                <strong>
                    {{ $etablissement->english_name }}
                </strong><br>

                PO-BOX : - - PHONE :
                {{ $etablissement->telephone }}<br>

                Immatriculation :
                {{ $etablissement->code_ecole }}

            </td>

        </tr>

    </table>


    {{-- =========================================================
         TITRE
    ========================================================== --}}
    <div class="title-container">

        <div class="title-box">

            <h2>
                CERTIFICAT DE PRISE DE SERVICE
            </h2>

            <div class="en-title">
                CERTIFICATE OF ASSUMPTION OF DUTY
            </div>

        </div>


        {{-- RÉFÉRENCE --}}
        <div class="ref-line">
            N°________________/________________/
        </div>

    </div>


    {{-- =========================================================
         JE SOUSSIGNÉ
    ========================================================== --}}
    <table>
        <tr>
            <td style="width: 15%;" class="label-fr">
                Je soussigné :
            </td>

            <td style="width: 85%;">
                --------------------------------------------------------------
            </td>
        </tr>

        <tr>
            <td class="label-en">
                I, the undersigned
            </td>

            <td></td>
        </tr>
    </table>

    {{-- =========================================================
         PROVISEUR / PRINCIPAL
    ========================================================== --}}
    <table>

        <tr>

            <td style="width: 20%;" class="label-fr">
                PROVISEUR / PRINCIPAL
            </td>

            <td style="width: 5%; font-size: 8.5px;">
                du
            </td>

            <td style="
            width: 75%;
            text-align: center;
        " class="label-fr">

                {{ $etablissement->nom }}

            </td>

        </tr>

        <tr>

            <td class="label-en">
                PRINCIPAL
            </td>

            <td class="label-en">
                of
            </td>

            <td style="text-align: center;" class="label-en">
                {{ $etablissement->english_name }}
            </td>

        </tr>

    </table>


    {{-- =========================================================
         CERTIFIE QUE
    ========================================================== --}}
    <table>

        <tr>

            <td style="
                width: 13%;
                white-space: nowrap;
            " class="label-fr">

                Certifie que :

            </td>

            <td style="
                width: 87%;
                padding-left: 10px;
            "
                class="line-underline val-text">

                {{ $user->name ?? '' }}

            </td>

        </tr>

        <tr>

            <td class="label-en">
                Certify that:
            </td>

            <td></td>

        </tr>

    </table>


    {{-- =========================================================
         GRADE / MATRICULE
    ========================================================== --}}
    <table>

        <tr>

            <td style="width: 8%;" class="label-fr">
                Grade:
            </td>

            <td style="
                width: 40%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ $enseignant->grade ?? '' }}

            </td>

            <td style="width: 2%;"></td>

            <td style="width: 11%;" class="label-fr">
                Matricule :
            </td>

            <td style="
                width: 39%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ $enseignant->matricule ?? '' }}

            </td>

        </tr>

        <tr>

            <td class="label-en">
                Grade:
            </td>

            <td></td>

            <td></td>

            <td class="label-en">
                Service Code :
            </td>

            <td></td>

        </tr>

    </table>


    {{-- =========================================================
         NÉ(E) LE / À
    ========================================================== --}}
    <table>

        <tr>

            <td style="width: 9%;" class="label-fr">
                Né(e) le :
            </td>

            <td style="
                width: 39%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ isset($enseignant->birth_date) && $enseignant->birth_date
                    ? \Carbon\Carbon::parse($enseignant->birth_date)->format('d/m/Y')
                    : '' }}

            </td>

            <td style="width: 2%;"></td>

            <td style="width: 4%;" class="label-fr">
                À:
            </td>

            <td style="
                width: 46%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ $enseignant->birth_place ?? '' }}

            </td>

        </tr>

        <tr>

            <td class="label-en">
                Born on:
            </td>

            <td></td>

            <td></td>

            <td class="label-en">
                At :
            </td>

            <td></td>

        </tr>

    </table>


    {{-- =========================================================
         SITUATION MATRIMONIALE / POSTE ANTÉRIEUR
    ========================================================== --}}
    <table>

        <tr>

            <td style="width: 22%;" class="label-fr">
                Situation matrimoniale:
            </td>

            <td style="
                width: 26%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ $enseignant->marital_status ?? '' }} / {{ $enseignant->number_of_children ?? 0 }}

            </td>

            <td style="width: 2%;"></td>

            <td style="width: 16%;" class="label-fr">
                Poste antérieur:
            </td>

            <td style="
                width: 34%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ $enseignant->previous_position ?? '' }}

            </td>

        </tr>

        <tr>

            <td class="label-en">
                Family situation
            </td>

            <td></td>

            <td></td>

            <td class="label-en">
                Former post:
            </td>

            <td></td>

        </tr>

    </table>


    {{-- =========================================================
         LIEU
    ========================================================== --}}
    <table>

        <tr>

            <td style="width: 6%;" class="label-fr">
                Lieu:
            </td>

            <td style="
                width: 94%;
                padding-left: 10px;
            "
                class="line-underline val-text">

                {{ $enseignant->previous_school ?? '//' }}

            </td>

        </tr>

        <tr>

            <td class="label-en">
                At:
            </td>

            <td></td>

        </tr>

    </table>


    {{-- =========================================================
         ACTE D'AFFECTATION / MUTATION / NOMINATION
    ========================================================== --}}
    <table style="margin-bottom: 2px;">

        <tr>

            <td class="label-fr">

                Affecté(e)/Muté(e)/Nommé(e)
                par note de service/décision/arrêté (1) N°:

            </td>

        </tr>

        <tr>

            <td class="label-en">

                Posted, Transfered, Appointed by Service Note,
                Decision or Order (1) N°:

            </td>

        </tr>

    </table>


    <table>

        <tr>

            <td style="width: 66%;" class="line-underline val-text">

                {{ $enseignant->appointment_document_number ?? '' }}

            </td>

            <td style="width: 2%;"></td>

            <td style="width: 5%;" class="label-fr">
                Du :
            </td>

            <td style="
                width: 27%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ isset($enseignant->appointment_date) && $enseignant->appointment_date
                    ? \Carbon\Carbon::parse($enseignant->appointment_date)->format('d/m/Y')
                    : '' }}

            </td>

        </tr>

        <tr>

            <td></td>

            <td></td>

            <td class="label-en">
                Of :
            </td>

            <td></td>

        </tr>

    </table>


    {{-- =========================================================
         PRISE DE SERVICE
    ========================================================== --}}
    <table>

        <tr>

            <td style="width: 52%;" class="label-fr">

                A effectivement pris service
                à son poste de travail depuis le :

            </td>

            <td style="
                width: 48%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ isset($enseignant->service_assumption_date) && $enseignant->service_assumption_date
                    ? \Carbon\Carbon::parse($enseignant->service_assumption_date)->format('d/m/Y')
                    : '' }}

            </td>

        </tr>

        <tr>

            <td class="label-en">

                Has effectively assumed duty
                at his workstation since:

            </td>

            <td></td>

        </tr>

    </table>


    {{-- =========================================================
         EN QUALITÉ DE
    ========================================================== --}}
    <table>

        <tr>

            <td style="width: 13%;" class="label-fr">
                En qualité de:
            </td>

            <td style="
                width: 87%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ $enseignant->quality ?? 'ENSEIGNANT' }}

            </td>

        </tr>

        <tr>

            <td class="label-en">
                As:
            </td>

            <td></td>

        </tr>

    </table>


    {{-- =========================================================
         DIPLÔME / SPÉCIALITÉ
    ========================================================== --}}
    <table>

        <tr>

            <td style="width: 10%;" class="label-fr">
                Diplôme:
            </td>

            <td style="
                width: 38%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ $enseignant->diploma ?? '' }}

            </td>

            <td style="width: 2%;"></td>

            <td style="width: 11%;" class="label-fr">
                Spécialité:
            </td>

            <td style="
                width: 39%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ $enseignant->matiere->libelle ?? '' }}

            </td>

        </tr>

        <tr>

            <td class="label-en">
                Certificate:
            </td>

            <td></td>

            <td></td>

            <td class="label-en">
                Specialty:
            </td>

            <td></td>

        </tr>

    </table>


    {{-- =========================================================
         DATE DE PREMIÈRE PRISE DE SERVICE DANS L'ADMINISTRATION
    ========================================================== --}}
    {{-- <table>

        <tr>

            <td style="width: 56%;" class="label-fr">

                Date de la première prise de service
                dans l'administration:

            </td>

            <td style="
                width: 44%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ isset($enseignant->public_service_first_date) && $enseignant->public_service_first_date
                    ? \Carbon\Carbon::parse($enseignant->public_service_first_date)->format('d/m/Y')
                    : '' }}

            </td>

        </tr>

        <tr>

            <td class="label-en">

                Date of first assumption of duty
                in the public service:

            </td>

            <td></td>

        </tr>

    </table> --}}


    {{-- =========================================================
         DATE DE PREMIÈRE PRISE DE SERVICE DANS L'ÉTABLISSEMENT
    ========================================================== --}}
    {{-- <table>

        <tr>

            <td style="width: 54%;" class="label-fr">

                Date de première prise de service
                dans l'établissement:

            </td>

            <td style="
                width: 46%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ isset($enseignant->school_first_date) && $enseignant->school_first_date
                    ? \Carbon\Carbon::parse($enseignant->school_first_date)->format('d/m/Y')
                    : '' }}

            </td>

        </tr>

        <tr>

            <td class="label-en">

                Date of first assumption of duty
                in the school:

            </td>

            <td></td>

        </tr>

    </table> --}}


    {{-- =========================================================
         MATIÈRE ENSEIGNÉE
    ========================================================== --}}
    <table style="margin-bottom: 12px;">

        <tr>

            <td style="width: 19%;" class="label-fr">
                Matière enseignée :
            </td>

            <td style="
                width: 81%;
                text-align: center;
            "
                class="line-underline val-text">

                {{ $enseignant->matiere->libelle ?? '' }}

            </td>

        </tr>

        <tr>

            <td class="label-en">
                Taught subject :
            </td>

            <td></td>

        </tr>

    </table>


    {{-- =========================================================
         FORMULE ADMINISTRATIVE
    ========================================================== --}}
    <div style="
        margin-top: 6px;
        margin-bottom: 20px;
    ">

        <div class="label-fr">

            En foi de quoi, le présent certificat lui est délivré
            pour servir et valoir ce que de droit.

        </div>

        <div class="label-en">

            In testimony whereof, this certificate is issued
            to the bearer to serve the purpose for which it is intended.

        </div>

    </div>


    {{-- =========================================================
         SIGNATURE
    ========================================================== --}}
    <table style="margin-top: 15px;">

        <tr>

            <td style="
                width: 45%;
                vertical-align: top;
            ">

                <div class="label-fr">
                    (1) Rayer la mention inutile
                </div>

                <div class="label-en">
                    Cancel out the unapplied
                </div>

            </td>


            <td
                style="
                width: 55%;
                text-align: center;
                vertical-align: top;
            ">

                <table>

                    <tr>

                        <td style="
                            width: 20%;
                            text-align: right;
                        "
                            class="label-fr">

                            Fait à

                        </td>

                        <td style="
                            width: 35%;
                            text-align: center;
                        "
                            class="line-underline val-text">

                            {{ $etablissement->adresse }}

                        </td>

                        <td style="
                            width: 10%;
                            text-align: center;
                        "
                            class="label-fr">

                            Le

                        </td>

                        <td style="
                            width: 35%;
                        "
                            class="line-underline">

                            &nbsp;

                        </td>

                    </tr>

                    <tr>

                        <td style="text-align: right;" class="label-en">
                            Done at
                        </td>

                        <td></td>

                        <td style="text-align: center;" class="label-en">
                            on
                        </td>

                        <td></td>

                    </tr>

                </table>


                <div style="margin-top: 15px;" class="label-fr">

                    Le PROVISEUR<br>

                    <span class="label-en" style="font-weight: normal;">
                        The PRINCIPAL
                    </span>

                </div>

            </td>

        </tr>

    </table>


    {{-- =========================================================
         PIED DE PAGE TECHNIQUE
    ========================================================== --}}
    <div class="footer-divider">

        AcademiaPro 2026 |
        AcademiaPro HIGH SCHOOL |
        Email: academiapro237@gmail.com |
        (+237) 679 13 51 77 / 689 58 72 79 / 694 22 35 03 / 675 06 60 01
        

        | Page 1/1

    </div>

</body>

</html>
