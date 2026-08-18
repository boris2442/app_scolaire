<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PresenceAndServiceController extends Controller
{
    public function generateAttestationPresence($id)
    {
        $etablissement = Etablissement::first(); // Récupérer le premier établissement
        // Récupérer l'utilisateur et sa relation enseignant
        $user = User::with('enseignant.matiere')->findOrFail($id);
        $enseignant = $user->enseignant;

        // Charger la vue PDF avec les données
        $pdf = Pdf::loadView('pages.presence-and-service.attestation-presence', compact('user', 'enseignant', 'etablissement'));

        // Configurer le format de la page en A4 Portrait
        $pdf->setPaper('a4', 'portrait');

        // Télécharger automatiquement le fichier PDF
        return $pdf->download('Presence_' . $user->name . '.pdf');
    }



     /**
     * Générer l'attestation de prise de service
     */
    public function generateAttestationPriseService($id)
    {
        // Récupérer l'établissement
        $etablissement = Etablissement::first();

        // Récupérer l'utilisateur avec son enseignant et sa matière
        $user = User::with('enseignant.matiere')->findOrFail($id);

        $enseignant = $user->enseignant;

        // Charger la vue PDF de prise de service
        $pdf = Pdf::loadView(
            'pages.presence-and-service.prise-service',
            compact(
                'user',
                'enseignant',
                'etablissement'
            )
        );

        // Format A4 Portrait
        $pdf->setPaper('a4', 'portrait');

        // Télécharger le PDF
        return $pdf->download(
            'Prise_Service_' . $user->name . '.pdf'
        );
    }









     /**
     * Attestation de reprise de service
     */
    public function generateAttestationRepriseService($id)
    {
        // Récupérer l'établissement
        $etablissement = Etablissement::first();

        // Récupérer l'utilisateur et son enseignant
        // avec la matière
        $user = User::with('enseignant.matiere')->findOrFail($id);

        $enseignant = $user->enseignant;

        // Générer le PDF à partir du template
        $pdf = Pdf::loadView(
            'pages.presence-and-service.reprise-service',
            compact(
                'user',
                'enseignant',
                'etablissement'
            )
        );

        // Format A4 Portrait
        $pdf->setPaper('a4', 'portrait');

        // Télécharger le document
        return $pdf->download(
            'Reprise_Service_' . $user->name . '.pdf'
        );
    }
}
