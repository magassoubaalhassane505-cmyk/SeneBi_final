# SeneBI — Database Challenge : Questions & Réponses SQL et Eloquent

Bienvenue dans le **Database Challenge SeneBI**. Ce document propose une série de questions techniques typiques pour l'analyse des données agricoles de la plateforme. Pour chaque question, vous trouverez la formulation du problème, la requête écrite en **PHP (Laravel Eloquent ORM)**, la requête **SQL brute** équivalente compilée en arrière-plan, et une explication technique d'optimisation.

---

## 🏆 Question 1 : Bénéfice net et quantité totale récoltée par culture en 2026
**Objectif :** Obtenir pour la saison 2026, la quantité totale récoltée (en kg) et le bénéfice net total généré pour chaque type de culture (Riz, Maïs, Coton).

### 💻 Solution Eloquent
```php
use App\Models\Recolte;
use Illuminate\Support\Facades\DB;

$results = Recolte::select('culture', 
        DB::raw('SUM(quantite) as total_recolte'), 
        DB::raw('SUM(benefice_net) as total_benefice')
    )
    ->where('saison', '2026')
    ->groupBy('culture')
    ->get();
```

### 🛢️ Solution SQL brute
```sql
SELECT 
    culture, 
    SUM(quantite) AS total_recolte, 
    SUM(benefice_net) AS total_benefice
FROM recoltes
WHERE saison = '2026'
GROUP BY culture;
```

### 💡 Explication et Optimisation
* **Indexation :** Pour accélérer cette requête sur une table volumineuse, il est recommandé de créer un **index composite** sur `(saison, culture)`. Cela permet au moteur MySQL de filtrer instantanément par saison et d'organiser le regroupement par culture sans faire un scan complet de la table.
* **Fonctions d'agrégation :** `SUM()` additionne les lignes regroupées par la clause `GROUP BY`.

---

## 🏆 Question 2 : Identifier les agriculteurs en alerte de stock critique
**Objectif :** Trouver la liste des agriculteurs (nom, email, entreprise) qui ont actuellement au moins un intrant en stock dont la quantité actuelle est inférieure ou égale au seuil critique.

### 💻 Solution Eloquent
```php
use App\Models\User;

$alertedFarmers = User::where('role', 'client')
    ->whereHas('stocks', function ($query) {
        $query->whereColumn('quantite_actuelle', '<=', 'seuil_critique');
    })
    ->select('id', 'name', 'email', 'company')
    ->get();
```

### 🛢️ Solution SQL brute
```sql
SELECT id, name, email, company
FROM users
WHERE role = 'client'
  AND EXISTS (
      SELECT 1 
      FROM stocks 
      WHERE stocks.user_id = users.id 
        AND stocks.quantite_actuelle <= stocks.seuil_critique
  );
```

### 💡 Explication et Optimisation
* **Performance :** L'utilisation de `whereHas` se traduit par une clause SQL `EXISTS`. C'est généralement beaucoup plus performant qu'un `JOIN` suivi d'un `DISTINCT` car le moteur de base de données s'arrête dès qu'il trouve la première correspondance dans la table `stocks`.
* **Indexation :** Assurez-vous d'avoir un index sur `stocks(user_id)` pour accélérer la sous-requête.

---

## 🏆 Question 3 : Rendement moyen par hectare pour le Riz dans la région de Sikasso
**Objectif :** Calculer le rendement moyen (quantité récoltée / surface de la parcelle) spécifiquement pour la culture du Riz dans la région de 'Sikasso' pour la saison 2026.

### 💻 Solution Eloquent
```php
use App\Models\Recolte;
use Illuminate\Support\Facades\DB;

$averageYield = Recolte::join('parcelles', 'recoltes.parcelle_id', '=', 'parcelles.id')
    ->where('recoltes.culture', 'Riz')
    ->where('parcelles.region', 'Sikasso')
    ->where('recoltes.saison', '2026')
    ->select(DB::raw('SUM(recoltes.quantite) / SUM(parcelles.surface) as rendement_moyen_ha'))
    ->first();
```

