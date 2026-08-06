<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    private function getEnumRoles(): array
    {
        $type = DB::select("SHOW COLUMNS FROM users WHERE Field = 'role'")[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        // dd($type);

        return array_map(function ($value) {
            return trim($value, "'");
        }, explode(',', $matches[1]));
    }
    public function index(Request $request)
    {
        $query = User::query();

        // Barre de recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

    $users = $query->orderBy('name', 'asc')
               ->orderBy('created_at', 'desc')
               ->paginate(10)
               ->withQueryString();

        // Récupération dynamique des rôles
        $roles = $this->getEnumRoles();

        return view('pages.users.index', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $rolesDisponibles = $this->getEnumRoles();

        $request->validate([
            'role' => 'required|in:' . implode(',', $rolesDisponibles),
        ]);

        // Mise à jour de la base de données
        $user->update([
            'role' => $request->role
        ]);

        return redirect()->back()->with('success', 'Rôle mis à jour avec succès.');
    }

    public function destroy(User $user)
{
    // Sécurité : Empêcher de supprimer l'administrateur courant ou soi-même si besoin
    if ($user->id === auth()->id()) {
        return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
    }

    $user->delete();

    return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé avec succès.');
}
}
