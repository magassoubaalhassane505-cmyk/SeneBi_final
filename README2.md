# SeneBI — Architecture Technique et Guide du Code

Ce document détaille la structure technique du code de la plateforme **SeneBI**, le fonctionnement de ses contrôleurs, la gestion des sessions, des middlewares, ainsi que la configuration Docker/Vite pour le déploiement sur les plateformes de cloud (ex: Render).

---

## 🏛️ Architecture Globale du Projet

L'application est construite sur le framework **Laravel 11+** (PHP 8.3+) en utilisant une architecture MVC (Modèle-Vue-Contrôleur) classique, couplée à des APIs JSON pour alimenter les graphiques dynamiques du front-end.

```
📂 app/
  📂 Http/
    📂 Controllers/        # Contrôleurs principaux de l'application
    📂 Middleware/         # Middlewares de sécurité et de filtrage
  📂 Models/               # Modèles Eloquent (User, Parcelle, Recolte, Stock, etc.)
  📂 Providers/            # Providers de services (ex: AppServiceProvider)
📂 bootstrap/
  📄 app.php               # Configuration des middlewares et routage global (Laravel 11+)
📂 config/                 # Fichiers de configuration de l'application
📂 database/
  📂 migrations/           # Schéma de base de données MySQL
  📂 seeders/              # Remplissage des données de test et BI
📂 public/
  📂 assets/               # Fichiers CSS et JS statiques de l'application
  📂 build/                # Fichiers CSS et JS compilés par Vite pour la production
📂 resources/
  📂 views/                # Vues Blade (mises en page et fragments HTML)
📂 routes/
  📄 web.php               # Définition de toutes les routes de l'application
```

---

## ⚙️ Détail des Contrôleurs Principaux

### 1. [RegisterController.php](file:///c:/wamp64/www/SeneBi_final/app/Http/Controllers/RegisterController.php)
Gère l'enregistrement des agriculteurs.
* **`store(Request $request)`** : Valide les entrées utilisateur (email unique, mot de passe fort). Il crée le compte avec les valeurs par défaut : `role => 'client'`, `status => 'pending'`, et `is_active => false`. Il déclenche également une notification globale destinée au manager.

### 2. [AuthController.php](file:///c:/wamp64/www/SeneBi_final/app/Http/Controllers/AuthController.php)
Gère le cycle de vie de la session utilisateur.
* **`login(Request $request)`** : Point d'accès unique pour la connexion. Si les identifiants sont corrects, il vérifie le rôle de l'utilisateur :
  * Si l'utilisateur est un **manager**, il est redirigé vers `/manager/dashboard`.
  * Si l'utilisateur est un **client**, il vérifie que son statut est `approved` et que `is_active` est à `true`. S'il est en attente (`pending`), la session est déconnectée et un message d'erreur est renvoyé.

### 3. [ManagementController.php](file:///c:/wamp64/www/SeneBi_final/app/Http/Controllers/ManagementController.php)
Contrôleur central pour les fonctionnalités d'administration du manager.
* **`dashboard()`** : Calcule les métriques globales de la saison en cours (bénéfice total, nombre d'agriculteurs, alertes critiques).
* **`approveClient(User $user)`** & **`rejectClient(User $user)`** : Permettent de modifier l'état d'approbation d'un agriculteur, de lui attribuer un manager référent, de dater l'action et d'envoyer des notifications internes.
* **`destroyUser(User $user)`** : Supprime définitivement un utilisateur de la base de données via `forceDelete()` (les suppressions en cascade sont gérées au niveau de la base).
* **Endpoints API BI (`*Api*`)** : Fournissent des réponses JSON formatées pour les bibliothèques de graphiques (ex: répartition des parcelles par région, rendement moyen par type de culture, bénéfices financiers).

### 4. [ClientController.php](file:///c:/wamp64/www/SeneBi_final/app/Http/Controllers/ClientController.php)
Gère l'affichage des pages de l'espace agriculteur (tableau de bord personnel, vue des parcelles, gestion des stocks, simulateur de rentabilité).

### 5. [ClientApiController.php](file:///c:/wamp64/www/SeneBi_final/app/Http/Controllers/ClientApiController.php)
Gère les opérations CRUD (Créer, Lire, Mettre à jour, Supprimer) effectuées en AJAX par l'agriculteur (ex. ajouter une parcelle, modifier un stock d'intrant, ajouter un mouvement de stock). Les transactions critiques y sont sécurisées via `DB::transaction()`.

---

## 🔒 Middlewares et Sécurité

Le filtrage des routes se fait de manière moderne dans Laravel 11/13 via des closures ou des middlewares enregistrés dans `bootstrap/app.php` :

* **Vérification du rôle Manager :**
  Les routes sous le préfixe `/manager/*` sont protégées par le middleware standard de vérification de rôle administrateur/manager. Un utilisateur ayant le rôle `client` tentant d'y accéder recevra une erreur `403 Forbidden`.
* **Vérification du statut Client :**
  Les routes sous le préfixe `/client/*` exigent que l'utilisateur connecté possède le rôle `client`, et qu'il ait préalablement été approuvé par un manager.

---

## 🌐 Déploiement et Reverse Proxy (Render & Docker)

Lors du déploiement de l'application sur un hébergeur comme **Render**, les requêtes des clients arrivent en HTTPS sur le serveur de Render, qui les transmet ensuite en HTTP à notre conteneur Docker. Pour éviter les bugs d'affichage (ressources CSS/JS bloquées car chargées en HTTP) :

### 1. Configuration des Proxies de Confiance
Dans le fichier **[bootstrap/app.php](file:///c:/wamp64/www/SeneBi_final/bootstrap/app.php)**, nous faisons confiance à tous les proxies amonts :
```php
$middleware->trustProxies(at: '*');
```
Cela permet à Laravel de lire l'en-tête standard `X-Forwarded-Proto` fourni par Render et de comprendre que la requête initiale de l'utilisateur était sécurisée.

### 2. Forçage de l'HTTPS pour les Assets
Dans le fichier **[AppServiceProvider.php](file:///c:/wamp64/www/SeneBi_final/app/Providers/AppServiceProvider.php)**, nous forçons la génération de toutes les URLs d'assets (via le helper `asset()`) en HTTPS si nous détectons que l'application tourne dans un environnement de production ou derrière le reverse proxy de Render :
```php
if ($this->app->environment('production') || 
    env('APP_ENV') === 'production' || 
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'onrender.com'))) {
    \Illuminate\Support\Facades\URL::forceScheme('https');
}
```

### 3. Conteneurisation (Dockerfile)
Le **[Dockerfile](file:///c:/wamp64/www/SeneBi_final/Dockerfile)** utilise un processus de construction en deux étapes (multi-stage) :
1. **Étape Node.js :** Installe les paquets NPM et compile les ressources front-end via Vite (`npm run build`).
2. **Étape PHP-Apache :** Installe PHP 8.3 avec Apache, installe les extensions système et PHP indispensables (`gd` pour les images, `pdo_mysql` pour la base de données), copie le code de l'application et les fichiers compilés par Vite, configure l'hôte virtuel Apache (`public/` comme racine), et lance le script d'entrée **[docker/entrypoint.sh](file:///c:/wamp64/www/SeneBi_final/docker/entrypoint.sh)** pour configurer dynamiquement le port réseau de Render (`$PORT`).
