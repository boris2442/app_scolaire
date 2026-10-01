<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        $user = $request->user();

        // 1. Remplir les données validées (sauf l'avatar qu'on gère manuellement juste après)
        $data = $request->validated();

        // On retire l'avatar du tableau fill() direct pour éviter d'insérer l'objet brut
        unset($data['avatar']);

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 2. Traitement correct de l'image de profil
        // if ($request->hasFile('avatar')) {
        //     // Supprimer l'ancienne image si elle existe
        //     if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
        //         Storage::disk('public')->delete($user->avatar);
        //     }

        //     // Enregistrer la nouvelle image et récupérer le chemin textuel
        //     $path = $request->file('avatar')->store('avatars', 'public');

        //     // Assigner le chemin textuel au modèle
        //     $user->avatar = $path;
        // }

        // 2. Traitement correct de l'image de profil
        if ($request->hasFile('avatar')) {

            // Supprimer l'ancienne image si elle existe
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            // Créer le dossier public/avatars s'il n'existe pas
            $directory = public_path('avatars');

            if (! file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Générer un nom unique
            $filename = Str::uuid().'.'.
                $request->file('avatar')->getClientOriginalExtension();

            // Déplacer directement dans public/avatars
            $request->file('avatar')->move($directory, $filename);

            // Enregistrer le chemin dans le modèle
            $user->avatar = 'avatars/'.$filename;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
