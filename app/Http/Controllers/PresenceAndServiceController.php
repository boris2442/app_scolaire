<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PresenceAndServiceController extends Controller
{
    public function generateAttestationPresence($id)
    {
        $etablissement= Etablissement::first(); // Récupérer le premier établissement
        // Récupérer l'utilisateur et sa relation enseignant
        $user = \App\Models\User::with('enseignant.matiere')->findOrFail($id);
        $enseignant = $user->enseignant;

        // Charger la vue PDF avec les données
        $pdf = Pdf::loadView('pages.presence-and-service.attestation-presence', compact('user', 'enseignant', 'etablissement'));

        // Configurer le format de la page en A4 Portrait
        $pdf->setPaper('a4', 'portrait');

        // Télécharger automatiquement le fichier PDF
        return $pdf->download('Attestation_Presence_' . $user->name . '.pdf');
    }
}
