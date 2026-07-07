<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Parcelle;
use App\Models\Recolte;
use App\Models\Stock;
use App\Models\StockMouvement;
use App\Models\IntrantConsomme;
use App\Models\Visite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BiDataSeeder extends Seeder
{
    public function run()
    {
        // Nettoyer les tables pour éviter les doublons lors des re-seedings
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        Recolte::truncate();
        IntrantConsomme::truncate();
        StockMouvement::truncate();
        Stock::truncate();
        Parcelle::truncate();
        Visite::truncate();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // 1. Définir les localisations et s'assurer que les utilisateurs existent
        $locations = [
            'Bamako' => 'Moussa Traoré',
            'Sikasso' => 'Fatoumata Coulibaly',
            'Ségou' => 'Cheick Oumar Koné',
            'Kayes' => 'Ousmane Dembélé',
            'Mopti' => 'Boubacar Barry',
            'Koulikoro' => 'Adama Sidibé'
        ];
        
        // Assurer la présence des agriculteurs régionaux de démonstration
        $i = 0;
        foreach ($locations as $location => $realName) {
            User::updateOrCreate(
                ['email' => "agriculteur{$i}@test.com"],
                [
                    'name' => $realName,
                    'password' => bcrypt('password'),
                    'role' => 'client',
                    'location' => $location,
                    'is_active' => true,
                    'status' => 'approved',
                    'saison' => '2026',
                ]
            );
            $i++;
        }

        // Mettre à jour les localisations des utilisateurs de test historiques s'ils ne l'ont pas
        $sidiSn = User::where('email', 'sidi@sidi-agri.sn')->first();
        if ($sidiSn) {
            $sidiSn->update(['name' => 'Sidi Diallo', 'location' => 'Sikasso', 'is_active' => true, 'status' => 'approved']);
        }
        $sidiMl = User::where('email', 'sidi@sidi-agri.ml')->first();
        if ($sidiMl) {
            $sidiMl->update(['name' => 'Sidi Keita', 'location' => 'Kayes', 'is_active' => true, 'status' => 'approved']);
        }
        $testUser = User::where('email', 'test@example.com')->first();
        if ($testUser) {
            $testUser->update(['name' => 'Abdoulaye Diop', 'location' => 'Ségou', 'is_active' => true, 'status' => 'approved', 'role' => 'client']);
        }

        // Récupérer tous les clients
        $clients = User::where('role', 'client')->get();

        // 2. Définir la configuration des stocks d'intrants standards
        $stockDefaults = [
            ['nom' => 'NPK', 'type' => 'Engrais', 'seuil_critique' => 450, 'cout_unitaire' => 360], // FCFA / kg
            ['nom' => 'Urée', 'type' => 'Engrais', 'seuil_critique' => 300, 'cout_unitaire' => 300], // FCFA / kg
            ['nom' => 'Semence Riz', 'type' => 'Semence', 'seuil_critique' => 200, 'cout_unitaire' => 1260], // FCFA / kg
            ['nom' => 'Semence Maïs', 'type' => 'Semence', 'seuil_critique' => 100, 'cout_unitaire' => 1000], // FCFA / kg
            ['nom' => 'Semence Coton', 'type' => 'Semence', 'seuil_critique' => 150, 'cout_unitaire' => 1200], // FCFA / kg
            ['nom' => 'Herbicide', 'type' => 'intrant', 'seuil_critique' => 50, 'cout_unitaire' => 13000], // FCFA / L
            ['nom' => 'Pesticide', 'type' => 'intrant', 'seuil_critique' => 30, 'cout_unitaire' => 10200], // FCFA / L
        ];

        // 3. Boucler sur chaque agriculteur pour peupler ses données
        foreach ($clients as $client) {
            $region = $client->location ?: 'Sikasso';
            
            // Créer les stocks initiaux pour l'agriculteur
            $userStocks = [];
            foreach ($stockDefaults as $def) {
                // Stock initial suffisant pour la saison
                $quantiteInitiale = rand(1200, 2500);
                
                // Intentionnellement, pour l'agriculteur de Mopti, mettre des stocks très bas pour déclencher l'alerte
                if ($client->location === 'Mopti' && in_array($def['nom'], ['NPK', 'Pesticide'])) {
                    $quantiteInitiale = rand(10, 40); // Sous le seuil critique
                }

                $stock = Stock::create([
                    'user_id' => $client->id,
                    'nom' => $def['nom'],
                    'type' => $def['type'],
                    'quantite_actuelle' => $quantiteInitiale,
                    'seuil_critique' => $def['seuil_critique'],
                    'cout_unitaire' => $def['cout_unitaire'],
                    'stock_minimum' => 10,
                ]);

                $userStocks[$def['nom']] = $stock;

                // Log mouvement d'entrée initial
                StockMouvement::create([
                    'user_id' => $client->id,
                    'stock_id' => $stock->id,
                    'type' => 'entree',
                    'description' => "Approvisionnement initial de début de saison",
                    'quantite' => $quantiteInitiale,
                    'quantite_avant' => 0,
                    'quantite_apres' => $quantiteInitiale,
                    'date_mouvement' => now()->subMonths(6),
                ]);
            }

            // Créer les parcelles de l'agriculteur (2 ou 3 parcelles de cultures différentes)
            $parcellesInfo = [
                ['nom' => 'Parcelle Nord - Riz', 'culture' => 'Riz', 'surface' => rand(4, 9) + (rand(0, 9) / 10)],
                ['nom' => 'Parcelle Est - Maïs', 'culture' => 'Maïs', 'surface' => rand(3, 7) + (rand(0, 9) / 10)],
                ['nom' => 'Parcelle Sud - Coton', 'culture' => 'Coton', 'surface' => rand(2, 6) + (rand(0, 9) / 10)]
            ];

            // Réduire les parcelles à 2 pour certains pour diversifier le paysage
            if (rand(0, 1) === 0) {
                array_pop($parcellesInfo);
            }

            foreach ($parcellesInfo as $pInfo) {
                $parcelle = Parcelle::create([
                    'user_id' => $client->id,
                    'nom' => "{$pInfo['nom']} ({$region})",
                    'region' => $region,
                    'surface' => $pInfo['surface'],
                    'culture' => $pInfo['culture'],
                    'latitude' => 12.6 + (rand(-100, 100) / 1000),
                    'longitude' => -8.0 + (rand(-100, 100) / 1000),
                ]);

                // Simuler les cycles de culture (consommations d'intrants puis récoltes)
                // Nous simulons 2024 (historique) et 2026 (saison en cours)
                $saisons = ['2024', '2026'];
                foreach ($saisons as $saison) {
                    $year = $saison === '2024' ? 2024 : 2026;
                    
                    // Besoins d'intrants par hectare
                    $besoins = [
                        'NPK' => 180, // kg/ha
                        'Urée' => 120, // kg/ha
                        'Herbicide' => 4, // L/ha
                        'Pesticide' => 3, // L/ha
                    ];

                    if ($pInfo['culture'] === 'Riz') {
                        $besoins['Semence Riz'] = 100;
                    } elseif ($pInfo['culture'] === 'Maïs') {
                        $besoins['Semence Maïs'] = 25;
                    } else {
                        $besoins['Semence Coton'] = 20;
                    }

                    // Enregistrer les consommations d'intrants pour cette saison
                    $coutsIntrantsSaison = 0;
                    $dateConsommation = "{$year}-07-15"; // Mois de juillet pour les intrants

                    foreach ($besoins as $nomIntrant => $qteParHa) {
                        $qteConsommee = round($pInfo['surface'] * $qteParHa, 2);
                        $stock = $userStocks[$nomIntrant] ?? null;
                        
                        if ($stock) {
                            $quantiteAvant = $stock->quantite_actuelle;
                            
                            // Déduire du stock (en gardant positif)
                            if ($stock->quantite_actuelle >= $qteConsommee) {
                                $stock->quantite_actuelle -= $qteConsommee;
                            } else {
                                $qteConsommee = $stock->quantite_actuelle;
                                $stock->quantite_actuelle = 0;
                            }
                            $stock->save();

                            $coutsIntrantsSaison += ($qteConsommee * $stock->cout_unitaire);

                            // Enregistrer la consommation
                            IntrantConsomme::create([
                                'stock_id' => $stock->id,
                                'parcelle_id' => $parcelle->id,
                                'user_id' => $client->id,
                                'quantite_consommee' => $qteConsommee,
                                'date_consommation' => $dateConsommation,
                            ]);

                            // Enregistrer le mouvement
                            StockMouvement::create([
                                'user_id' => $client->id,
                                'stock_id' => $stock->id,
                                'type' => 'utilisation',
                                'description' => "Utilisation saison {$saison} sur {$parcelle->nom}",
                                'quantite' => $qteConsommee,
                                'quantite_avant' => $quantiteAvant,
                                'quantite_apres' => $stock->quantite_actuelle,
                                'date_mouvement' => $dateConsommation . ' 08:00:00',
                            ]);
                        }
                    }

                    // Calcul de la récolte
                    // Rendement théorique réaliste en kg par hectare :
                    // Riz : 5000-7200 kg/ha | Maïs : 3800-5200 kg/ha | Coton : 1400-2000 kg/ha
                    $rendementBase = 0;
                    $prixUnitaire = 0;
                    if ($pInfo['culture'] === 'Riz') {
                        $rendementBase = rand(5000, 7200);
                        $prixUnitaire = 350; // FCFA / kg
                    } elseif ($pInfo['culture'] === 'Maïs') {
                        $rendementBase = rand(3800, 5200);
                        $prixUnitaire = 280; // FCFA / kg
                    } else {
                        $rendementBase = rand(1400, 2000);
                        $prixUnitaire = 380; // FCFA / kg
                    }

                    // Intentionnellement, pour l'agriculteur de Mopti, simuler une catastrophe (sécheresse)
                    // de sorte que le rendement chute drastiquement et que sa rentabilité soit négative !
                    if ($client->location === 'Mopti') {
                        $rendementBase = round($rendementBase * 0.25); // Division du rendement par 4
                    }

                    $quantiteRecoltee = round($pInfo['surface'] * $rendementBase, 2);
                    $revenuTotal = $quantiteRecoltee * $prixUnitaire;
                    
                    // Coûts d'exploitation = coût des intrants consommés + coûts de main d'oeuvre & matériel
                    $coutExploitationBase = round($pInfo['surface'] * 95000); // 95 000 FCFA/ha pour labour/main d'oeuvre
                    $coutsTotaux = $coutsIntrantsSaison + $coutExploitationBase;
                    $beneficeNet = $revenuTotal - $coutsTotaux;

                    $dateRecolte = "{$year}-11-" . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

                    Recolte::create([
                        'parcelle_id' => $parcelle->id,
                        'user_id' => $client->id,
                        'date_recolte' => $dateRecolte,
                        'quantite' => $quantiteRecoltee,
                        'prix_unitaire' => $prixUnitaire,
                        'culture' => $pInfo['culture'],
                        'saison' => $saison,
                        'couts_totaux' => $coutsTotaux,
                        'revenu_total' => $revenuTotal,
                        'benefice_net' => $beneficeNet,
                    ]);
                }
            }

            // 4. Ajouter des visites de supervision pour les clients
            // Visite passée
            Visite::create([
                'user_id' => $client->id,
                'parcelle_id' => null,
                'date_visite' => now()->subMonths(1),
                'action_effectuee' => "Visite d'inspection générale de milieu de saison",
                'recommandation' => "Surveiller les attaques d'insectes, maintenir le calendrier d'irrigation.",
                'duree' => 90,
            ]);

            // Intentionnellement, planifier une visite future pour certains
            if (rand(0, 1) === 0) {
                Visite::create([
                    'user_id' => $client->id,
                    'parcelle_id' => null,
                    'date_visite' => now()->addDays(rand(5, 20)),
                    'action_effectuee' => "Planification de la visite pré-récolte",
                    'recommandation' => "Évaluer la maturité des épis/grains avant coupe.",
                    'duree' => 60,
                ]);
            }
        }

        // Cas particulier : l'agriculteur de Ségou n'a pas eu de visites récentes (visite il y a 3 mois)
        // afin de déclencher le statut de "Faible activité" sur le tableau de bord
        $segouFarmer = User::where('role', 'client')->where('location', 'Ségou')->first();
        if ($segouFarmer) {
            Visite::where('user_id', $segouFarmer->id)->delete();
            Visite::create([
                'user_id' => $segouFarmer->id,
                'parcelle_id' => null,
                'date_visite' => now()->subMonths(3), // Plus de 2 mois (faible activité)
                'action_effectuee' => "Visite de début de semis",
                'recommandation' => "Appliquer la première dose d'Urée après levée.",
                'duree' => 75,
            ]);
        }

        $this->command->info('Données BI cohérentes générées avec succès !');
        $this->command->info('- ' . $clients->count() . ' comptes agriculteurs mis en cohérence');
        $this->command->info('- ' . Parcelle::count() . ' parcelles créées avec rendements logiques');
        $this->command->info('- ' . Recolte::count() . ' récoltes créées avec bénéfices réels');
        $this->command->info('- ' . StockMouvement::count() . ' mouvements de stock tracés');
    }
}
