<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
      /**
     * Enregistrer un avis ou une suggestion.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        Feedback::create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        return back()->with(
            'success',
            'Merci ! Votre avis a été envoyé avec succès.'
        );
    }
}
