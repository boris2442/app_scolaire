<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case ENSEIGNANT = 'enseignant';
    case SECRETAIRE = 'secretaire'; // Le nouveau rôle
    case PARENT = 'parent';
    case ELEVE = 'eleve';
}