### 🛢️ Solution SQL brute
```sql
SELECT 
    SUM(r.quantite) / SUM(p.surface) AS rendement_moyen_ha
FROM recoltes r
INNER JOIN parcelles p ON r.parcelle_id = p.id
WHERE r.culture = 'Riz'
  AND p.region = 'Sikasso'
  AND r.saison = '2026';
```

### 💡 Explication et Optimisation
* **Logique :** On divise la somme totale des kg récoltés par la somme totale des surfaces pour obtenir un rendement moyen pondéré (et non une simple moyenne arithmétique des rendements individuels, qui masquerait les disparités de taille de parcelles).
* **Indexation :** Des index sur `recoltes(parcelle_id, culture, saison)` et `parcelles(id, region)` optimisent grandement la jointure et le filtrage.

---

## 🏆 Question 4 : Trouver la région la plus rentable de la saison 2026
**Objectif :** Identifier quelle région a enregistré le bénéfice net moyen par hectare le plus élevé lors de la saison 2026.

### 💻 Solution Eloquent
```php
use App\Models\Recolte;
use Illuminate\Support\Facades\DB;

$bestRegion = Recolte::join('parcelles', 'recoltes.parcelle_id', '=', 'parcelles.id')
    ->where('recoltes.saison', '2026')
    ->select('parcelles.region', 
        DB::raw('SUM(recoltes.benefice_net) / SUM(parcelles.surface) as benefice_par_hectare')
    )
    ->groupBy('parcelles.region')
    ->orderByDesc('benefice_par_hectare')
    ->first();
```

### 🛢️ Solution SQL brute
```sql
SELECT 
    p.region, 
    SUM(r.benefice_net) / SUM(p.surface) AS benefice_par_hectare
FROM recoltes r
INNER JOIN parcelles p ON r.parcelle_id = p.id
WHERE r.saison = '2026'
GROUP BY p.region
ORDER BY benefice_par_hectare DESC
LIMIT 1;
```

### 💡 Explication et Optimisation
* **Tri et Limitation :** `ORDER BY ... DESC` combiné à `LIMIT 1` (ou `first()` en Eloquent) permet de trier les résultats du plus rentable au moins rentable et de ne récupérer que la première ligne (le vainqueur).
* **Intégrité :** Cette requête met en évidence les régions ayant les meilleures performances économiques globales par unité de surface cultivée.

---

## 🏆 Question 5 : Journaliser l'activité de consommation d'un intrant spécifique
**Objectif :** Lister tous les mouvements de stock de type `'utilisation'` concernant l'intrant `'Urée'`, triés du plus récent au plus ancien, en affichant le nom de l'agriculteur et la quantité consommée.

### 💻 Solution Eloquent
```php
use App\Models\StockMouvement;

$movements = StockMouvement::join('stocks', 'stock_mouvements.stock_id', '=', 'stocks.id')
    ->join('users', 'stock_mouvements.user_id', '=', 'users.id')
    ->where('stocks.nom', 'Urée')
    ->where('stock_mouvements.type', 'utilisation')
    ->select(
        'users.name as agriculteur',
        'stock_mouvements.quantite',
        'stock_mouvements.quantite_avant',
        'stock_mouvements.quantite_apres',
        'stock_mouvements.date_mouvement'
    )
    ->orderByDesc('stock_mouvements.date_mouvement')
    ->get();
```

### 🛢️ Solution SQL brute
```sql
SELECT 
    u.name AS agriculteur, 
    sm.quantite, 
    sm.quantite_avant, 
    sm.quantite_apres, 
    sm.date_mouvement
FROM stock_mouvements sm
INNER JOIN stocks s ON sm.stock_id = s.id
INNER JOIN users u ON sm.user_id = u.id
WHERE s.nom = 'Urée'
  AND sm.type = 'utilisation'
ORDER BY sm.date_mouvement DESC;
```

### 💡 Explication et Optimisation
* **Double Jointure :** Cette requête relie 3 tables : le journal des mouvements (`stock_mouvements`), l'intrant concerné (`stocks`) et l'agriculteur propriétaire (`users`).
* **Tri Chronologique :** L'index sur `stock_mouvements(date_mouvement)` évite un tri coûteux en mémoire (filesort) si la table contient des dizaines de milliers de lignes.
