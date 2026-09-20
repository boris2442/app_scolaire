<?php

namespace App\Http\Controllers;


use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index($subjectId, $classRoomId)
    {
        $userId = auth()->id();

        // Récupérer les leçons existantes en utilisant directement $subjectId et $classRoomId
        $lessons = Lesson::where('enseignant_id', $userId)
            ->where('matiere_id', $subjectId)
            ->where('classe_id', $classRoomId)
            ->orderBy('ordre')
            ->get();

        // On passe les variables attendues par la vue
        return view('pages.lessons.index', compact('lessons', 'subjectId', 'classRoomId'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'matiere_id' => 'required',
            'classe_id' => 'required',
        ]);

        Lesson::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'matiere_id' => $request->matiere_id,
            'classe_id' => $request->classe_id,
            'enseignant_id' => auth()->id(),
            'ordre' => $request->ordre ?? 1,
        ]);

        return back()->with('success', 'Leçon ajoutée avec succès au programme !');
    }
}
