<?php

namespace App\Http\Controllers\Api\ProductShop;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\PromoProduct;

class GetProductShopController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        // dd($search);
        $fullUrl = url('/');
        if ($search) {
            $data = Product::select('kategori', 'namaproduk', 'kemasan', 'harga', 'id_brand')->selectRaw("CASE WHEN LENGTH(gambar) > 0 THEN CONCAT('$fullUrl', '/files/', gambar) ELSE NULL END AS gambar")

                ->where(function ($query) use ($search) {
                    $query->whereHas('brand', function ($brandQuery) use ($search) {
                        $brandQuery->where('brand', 'LIKE', "%$search%");
                    })
                        ->orWhere('namaproduk', 'LIKE', "%$search%")
                        ->orWhere('slug', 'LIKE', "%$search%");
                })
                ->get()->toArray();
        } else {
            $data = Product::select('kategori', 'namaproduk', 'kemasan', 'harga', 'id')->selectRaw("CASE WHEN LENGTH(gambar) > 0 THEN CONCAT('$fullUrl', '/files/', gambar) ELSE NULL END AS gambar")->get()->toArray();
        }

        return response()->json([
            'success' => true,
            'message' => 'Get Product All',
            'data' => $data,
        ], 200);
    }


    public function indexMobile(Request $request)
    {

        if (!$request->merchant) {
            $idMerchant = 3;
        } else {
            $idMerchant = Merchant::where('merchant_name', $request->merchant)->first()->id;
        }

        $brand = Brand::where('id_merchant', $idMerchant)->pluck('brand');
        $merchant = Merchant::pluck('merchant_name');
        $fullUrl = url('/');

        $brandsWithProducts = Brand::with(['products' => function ($query) use ($fullUrl, $idMerchant) {
            $query->select('*')
                ->selectRaw("CASE WHEN LENGTH(gambar) > 0 THEN CONCAT('$fullUrl', '/files/', gambar) ELSE NULL END AS gambar")
                ->where('product_status', '1')
                ->where('id_merchant', $idMerchant);
        }])->where('id_merchant', $idMerchant)->get();

        $promoProducts = PromoProduct::where('id_merchant', $idMerchant)
            ->whereDate('start', '<=', now())
            ->whereDate('end', '>=', now())
            ->get();

        $mergedData = $brandsWithProducts->map(function ($brand) use ($promoProducts, $fullUrl) {
            return [
                'id' => $brand->id,
                'id_merchant' => $brand->id_merchant,
                'brand' => $brand->brand,
                'status' => $brand->status,
                'created_at' => $brand->created_at,
                'updated_at' => $brand->updated_at,
                'products' => $brand->products->map(function ($product) use ($promoProducts, $fullUrl) {
                    $promoProduct = $promoProducts->where('onepoint_produk_id', $product->id)->first();

                    if ($promoProduct && isset($promoProduct->value_diskon)) {
                        $diskon = $promoProduct->value_diskon;

                        if ($diskon > 100) {
                            $harga = $product->harga - $diskon;
                        } else {
                            $nilaiDiskon = $product->harga * ($diskon / 100);
                            $hargaSetelahDiskon = $product->harga - $nilaiDiskon;
                            $harga = $hargaSetelahDiskon;
                        }
                    } else {
                        $harga = $product->harga;
                    }

                    return array_merge(
                        $product->toArray(), // Ambil seluruh atribut dari produk
                        [
                            'harga' => $harga, // Tambahkan harga berdasarkan perhitungan atau harga normal
                            'gambar' =>  $product->gambar, // Tambahkan full URL ke gambar
                            // ... (tambahkan atribut lain yang diperlukan)
                        ]
                    );
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Get Product All',
            'brand' => $brand,
            'merchant' => $merchant,
            'data' => $mergedData,
        ], 200);
    }
}
