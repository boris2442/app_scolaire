<?php

namespace App\Exceptions;

use Exception;

class IncoherentScolariteException extends Exception
{
   public function render($request)
    {
        // Redirige sur la page précédente avec le message d'erreur pour Blade
        return back()->with('error', $this->getMessage() ?: 'Données scolaires incohérentes.');
    }
}
