<?php

namespace App\Http\Controllers\Api\ProductShop;


use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PostProductShopController extends Controller
{
    public function index(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_brand' => 'required',
                'namaproduk' => 'required',
                'shortdescription' => 'required',
                'description' => 'required',
                'harga' => 'required',
                'url' => 'required',
                'slug' => 'required',

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors(),
                ], 200);
            }



            $Product = Product::create($input);
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data' =>   $Product
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 200);
        }
    }
}
