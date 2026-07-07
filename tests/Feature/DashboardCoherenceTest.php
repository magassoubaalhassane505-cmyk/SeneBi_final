<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Parcelle;
use App\Models\Recolte;
use App\Models\Stock;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class DashboardCoherenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Register SQLite functions to mimic MySQL for raw queries used in the app
        $connection = DB::connection();
        if ($connection->getDriverName() === 'sqlite') {
            $connection->getPdo()->sqliteCreateFunction('MONTH', function($date) {
                if (!$date) return null;
                return (int) date('m', strtotime($date));
            });
            $connection->getPdo()->sqliteCreateFunction('FIELD', function($val, ...$args) {
                $pos = array_search($val, $args);
                return $pos !== false ? $pos + 1 : 0;
            });
        }

        $this->seed();
    }

    public function test_manager_dashboard_data_and_regions_coherence()
    {
        $manager = User::where('role', 'admin')->orWhere('role', 'manager')->first();
        $this->assertNotNull($manager, "Manager account must exist.");

        // Authenticate as manager
        $response = $this->actingAs($manager)->get(route('manager.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'totalUsers',
            'activeClients',
            'totalParcelles',
            'totalSurface',
            'totalStocks',
            'criticalStocks',
            'totalRecoltes',
            'totalQuantiteRecoltee',
            'totalCA',
            'regionsData'
        ]);

        $regionsData = $response->viewData('regionsData');
        $this->assertIsArray($regionsData);
        $this->assertArrayHasKey('all', $regionsData);
        $this->assertArrayHasKey('sik', $regionsData);
        $this->assertArrayHasKey('bko', $regionsData);

        // Verify "all" totals match the sum of individual region records
        $totalHarvestFromRegions = 0;
        $totalCAFromRegions = 0;
        $totalHectaresFromRegions = 0;

        foreach ($regionsData as $key => $data) {
            if ($key === 'all') continue;
            $totalHarvestFromRegions += $data['totalHarvest'];
            $totalCAFromRegions += $data['ca'];
            $totalHectaresFromRegions += $data['hectares'];
        }

        $this->assertEquals(round($regionsData['all']['totalHarvest'], 1), round($totalHarvestFromRegions, 1), "Global production must equal sum of region productions.");
        $this->assertEquals(round($regionsData['all']['ca'], 1), round($totalCAFromRegions, 1), "Global revenue must equal sum of region revenues.");
    }

    public function test_manager_analyses_bi_page()
    {
        $manager = User::where('role', 'admin')->orWhere('role', 'manager')->first();
        
        // Test 2026 season
        $response2026 = $this->actingAs($manager)->get(route('manager.analyses', ['saison' => '2026']));
        $response2026->assertStatus(200);
        $response2026->assertViewHas([
            'stats',
            'topFarmers',
            'atRiskFarmers',
            'productionByMonth',
            'performanceByCulture',
        ]);
        $stats2026 = $response2026->viewData('stats');
        $this->assertGreaterThan(0, $stats2026['production_totale'], "2026 production must be seeded.");

        // Test 2024 season
        $response2024 = $this->actingAs($manager)->get(route('manager.analyses', ['saison' => '2024']));
        $response2024->assertStatus(200);
        $stats2024 = $response2024->viewData('stats');
        $this->assertGreaterThan(0, $stats2024['production_totale'], "2024 production must be seeded.");
    }

    public function test_client_dashboard_and_rentabilite_pages()
    {
        $client = User::where('role', 'client')->first();
        $this->assertNotNull($client, "Client account must exist.");

        // Client dashboard
        $response = $this->actingAs($client)->get(route('client.dashboard'));
        $response->assertStatus(200);

        // Client rentabilité page
        $response = $this->actingAs($client)->get(route('client.rentabilite'));
        $response->assertStatus(200);
        $response->assertViewHasAll([
            'recoltes',
            'totalCA',
            'totalCouts',
            'totalBenefice',
            'parcelles'
        ]);

        $totalCA = $response->viewData('totalCA');
        $totalCouts = $response->viewData('totalCouts');
        $totalBenefice = $response->viewData('totalBenefice');

        $this->assertEquals($totalCA - $totalCouts, $totalBenefice, "Net profit must equal revenues minus expenses.");
    }
}
