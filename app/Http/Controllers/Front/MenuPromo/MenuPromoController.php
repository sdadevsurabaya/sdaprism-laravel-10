<?php

namespace App\Http\Controllers\Front\MenuPromo;

use App\Models\PromoProduct;
use Illuminate\Http\Request;
use App\Models\PromoCollection;
use App\Http\Controllers\Controller;

class MenuPromoController extends Controller
{
    public function index(Request $request)
    {

        $today = now()->format('Y-m-d');

        $promoProducts = PromoProduct::with('promoCollection', 'product')
            ->whereHas('promoCollection', function ($query) use ($today) {
                $query->where('start', '<=', $today)
                    ->where(function ($query) use ($today) {
                        $query->where('end', '>=', $today)
                            ->orWhereDate('end', '=', $today); // Tambahkan ini untuk memeriksa tanggal yang sama
                    });
            })
            ->get();

        // $promoList = PromoCollection::where('start', '<=', $today)
        //     ->where('end', '>=', $today)
        //     ->pluck('name');

        $search = $request->search;
        $PromoCollection = PromoCollection::where('start', '<=', $today)
            ->where('end', '>=', $today)
            ->pluck('name');
        $searchDefault = $PromoCollection->first();

        if ($search) {
            $searchParam = $search;
        } else {
            $searchParam = $searchDefault;
        }

        // $promoProducts = PromoProduct::with('promoCollection', 'product')->get();

        $filteredPromoProducts = $promoProducts->filter(function ($promoProduct) use ($searchParam) {
            return $promoProduct->promoCollection->name === $searchParam;
        });

        $fullUrl = url('/');
        $formattedData = $filteredPromoProducts->map(function ($promoProduct) use ($fullUrl) {

            if (isset($promoProduct->value_diskon)) {

                $diskon = $promoProduct->value_diskon;

                if ($diskon > 100) {

                    $harga = $promoProduct->product->harga - $diskon;
                } else {

                    $nilaiDiskon = $promoProduct->product->harga * ($diskon / 100);
                    $hargaSetelahDiskon = $promoProduct->product->harga - $nilaiDiskon;
                    $harga = $hargaSetelahDiskon;
                }
            } else {

                $harga = $promoProduct->product->harga;
            }
            return [
                'label_promo' => $promoProduct->name,
                'namaproduk' => $promoProduct->product->namaproduk,
                'kategori' => $promoProduct->product->kategori,
                'kemasan' => $promoProduct->product->kemasan,
                'harga' => $harga,
                'gambar' => $fullUrl . '/files/' . $promoProduct->product->gambar,
                'url' => $promoProduct->product->url,
            ];
        })->values();


        // dd($formattedData);
        return view('page-sdamember.promo', compact(
            'PromoCollection',
            'formattedData',
        ));
    }
}
