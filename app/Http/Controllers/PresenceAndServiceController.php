<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PresenceAndServiceController extends Controller
{
    private function loadDataReport()
    {
        $user = Auth::user();

        $etablissement = Etablissement::first();

        $user->load('enseignant.matiere');

        $enseignant = $user->enseignant;

        return compact('enseignant', 'user', 'etablissement');

    }

    public function generateAttestationPresence()
    {
        $data = $this->loadDataReport();

        $pdf = Pdf::loadView(
            'pages.presence-and-service.attestation-presence',
            $data
        );

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Presence_effective'.$data['user']->name.'.pdf');
    }

    /**
     * Générer l'attestation de prise de service
     */
    public function generateAttestationPriseService()
    {
        $data = $this->loadDataReport();
        // Charger la vue PDF de prise de service
        $pdf = Pdf::loadView(
            'pages.presence-and-service.prise-service',
            $data
        );

        // Format A4 Portrait
        $pdf->setPaper('a4', 'portrait');

        // Télécharger le PDF
        return $pdf->download(
            'Prise_Service_'.$data['user']->name.'.pdf'
        );
    }

    /**
     * Attestation de reprise de service
     */
    public function generateAttestationRepriseService()
    {
        $data = $this->loadDataReport();
        // Générer le PDF à partir du template
        $pdf = Pdf::loadView(
            'pages.presence-and-service.reprise-service',
            $data
        );

        // Format A4 Portrait
        $pdf->setPaper('a4', 'portrait');

        // Télécharger le document
        return $pdf->download(
            'Reprise_Service_'.$data['user']->name.'.pdf'
        );
    }
}
