# 🏫 Educational Management System

Système complet de gestion d'établissements scolaires - Gestion des élèves, enseignants, classes, matières, emplois du temps, évaluations et notes.

[![Laravel](https://img.shields.io/badge/Laravel-13.0-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php)](https://www.php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](LICENSE)

---

## 📋 Table des matières

- [Aperçu](#aperçu)
- [Fonctionnalités](#fonctionnalités)
- [Technologies](#technologies)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Structure du projet](#structure-du-projet)
- [Utilisation](#utilisation)
- [API](#api)
- [Base de données](#base-de-données)
- [Contribution](#contribution)
- [Licence](#licence)

---

## 🎯 Aperçu

**Educational Management System** est une plateforme web complète conçue pour simplifier et automatiser la gestion administrative d'établissements scolaires. Le système offre une solution intégrée pour gérer les inscriptions, les affectations de classe, les emplois du temps, les évaluations, et le suivi académique des élèves.

Idéal pour :

- **Écoles** : Gestion centralisée des données académiques
- **Établissements publics et privés** : Support complet des cycles et niveaux
- **Administrations scolaires** : Reporting et statistiques détaillées
- **Enseignants** : Interface intuitive pour la gestion des évaluations

---

## ✨ Fonctionnalités

### 👥 Gestion des Utilisateurs

- **Rôles multiples** : Admin, Enseignants, Secrétaires
- **Authentification sécurisée** : Laravel Breeze
- **Gestion des profils utilisateur**
- **Contrôle d'accès basé sur les rôles (RBAC)**

### 🎓 Gestion Académique

- **Cycles et Niveaux** : Gestion des cycles scolaires (Premier Cycle, Second Cycle, etc.)
- **Classes** : Organisation des salles et sections
- **Années scolaires** : Gestion des périodes académiques avec dates
- **Matières/Courses** : Catalogue complet des cours enseignés

### 👨‍🎓 Gestion des Élèves

- **Inscriptions** : Enregistrement et suivi des élèves
- **Profils détaillés** : Informations personnelles et académiques
- **Historique** : Suivi des parcours scolaires
- **Import/Export** : Importation en masse via Excel

### 👨‍🏫 Gestion des Enseignants

- **Affectations** : Assignation aux classes et matières
- **Emplois du temps** : Gestion des créneaux horaires
- **Spécialités** : Classification par domaine d'enseignement
- **Export de données** : Génération de rapports

### 📊 Évaluations et Évaluation

- **Gestion des notes** : Enregistrement des évaluations
- **Moyennes** : Calcul automatique des performances
- **Bilans académiques** : Suivi du progrès des élèves
- **Génération de rapports PDF** : Documentation académique

### 📅 Emploi du Temps

- **Créneaux horaires** : Gestion des horaires de cours
- **Jours de semaine** : Organisation hebdomadaire
- **Assignation** : Liaison avec les classes et enseignants

### 📈 Données et Rapports

- **Export Excel** : Données académiques et administratives
- **Génération PDF** : Bulletins et certificats
- **Analytics** : Statistiques et tableaux de bord
- **Import de données** : Intégration par lot

---

## 🔧 Technologies

| Composant                   | Technologie       | Version |
| --------------------------- | ----------------- | ------- |
| **Framework Backend**       | Laravel           | 13.0    |
| **Langage**                 | PHP               | 8.3+    |
| **Base de données**         | MySQL/MariaDB     | 5.7+    |
| **Frontend Framework**      | Vue.js + Vite     | Moderne |
| **Styling**                 | Tailwind CSS      | Latest  |
| **Templating**              | Blade             | Laravel |
| **Export Excel**            | Maatwebsite/Excel | 3.1     |
| **Génération PDF**          | DOMPDF            | 3.1     |
| **Interactions dynamiques** | Hotwired Turbo    | 2.6     |
| **Icônes**                  | Lucide Icons      | Latest  |
| **Testing**                 | Pest              | 4.4     |
| **Code Quality**            | Pint              | 1.27    |
| **Debugging**               | Debugbar          | 4.1     |

---

## 📋 Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés :

- **PHP** >= 8.3
- **Composer** >= 2.0
- **Node.js** >= 16.0
- **npm** >= 8.0 ou **yarn**
- **MySQL/MariaDB** >= 5.7
- **Git**

### Vérification des prérequis

```bash
# Vérifier PHP
php -v

# Vérifier Composer
composer -v

# Vérifier Node.js
node -v
npm -v

# Vérifier MySQL
mysql -v
```

---

## 🚀 Installation

### 1. Cloner le repository

```bash
git clone https://github.com/yourusername/educational-management.git
cd educational-management
```

### 2. Installation automatique (recommandée)

```bash
composer run-script setup
```

Cette commande effectue automatiquement :

- Installation des dépendances PHP
- Génération du fichier `.env`
- Génération de la clé d'application
- Migration de la base de données
- Installation des dépendances Node.js

### 3. Installation manuelle

Si vous préférez une installation étape par étape :

#### 3.1 Installer les dépendances PHP

```bash
composer install
```

#### 3.2 Configurer l'environnement

```bash
# Copier le fichier d'exemple
cp .env.example .env

# Générer la clé d'application
php artisan key:generate
```

#### 3.3 Configurer la base de données

Éditer le fichier `.env` et configurer les variables :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=educational_system
DB_USERNAME=root
DB_PASSWORD=
```

#### 3.4 Migrer la base de données

```bash
# Exécuter les migrations
php artisan migrate

# (Optionnel) Remplir la base de données avec des données de test
php artisan db:seed
```

#### 3.5 Installer les dépendances Node.js

```bash
npm install
```

#### 3.6 Compiler les assets

```bash
# Mode développement
npm run dev

# Mode production
npm run build
```

### 4. Démarrer le serveur de développement

```bash
php artisan serve
```

L'application sera accessible à : `http://localhost:8000`

---

## ⚙️ Configuration

### Variables d'environnement

Les principales variables à configurer dans `.env` :

```env
# Application
APP_NAME="Educational Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=educational_system
DB_USERNAME=root
DB_PASSWORD=

# Mail (optionnel)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=no-reply@example.com

# Cache et Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### Configuration de la base de données

Modifier `config/database.php` pour ajuster les paramètres de connexion MySQL.

### Configuration du mail

Pour activer les notifications par email, configurer un service SMTP dans `.env`.

---

## 📁 Structure du projet

```
educational-management/
├── app/
│   ├── Enums/                      # Énumérations (rôles, statuts)
│   ├── Exports/                    # Classes d'export Excel
│   │   ├── DepartmentExport.php
│   │   ├── InscriptionExport.php
│   │   ├── StudentExport.php
│   │   ├── TeachersExport.php
│   │   └── UsersExport.php
│   ├── Http/
│   │   ├── Controllers/            # Contrôleurs
│   │   ├── Middleware/             # Middlewares
│   │   └── Requests/               # Form Requests / Validations
│   ├── Imports/                    # Classes d'import Excel
│   │   └── StudentImport.php
│   ├── Models/                     # Modèles Eloquent
│   │   ├── Affectation.php         # Assignation enseignant-classe-matière
│   │   ├── AnneeScolaire.php       # Années scolaires
│   │   ├── Bilan.php               # Bilans académiques
│   │   ├── Classe.php              # Classes
│   │   ├── Creneau.php             # Créneaux horaires
│   │   ├── Cycle.php               # Cycles scolaires
│   │   ├── Departement.php         # Départements
│   │   ├── Eleve.php               # Élèves
│   │   ├── Enseignant.php          # Enseignants
│   │   ├── Etablissement.php       # Établissements
│   │   ├── Evaluation.php          # Évaluations
│   │   ├── GroupeMatiere.php       # Groupes de matières
│   │   ├── Inscription.php         # Inscriptions
│   │   ├── Jour.php                # Jours de semaine
│   │   ├── Lecon.php               # Leçons/Cours
│   │   ├── Matiere.php             # Matières
│   │   ├── Moyenne.php             # Calcul des moyennes
│   │   ├── Note.php                # Notes des élèves
│   │   └── ...
│   ├── Providers/                  # Service Providers
│   ├── Services/                   # Classes de service métier
│   └── View/                       # View Composers
├── bootstrap/
│   ├── app.php                     # Bootstrap de l'application
│   ├── cache/                      # Cache du framework
│   └── providers.php
├── config/                         # Fichiers de configuration
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── excel.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   └── ...
├── database/
│   ├── factories/                  # Factories pour les tests
│   ├── migrations/                 # Migrations de schéma
│   └── seeders/                    # Seeders pour les données
├── public/
│   ├── assets/                     # Ressources publiques
│   ├── build/                      # Assets compilés
│   ├── images/                     # Galerie d'images
│   ├── index.php                   # Point d'entrée
│   └── ...
├── resources/
│   ├── css/                        # Feuilles de style
│   ├── js/                         # Fichiers JavaScript
│   └── views/                      # Vues Blade
├── routes/
│   ├── api.php                     # Routes API
│   ├── auth.php                    # Routes d'authentification
│   ├── console.php                 # Commandes console
│   └── web.php                     # Routes web
├── storage/
│   ├── app/                        # Fichiers uploadés
│   ├── framework/                  # Cache et sessions
│   └── logs/                       # Fichiers de log
├── tests/
│   ├── Feature/                    # Tests de fonctionnalités
│   └── Unit/                       # Tests unitaires
├── .env.example                    # Exemple de fichier d'environnement
├── artisan                         # CLI Laravel
├── composer.json                   # Dépendances PHP
├── package.json                    # Dépendances Node.js
├── postcss.config.js               # Configuration PostCSS
├── tailwind.config.js              # Configuration Tailwind CSS
├── vite.config.js                  # Configuration Vite
├── phpunit.xml                     # Configuration PHPUnit
└── README.md                       # Ce fichier
```

---

## 💻 Utilisation

### Authentification

1. Accédez à `http://localhost:8000/login`
2. Utilisez les identifiants fournis lors de la création des utilisateurs via seeders
3. Selon votre rôle (Admin, Enseignant, Secrétaire), vous accéderez à différentes sections

### Gestion des Établissements

**Admin only** - Gérer les établissements scolaires et leurs paramètres

```
Dashboard → Établissements → Ajouter/Modifier
```

### Gestion des Années Scolaires

**Admin** - Créer et gérer les années scolaires

```
Dashboard → Configuration → Années Scolaires
```

### Gestion des Classes

**Admin/Secrétaire** - Organiser les classes par cycle et niveau

```
Dashboard → Académique → Classes
```

### Enregistrement des Élèves

**Admin/Secrétaire** - Inscrire de nouveaux élèves

```
Dashboard → Élèves → Ajouter une Inscription
```

### Gestion des Enseignants

**Admin** - Affecter les enseignants aux classes et matières

```
Dashboard → Enseignants → Affectations
```

### Saisie des Notes

**Enseignants** - Enregistrer les évaluations et notes

```
Dashboard → Mes Classes → Saisie des Notes
```

### Génération de Rapports

**Admin/Secrétaire** - Exporter les données et générer les bulletins

```
Dashboard → Rapports → Exporter
```

---

## 🔌 API

L'application expose plusieurs endpoints API pour l'intégration tierce.

### Authentification API

```bash
# Obtenir un token (si utilisation de Sanctum)
POST /api/login
{
  "email": "user@example.com",
  "password": "password"
}
```

### Endpoints principaux

```
GET    /api/classes              # Lister les classes
GET    /api/classes/{id}         # Détails d'une classe
POST   /api/classes              # Créer une classe
PUT    /api/classes/{id}         # Modifier une classe
DELETE /api/classes/{id}         # Supprimer une classe

GET    /api/eleves               # Lister les élèves
GET    /api/eleves/{id}          # Détails d'un élève
POST   /api/eleves               # Créer un élève

GET    /api/enseignants          # Lister les enseignants
GET    /api/notes                # Récupérer les notes
POST   /api/notes                # Enregistrer une note
```

**Note** : Consultez la documentation API complète dans `docs/api.md` pour tous les endpoints disponibles.

---

## 🗄️ Base de données

### Schéma principal

Le système utilise une base de données relationnelle avec 21+ tables interconnectées :

#### Entités principales

- **users** : Utilisateurs du système
- **etablissements** : Établissements scolaires
- **annee_scolaires** : Années scolaires
- **cycles** : Cycles d'enseignement
- **niveaux** : Niveaux académiques
- **classes** : Classes/Sections
- **salles** : Salles de classe
- **eleves** : Étudiants
- **enseignants** : Professeurs
- **matieres** : Matières enseignées
- **inscriptions** : Enregistrements des élèves
- **affectations** : Assignations enseignant-classe
- **creeneaux** : Horaires de cours
- **evaluations** : Évaluations académiques
- **notes** : Notes des élèves
- **moyennes** : Moyennes calculées
- **bilans** : Bilans académiques

Pour plus de détails, consulter [DATABASE_ANALYSIS.md](DATABASE_ANALYSIS.md) et [DATABASE_SCHEMA.json](DATABASE_SCHEMA.json).

### Migrations

Toutes les migrations sont dans `database/migrations/`. Pour ajouter une nouvelle table :

```bash
php artisan make:migration create_[table_name]_table
```

---

## 🧪 Tests

### Lancer les tests

```bash
# Tous les tests
php artisan test

# Tests spécifiques
php artisan test tests/Feature/AuthTest.php

# Avec couverture de code
php artisan test --coverage
```

### Écrire des tests

Tests avec Pest dans `tests/Feature/` et `tests/Unit/`.

```php
// tests/Feature/ClasseTest.php
test('can create a classe', function () {
    $response = $this->post('/api/classes', [
        'nom' => 'Classe 6ème M1',
        'niveau_id' => 1,
    ]);

    $response->assertCreated();
});
```

---

## 🎨 Développement frontend

### Compiler les assets

```bash
# Mode développement avec rechargement automatique
npm run dev

# Build pour la production
npm run build

# Linter le code JavaScript
npm run lint
```

### Architecture frontend

- **Vue.js** pour les composants interactifs
- **Tailwind CSS** pour le styling
- **Blade** pour les templates serveur
- **Hotwired Turbo** pour les interactions sans rechargement

---

## 📝 Commandes utiles

```bash
# Afficher les routes disponibles
php artisan route:list

# Accéder à la console PHP interactive
php artisan tinker

# Formater le code PHP
composer run-script lint

# Générer la documentation
php artisan ide-helper:generate

# Vider les caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Créer un utilisateur admin
php artisan make:user --admin

# Exporter la configuration
php artisan config:cache
```

---

## 🔒 Sécurité

### Bonnes pratiques

- ✅ CSRF Protection activée par défaut
- ✅ Validation d'entrée sur tous les formulaires
- ✅ Hachage des mots de passe avec bcrypt
- ✅ Authentification et autorisation via Middleware
- ✅ Variables sensibles dans `.env` (non commitées)
- ✅ SQL Injection prévenue via Eloquent ORM

### Mise à jour de sécurité

```bash
# Vérifier les vulnérabilités connues
composer audit

# Mettre à jour les dépendances
composer update
npm update
```

---

## 📦 Déploiement

### Préparation pour la production

```bash
# Installer les dépendances en mode production
composer install --no-dev --optimize-autoloader

# Build des assets
npm run build

# Optimiser l'application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Générer une clé app unique
php artisan key:generate --force
```

### Déployer sur un serveur

1. Cloner le repository
2. Exécuter les préparations production
3. Configurer `.env` pour la production
4. Migrer la base de données
5. Configurer les permissions des répertoires
6. Configurer le web server (Nginx/Apache)

### Hébergement recommandé

- **VPS** : DigitalOcean, Linode, AWS EC2
- **Hébergement partagé** : Hostinger (si PHP 8.3+ supporté)
- **Platform as a Service** : Heroku, Railway

---

## 🤝 Contribution

Les contributions sont bienvenues ! Pour contribuer :

1. **Fork** le repository
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commiter vos changements (`git commit -m 'Add AmazingFeature'`)
4. Pousser vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une **Pull Request**

### Guidelines

- Suivre le style de code du projet (Pint)
- Ajouter des tests pour les nouvelles fonctionnalités
- Mettre à jour la documentation
- Utiliser des commits descriptifs

---

## 📞 Support

- **Issues GitHub** : [Ouvrir une issue](https://github.com/yourusername/educational-management/issues)
- **Discussions** : [Rejoindre les discussions](https://github.com/yourusername/educational-management/discussions)
- **Email** : support@example.com

---

## 📜 Licence

Ce projet est sous licence **MIT**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 🙏 Remerciements

Merci à :

- **Laravel Team** pour l'excellent framework
- **Maatwebsite** pour Excel integration
- **DOMPDF** pour la génération de PDF
- **Tailwind CSS** pour le framework CSS
- **Tous les contributeurs** du projet

---

## 📅 Changelog

Voir [CHANGELOG.md](CHANGELOG.md) pour l'historique des versions et mises à jour.

---

**Développé avec ❤️ pour les établissements scolaires**

_Dernière mise à jour : 2026_
