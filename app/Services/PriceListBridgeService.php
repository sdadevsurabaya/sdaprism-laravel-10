<?php

namespace App\Services;

use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service untuk mengelola integrasi API Price List v2.0
 * Endpoint target: https://bridge.tokosda.com/api-v2/endpoints/pricelist.php
 * 
 * Sangat mudah dipelihara dan digunakan oleh programmer backend.
 * Dilengkapi dengan Caching otomatis di Laravel (default 1 jam / 3600 detik).
 */
class PriceListBridgeService
{
    /**
     * URL Endpoint API Price List
     *
     * @var string
     */
    protected string $endpoint;

    /**
     * Durasi cache dalam detik (default: 3600 detik = 1 jam)
     *
     * @var int
     */
    protected int $cacheTtl;

    /**
     * Constructor Service
     */
    public function __construct()
    {
        $this->endpoint = env(
            'PRICELIST_API_ENDPOINT',
            'https://bridge.tokosda.com/api-v2/endpoints/pricelist.php'
        );

        $this->cacheTtl = (int) env('PRICELIST_CACHE_TTL', 3600);
    }

    /**
     * Mengambil data price list dari API eksternal berdasarkan parameter query.
     * Menggunakan Laravel Cache agar respon super cepat dan hemat bandwidth.
     *
     * @param array $params Parameter query opsional:
     *                      - limit (int): Jumlah data per halaman, -1 untuk semua data
     *                      - offset (int): Offset paginasi
     *                      - search (string): Kata kunci pencarian nama/kode/merk
     *                      - kode (string): Kode barang spesifik
     *                      - realtime (int): 1 untuk bypass cache, 0 untuk default
     * @return array Response JSON standar dari server bridge
     */
    public function fetch(array $params = []): array
    {
        // Menyusun parameter query dengan default value yang aman
        $queryParams = [];

        if (isset($params['limit'])) {
            $queryParams['limit'] = (int) $params['limit'];
        }

        if (isset($params['offset'])) {
            $queryParams['offset'] = (int) $params['offset'];
        }

        if (!empty($params['search'])) {
            $queryParams['search'] = (string) $params['search'];
        }

        if (!empty($params['kode'])) {
            $queryParams['kode'] = (string) $params['kode'];
        }

        if (isset($params['realtime'])) {
            $queryParams['realtime'] = (int) $params['realtime'] === 1 ? 1 : 0;
        }

        $isRealtime = isset($queryParams['realtime']) && $queryParams['realtime'] === 1;

        // Buat key cache unik berdasarkan parameter query
        $cacheKey = 'pricelist_api_' . md5(json_encode($queryParams));

        // Jika realtime = 1, bersihkan cache terlebih dahulu
        if ($isRealtime) {
            Cache::forget($cacheKey);
        }

        // Jika data ada di cache Laravel dan tidak realtime, langsung kembalikan cache
        if (!$isRealtime && Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            if (is_array($cachedData)) {
                $cachedData['laravel_cached'] = true;
                return $cachedData;
            }
        }

        try {
            // Memanggil API eksternal menggunakan Laravel Http Facade
            $response = Http::timeout(30)->get($this->endpoint, $queryParams);

            // Log aktivitas ke database sistem jika ActivityLogger tersedia
            $this->logActivity('Fetch PriceList API', [
                'params'      => $queryParams,
                'status_code' => $response->status(),
                'success'     => $response->successful(),
                'realtime'    => $isRealtime,
            ]);

            if ($response->successful()) {
                $result = $response->json() ?? [
                    'success' => false,
                    'message' => 'Response kosong dari server API Price List.'
                ];

                if (isset($result['success']) && $result['success']) {
                    // Simpan data sukses ke cache Laravel
                    Cache::put($cacheKey, $result, $this->cacheTtl);
                }

                return $result;
            }

            return [
                'success' => false,
                'message' => 'Gagal mengambil data dari server remote API Price List.',
                'status'  => $response->status(),
                'details' => $response->json()
            ];

        } catch (Exception $e) {
            Log::error("PriceListBridgeService Error: " . $e->getMessage(), [
                'endpoint' => $this->endpoint,
                'params'   => $queryParams
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan koneksi ke server API Price List.',
                'error'   => $e->getMessage()
            ];
        }
    }

    /**
     * Clear / flush cache Price List spesifik di Laravel
     *
     * @param array $params Parameter spesifik jika ada
     */
    public function clearCache(array $params = []): void
    {
        if (!empty($params)) {
            $cacheKey = 'pricelist_api_' . md5(json_encode($params));
            Cache::forget($cacheKey);
        } else {
            Cache::forget('pricelist_api_' . md5(json_encode(['limit' => -1, 'realtime' => 0])));
            Cache::forget('pricelist_api_' . md5(json_encode(['limit' => -1])));
        }
    }

    /**
     * Helper untuk mengambil 1 item barang spesifik berdasarkan Kode barang.
     *
     * @param string $kode Kode barang (contoh: BRG-00123)
     * @param bool $realtime Set true jika ingin mengambil data langsung dari database tanpa cache
     * @return array
     */
    public function getByKode(string $kode, bool $realtime = false): array
    {
        return $this->fetch([
            'kode'     => $kode,
            'realtime' => $realtime ? 1 : 0
        ]);
    }

    /**
     * Helper untuk melakukan pencarian produk berdasarkan kata kunci (Kode, nama, merk).
     *
     * @param string $keyword Kata kunci pencarian
     * @param int $limit Jumlah data maksimal
     * @param int $offset Offset paginasi
     * @param bool $realtime Set true untuk realtime data
     * @return array
     */
    public function search(string $keyword, int $limit = 1000, int $offset = 0, bool $realtime = false): array
    {
        return $this->fetch([
            'search'   => $keyword,
            'limit'    => $limit,
            'offset'   => $offset,
            'realtime' => $realtime ? 1 : 0
        ]);
    }

    /**
     * Helper untuk mengambil SELURUH data Price List (limit = -1).
     *
     * @param bool $realtime Set true untuk realtime data
     * @return array
     */
    public function getAll(bool $realtime = false): array
    {
        return $this->fetch([
            'limit'    => -1,
            'realtime' => $realtime ? 1 : 0
        ]);
    }

    /**
     * Simpan log aktivitas jika class ActivityLogger tersedia
     */
    protected function logActivity(string $action, array $context = []): void
    {
        try {
            if (class_exists(ActivityLogger::class)) {
                ActivityLogger::log(
                    'PriceList API Bridge',
                    $action,
                    "Panggilan API Price List dengan status: " . ($context['status_code'] ?? 'Unknown'),
                    $context
                );
            }
        } catch (Exception $e) {
            Log::warning("Gagal mencatat ActivityLogger: " . $e->getMessage());
        }
    }
}
