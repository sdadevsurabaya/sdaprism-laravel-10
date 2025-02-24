<?php

namespace App\Http\Controllers\Api\ProductShop;


use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UpdateProductShopController extends Controller
{
    public function index(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id' => 'required',
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

            $editProduct = Product::findOrFail($request->id);
            $editProduct->id_brand = $request->id_brand;
            $editProduct->namaproduk = $request->namaproduk;
            $editProduct->shortdescription = $request->shortdescription;
            $editProduct->merek = $request->merek;
            $editProduct->packing = $request->packing;
            $editProduct->kemasan = $request->kemasan;
            $editProduct->kategori = $request->kategori;
            $editProduct->subkategori = $request->subkategori;
            $editProduct->harga = $request->harga;
            $editProduct->specification = $request->specification;
            $editProduct->description = $request->description;
            $editProduct->gambar = $request->gambar;
            $editProduct->gambar1 = $request->gambar1;
            $editProduct->gambar2 = $request->gambar2;
            $editProduct->gambar3 = $request->gambar3;
            $editProduct->hargadiskon = $request->hargadiskon;
            $editProduct->sku = $request->sku;
            $editProduct->jumlahstock = $request->jumlahstock;
            $editProduct->berat = $request->berat;
            $editProduct->panjang = $request->panjang;
            $editProduct->lebar = $request->lebar;
            $editProduct->tinggi = $request->tinggi;
            $editProduct->kind = $request->kind;
            $editProduct->produkunggulan = $request->produkunggulan;
            $editProduct->product_status = $request->product_status;
            $editProduct->beratbersih = $request->beratbersih;
            $editProduct->beratkotor = $request->beratkotor;
            $editProduct->keywords = $request->keywords;
            $editProduct->promo = $request->promo;
            $editProduct->deleted = $request->deleted;
            $editProduct->status = $request->status;
            $editProduct->created_at = $request->created_at;
            $editProduct->updated_at = $request->updated_at;
            $editProduct->slug = $request->slug;
            $editProduct->url = $request->url;

            $editProduct->save();

            $data = [
                'success' => true,
                'message' => 'Update Detail Produk Success',
                'data' => $editProduct

            ];


            return $data;
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 200);
        }
    }
}
