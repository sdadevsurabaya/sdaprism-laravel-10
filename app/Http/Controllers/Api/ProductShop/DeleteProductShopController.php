<?php

namespace App\Http\Controllers\Api\ProductShop;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

class DeleteProductShopController extends Controller
{
    public function index(Request $request)
    {
        try {
            Product::findOrFail($request->id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Success Delete records Product.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 200);
        }
    }
}
