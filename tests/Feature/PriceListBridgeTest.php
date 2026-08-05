<?php

namespace Tests\Feature;

use App\Services\PriceListBridgeService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PriceListBridgeTest extends TestCase
{
    /**
     * Test PriceListBridgeService dengan Http Fake.
     */
    public function test_service_fetches_price_list_successfully_with_mock()
    {
        Http::fake([
            'https://bridge.tokosda.com/api-v2/endpoints/pricelist.php*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'Kode' => 'BRG-00123',
                        'nama' => 'ASUS ROG ZEPHYRUS G14 GA402',
                        'merk' => 'ASUS',
                        'Harga' => 25500000,
                        'tglJualTerakhir' => '2026-08-01 14:30:00',
                        'tglUpdateData' => '2026-08-02 08:00:00'
                    ]
                ],
                'total' => 1,
                'limit' => 1000,
                'offset' => 0,
                'cached' => true
            ], 200)
        ]);

        $service = new PriceListBridgeService();
        $result = $service->fetch(['limit' => 1000, 'offset' => 0]);

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('BRG-00123', $result['data'][0]['Kode']);
        $this->assertEquals('ASUS ROG ZEPHYRUS G14 GA402', $result['data'][0]['nama']);
    }

    /**
     * Test API endpoint /api/pricelist-data dengan Http Fake.
     */
    public function test_api_endpoint_returns_json_response()
    {
        Http::fake([
            'https://bridge.tokosda.com/api-v2/endpoints/pricelist.php*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'Kode' => 'BRG-00124',
                        'nama' => 'LOGITECH MX MASTER 3S WIRELESS',
                        'merk' => 'LOGITECH',
                        'Harga' => 1650000,
                        'tglJualTerakhir' => '2026-08-03 09:15:22',
                        'tglUpdateData' => '2026-08-03 10:00:00'
                    ]
                ],
                'total' => 1,
                'limit' => 1000,
                'offset' => 0,
                'cached' => true
            ], 200)
        ]);

        $response = $this->getJson('/api/pricelist-data?search=LOGITECH');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total' => 1,
                'data' => [
                    [
                        'Kode' => 'BRG-00124',
                        'merk' => 'LOGITECH'
                    ]
                ]
            ]);
    }

    /**
     * Test single item lookup by kode.
     */
    public function test_service_get_by_kode()
    {
        Http::fake([
            'https://bridge.tokosda.com/api-v2/endpoints/pricelist.php*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'Kode' => 'BRG-00123',
                        'nama' => 'ASUS ROG ZEPHYRUS G14 GA402',
                        'merk' => 'ASUS',
                        'Harga' => 25500000,
                        'tglJualTerakhir' => '2026-08-01 14:30:00',
                        'tglUpdateData' => '2026-08-02 08:00:00'
                    ]
                ],
                'total' => 1,
                'limit' => null,
                'offset' => null,
                'cached' => true
            ], 200)
        ]);

        $service = new PriceListBridgeService();
        $result = $service->getByKode('BRG-00123');

        $this->assertTrue($result['success']);
        $this->assertEquals('BRG-00123', $result['data'][0]['Kode']);
    }

    /**
     * Test route /pricelists/api terhubung dengan API dan mengurutkan data by Kode.
     */
    public function test_show_api_pricelist_sorted_by_kode()
    {
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();

        Http::fake([
            'https://bridge.tokosda.com/api-v2/endpoints/pricelist.php*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'Kode' => 'BRG-00200',
                        'nama' => 'ZOTAC GAMING RTX 4070',
                        'merk' => 'ZOTAC',
                        'Harga' => 10500000,
                    ],
                    [
                        'Kode' => 'BRG-00100',
                        'nama' => 'ASUS ROG ZEPHYRUS',
                        'merk' => 'ASUS',
                        'Harga' => 25000000,
                    ]
                ],
                'total' => 2,
                'limit' => -1,
                'offset' => 0,
                'cached' => true
            ], 200)
        ]);

        $response = $this->actingAs($user)->get('/pricelists/api');

        $response->assertStatus(200);
        $response->assertSee('Price Lists');
        // Assert BRG-00100 is sorted before BRG-00200
        $content = $response->getContent();
        $pos1 = strpos($content, 'BRG-00100');
        $pos2 = strpos($content, 'BRG-00200');
        $this->assertNotFalse($pos1);
        $this->assertNotFalse($pos2);
        $this->assertTrue($pos1 < $pos2, 'BRG-00100 should be ordered before BRG-00200');
    }

    /**
     * Test Service menyimpan data dalam cache Laravel dan mengembalikan laravel_cached => true.
     */
    public function test_service_caches_data_in_laravel()
    {
        Http::fake([
            'https://bridge.tokosda.com/api-v2/endpoints/pricelist.php*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'Kode' => 'BRG-00999',
                        'nama' => 'TEST CACHED ITEM',
                        'merk' => 'TEST',
                        'Harga' => 50000,
                    ]
                ],
                'total' => 1,
                'limit' => 10,
                'offset' => 0,
                'cached' => true
            ], 200)
        ]);

        $service = new PriceListBridgeService();

        // Panggilan pertama (HTTP request ditangkap)
        $firstCall = $service->fetch(['limit' => 10, 'search' => 'TEST_CACHE']);
        $this->assertTrue($firstCall['success']);
        $this->assertArrayNotHasKey('laravel_cached', $firstCall);

        // Panggilan kedua (menggunakan cache Laravel)
        $secondCall = $service->fetch(['limit' => 10, 'search' => 'TEST_CACHE']);
        $this->assertTrue($secondCall['success']);
        $this->assertTrue($secondCall['laravel_cached']);

        // Panggilan ketiga dengan realtime = 1 (bypass cache)
        $thirdCall = $service->fetch(['limit' => 10, 'search' => 'TEST_CACHE', 'realtime' => 1]);
        $this->assertTrue($thirdCall['success']);
        $this->assertArrayNotHasKey('laravel_cached', $thirdCall);
    }

    /**
     * Test route /pricelists (List Utama) menampilkan item Price List API di paling atas.
     */
    public function test_pricelists_index_shows_api_item_at_top()
    {
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get('/pricelists');

        $response->assertStatus(200);
        $response->assertSee('Price Lists');
    }

    /**
     * Test route /pricelists/0 tidak 404 dan menampilkan data API.
     */
    public function test_show_api_pricelist_with_id_zero()
    {
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();

        Http::fake([
            'https://bridge.tokosda.com/api-v2/endpoints/pricelist.php*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'Kode' => 'BRG-00100',
                        'nama' => 'ASUS ROG ZEPHYRUS',
                        'merk' => 'ASUS',
                        'Harga' => 25000000,
                    ]
                ],
                'total' => 1,
                'limit' => -1,
                'offset' => 0,
                'cached' => true
            ], 200)
        ]);

        $response = $this->actingAs($user)->get('/pricelists/0');

        $response->assertStatus(200);
        $response->assertSee('Price Lists');
    }


    /**
     * Test Staff tanpa whitelist tidak dapat melihat atau membuka Realtime Price List.
     */
    public function test_non_whitelisted_staff_cannot_see_or_access_realtime_pricelist()
    {
        $staffRole = \App\Models\Roles::firstOrCreate(['name' => 'staff']);
        $staffUser = \App\Models\User::factory()->create();
        \App\Models\RolesUser::create(['users_id' => $staffUser->id, 'roles_id' => $staffRole->id]);

        // Pastikan tidak ada di whitelist
        \App\Models\PricelistApiWhitelist::where('user_id', $staffUser->id)->delete();

        // 1. Tidak tampil di /pricelists
        $responseIndex = $this->actingAs($staffUser)->get('/pricelists');
        $responseIndex->assertStatus(200);
        $responseIndex->assertDontSee('Price Lists');

        // 2. Ditolak saat membuka /pricelists/api
        $responseShow = $this->actingAs($staffUser)->get('/pricelists/api');
        $responseShow->assertRedirect('/pricelists');
        $responseShow->assertSessionHas('error');
    }

    /**
     * Test Staff yang terdaftar di whitelist dapat melihat dan membuka Realtime Price List.
     */
    public function test_whitelisted_staff_can_see_and_access_realtime_pricelist()
    {
        $staffRole = \App\Models\Roles::firstOrCreate(['name' => 'staff']);
        $staffUser = \App\Models\User::factory()->create();
        \App\Models\RolesUser::create(['users_id' => $staffUser->id, 'roles_id' => $staffRole->id]);

        // Tambahkan ke whitelist
        \App\Models\PricelistApiWhitelist::create(['user_id' => $staffUser->id]);

        Http::fake([
            'https://bridge.tokosda.com/api-v2/endpoints/pricelist.php*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'Kode' => 'BRG-00100',
                        'nama' => 'ASUS ROG ZEPHYRUS',
                        'merk' => 'ASUS',
                        'Harga' => 25000000,
                    ]
                ],
                'total' => 1,
                'limit' => -1,
                'offset' => 0,
                'cached' => true
            ], 200)
        ]);

        // 1. Tampil di /pricelists
        $responseIndex = $this->actingAs($staffUser)->get('/pricelists');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Price Lists');

        // 2. Berhasil membuka /pricelists/api
        $responseShow = $this->actingAs($staffUser)->get('/pricelists/api');
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Price Lists');
    }

    /**
     * Test Admin dapat memperbarui Whitelist melalui endpoint POST /user/whitelist-realtime.
     */
    public function test_admin_can_update_pricelist_whitelist()
    {
        $adminRole = \App\Models\Roles::firstOrCreate(['name' => 'admin']);
        $adminUser = \App\Models\User::factory()->create();
        \App\Models\RolesUser::create(['users_id' => $adminUser->id, 'roles_id' => $adminRole->id]);

        $staffUser = \App\Models\User::factory()->create();

        $response = $this->actingAs($adminUser)->post('/user/whitelist-realtime', [
            'user_ids' => [$staffUser->id]
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('pricelist_api_whitelists', ['user_id' => $staffUser->id]);
    }

}





