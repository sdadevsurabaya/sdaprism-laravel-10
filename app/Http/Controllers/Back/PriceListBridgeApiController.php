<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Services\PriceListBridgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller backend penangan API Price List Bridge (v2.0)
 * Menghubungkan request dari backend/frontend internal ke API bridge.tokosda.com
 */
class PriceListBridgeApiController extends Controller
{
    /**
     * Service instance PriceListBridgeService
     *
     * @var PriceListBridgeService
     */
    protected PriceListBridgeService $priceListService;

    /**
     * Inject PriceListBridgeService via Dependency Injection
     */
    public function __construct(PriceListBridgeService $priceListService)
    {
        $this->priceListService = $priceListService;
    }

    /**
     * Method utama untuk mengambil data Price List dari remote API API v2
     *
     * Query Parameters:
     * - limit (int): Maksimal data (default 1000, -1 untuk semua data)
     * - offset (int): Offset paginasi (default 0)
     * - search (string): Kata kunci pencarian (Kode/nama/merk)
     * - kode / id (string): Kode barang spesifik
     * - realtime (int): 1 untuk bypass cache server, 0 untuk default
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getData(Request $request): JsonResponse
    {
        if (\Auth::check() && !\Auth::user()->canAccessRealtimePricelist()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki izin untuk melihat Pricelist Realtime.'
            ], 403);
        }

        // Terima parameter dari HTTP Request

        $params = [];

        if ($request->has('limit')) {
            $params['limit'] = $request->query('limit');
        }

        if ($request->has('offset')) {
            $params['offset'] = $request->query('offset');
        }

        if ($request->filled('search')) {
            $params['search'] = $request->query('search');
        }

        // Dukung baik 'kode' maupun 'id' sebagai alias kode barang
        if ($request->filled('kode')) {
            $params['kode'] = $request->query('kode');
        } elseif ($request->filled('id')) {
            $params['kode'] = $request->query('id');
        }

        if ($request->has('realtime')) {
            $params['realtime'] = $request->query('realtime');
        }

        // Panggil service
        $result = $this->priceListService->fetch($params);

        $statusCode = isset($result['success']) && $result['success'] ? 200 : ($result['status'] ?? 500);

        return response()->json($result, $statusCode);
    }
}
