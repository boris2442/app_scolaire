<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sequence;
use App\Services\ScolariteService;
use Illuminate\Http\Request;

class SequenceController extends Controller
{

    protected $scolarite;

    // Injection automatique du service via le constructeur
    public function __construct(ScolariteService $scolarite)
    {
        $this->scolarite = $scolarite;
    }
    public function index()
    {
        $anneeActive = $this->scolarite->getAnneeActive();

        $sequences = Sequence::whereHas('trimestre', function ($query) use ($anneeActive) {
            $query->where('annee_scolaire_id', $anneeActive->id);
        })->with('trimestre')->get();

        return view('pages.admin.sequences.index', compact('sequences', 'anneeActive'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'submission_deadline' => 'nullable|date',
            'is_closed' => 'nullable|boolean',
        ]);

        $sequence = Sequence::findOrFail($id);

        $sequence->update([
            'submission_deadline' => $request->submission_deadline,
            'is_closed' => $request->has('is_closed') ? true : false,
        ]);

        return back()->with('success', 'Paramètres de la séquence mis à jour !');
    }
}
