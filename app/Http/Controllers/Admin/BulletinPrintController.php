<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulletinPrintController extends Controller
{
  // 1. Affichage de la Grille des Classes
    public function index(Request $request)
    {
        // On récupère l'année active (via ta logique existante)
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

        if (!$anneeActive) {
            abort(500, "Aucune année scolaire active configurée.");
        }

        // Récupérer tous les trimestres pour le menu déroulant
        $trimestres = DB::table('trimestres')
            ->where('annee_scolaire_id', $anneeActive->id)
            ->get();

        // Récupérer le trimestre sélectionné ou prendre le premier par défaut
        $trimestreId = $request->get('trimestre_id') ?? ($trimestres->first()->id ?? null);

        // Récupérer toutes les classes avec le nombre d'élèves inscrits pour cette année
        $classes = DB::table('classes')
            ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
            ->leftJoin('inscriptions', function($join) use ($anneeActive) {
                $join->on('inscriptions.classe_id', '=', 'classes.id')
                     ->where('inscriptions.annee_scolaire_id', '=', $anneeActive->id);
            })
            ->select(
                'classes.id',
                'niveaux.nom as niveau_nom',
                'classes.nom as classe_nom',
                DB::raw('COUNT(inscriptions.id) as total_eleves')
            )
            ->groupBy('classes.id', 'niveaux.nom', 'classes.nom')
            ->orderBy('niveaux.nom', 'desc')
            ->get();

        return view('pages.admin.bulletins.index', compact('classes', 'trimestres', 'trimestreId'));
    }

    // 2. Affichage du Hub d'une classe (La liste des élèves)
    public function classeHub($classeId, Request $request)
    {
        $trimestreId = $request->get('trimestre_id');
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

        // Informations de la classe
        $classe = DB::table('classes')
            ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
            ->where('classes.id', $classeId)
            ->select('classes.id', DB::raw("CONCAT(niveaux.nom, ' ', classes.nom) as nom"))
            ->first();

        // Liste des élèves inscrits dans cette classe
        $eleves = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('inscriptions.annee_scolaire_id', $anneeActive->id)
            ->select('inscriptions.id as inscription_id', 'eleves.nom', 'eleves.prenom', 'eleves.matricule')
            ->orderBy('eleves.nom', 'asc')
            ->get();

        return view('pages.admin.bulletins.classe-hub', compact('classe', 'eleves', 'trimestreId'));
    }
}
