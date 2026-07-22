<?php

namespace App\Http\Controllers\Back;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RakitanApiController extends Controller
{
    public function getData(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter ID is required.'
            ], 400);
        }

        $endpoint = env('RAKITAN_QR_ENDPOINT', 'https://bridge.tokosda.com/api-v2/endpoints/rakitan_qr.php');

        try {
            $response = Http::get($endpoint, [
                'id' => $id
            ]);

            ActivityLogger::log(
                'QR Scan',
                'QR Lookup',
                "Melakukan scan/pencarian data rakitan QR Serial: '{$id}'",
                ['scanned_id' => $id, 'http_status' => $response->status(), 'success' => $response->successful()]
            );

            if ($response->successful()) {
                return $response->json();
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch data from remote server.',
                'status' => $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while connecting to the remote server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
