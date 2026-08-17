<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
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
    // public function update(ProfileUpdateRequest $request): RedirectResponse
    // {
    //     $request->user()->fill($request->validated());

    //     if ($request->user()->isDirty('email')) {
    //         $request->user()->email_verified_at = null;
    //     }

    //     // Gestion de l'upload de la photo de profil
    //     if ($request->hasFile('avatar')) {
    //         // Supprimer l'ancienne image si elle existe déjà pour libérer de l'espace
    //         if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
    //             Storage::disk('public')->delete($user->avatar);
    //         }

    //         // Enregistrer la nouvelle image dans storage/app/public/avatars
    //         $path = $request->file('avatar')->store('avatars', 'public');
    //         $user->avatar = $path;
    //     }


    //     $request->user()->save();

    //     return Redirect::route('profile.edit')->with('status', 'profile-updated');
    // }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
    //     dd(
    //     $request->all(), 
    //     $request->hasFile('avatar'), 
    //     $request->file('avatar')
    // );
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
        if ($request->hasFile('avatar')) {
            // Supprimer l'ancienne image si elle existe
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Enregistrer la nouvelle image et récupérer le chemin textuel
            $path = $request->file('avatar')->store('avatars', 'public');

            // Assigner le chemin textuel au modèle
            $user->avatar = $path;
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
