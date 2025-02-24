<?php

namespace App\Http\Controllers\Back\OnepointMenuPromo;

use App\Models\Product;
use App\Models\Merchant;
use App\Models\PromoProduct;
use Illuminate\Http\Request;
use App\Models\PromoCollection;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MenuPromorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('back.Onepoint_MenuPromo.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $onepointProducts = Product::with('merchant')
            ->where('product_status', 1)
            ->get();


        $promoProducts = collect();

        $merchant = Merchant::pluck('merchant_name', 'id');


        return view('back.Onepoint_MenuPromo.create', compact('promoProducts', 'onepointProducts', 'merchant'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $idMerchant =  $request->input('id_merchant');
            $onepointProdukId =  $request->input('onepoint_produk_id');
            $promoCollectionStart =  $request->input('promo_collection_start');
            $promoCollectionEnd =  $request->input('promo_collection_end');
            $request->validate([
                'promo_collection_name' => 'required|string|max:255',
                'onepoint_produk_id' => [
                    'array',
                    Rule::exists('onepoint_produk', 'id'),
                    Rule::unique('promo_products')
                        ->where(function ($query) use ($onepointProdukId, $idMerchant, $promoCollectionStart, $promoCollectionEnd) {
                            $query->where('onepoint_produk_id', $onepointProdukId)
                                ->where('id_merchant', $idMerchant)
                                ->where(function ($subquery) use ($promoCollectionStart, $promoCollectionEnd) {
                                    $subquery->where('end', '>=', $promoCollectionStart)
                                        ->where('start', '<=', $promoCollectionEnd);
                                });
                        }),
                ],
                'promo_product_name' => 'required|string|max:255',
                'promo_product_diskon' => 'required|string|max:255',
                'promo_collection_start' => 'required|date|after_or_equal:today',
                'promo_collection_end' => 'required|date|after_or_equal:promo_collection_start',
                'id_merchant' => 'required|exists:onepoint_merchant,id', // Sesuaikan dengan nama dan tabel merchant yang sesuai
            ]);
        } catch (ValidationException $exception) {
            return redirect()->back()
                ->withErrors($exception->validator)
                ->withInput();
        }

        $promoCollection = PromoCollection::create([
            'name' => $request->input('promo_collection_name'),
            'start' => $request->input('promo_collection_start'),
            'end' => $request->input('promo_collection_end'),
            'id_merchant' => $request->input('id_merchant'),
            // ... tambahkan data lainnya sesuai kebutuhan
        ]);

        foreach ($request->input('onepoint_produk_id', []) as $onepointProdukId) {

            PromoProduct::updateOrCreate(
                [
                    'promo_collection_id' => $promoCollection->id,
                    'onepoint_produk_id' => $onepointProdukId,
                ],
                [
                    'name' => $request->input('promo_product_name'),
                    'value_diskon' => $request->input('promo_product_diskon'),
                    'start' => $request->input('promo_collection_start'),
                    'end' => $request->input('promo_collection_end'),
                    'id_merchant' => $request->input('id_merchant'),
                ]
            );
        }

        return redirect()->route('onepoint_menu_promo.index')->with('success', 'Promo created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $promoCollection = PromoCollection::findOrFail($id);
        $promoProducts = PromoProduct::where('promo_collection_id', $promoCollection->id)->get();
        $onepointProducts = Product::with('merchant')
            ->where('product_status', 1)
            ->get();


        $merchant = Merchant::pluck('merchant_name', 'id');
        return view('back.Onepoint_MenuPromo.edit', compact('promoCollection', 'promoProducts', 'onepointProducts', 'merchant'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
            $idMerchant = $request->input('id_merchant');
            $onepointProdukId = $request->input('onepoint_produk_id');
            $promoCollectionStart = $request->input('promo_collection_start');
            $promoCollectionEnd = $request->input('promo_collection_end');

            $request->validate([
                'promo_collection_name' => 'required|string|max:255',
                'onepoint_produk_id' => [
                    'array',
                    Rule::exists('onepoint_produk', 'id'),
                    Rule::unique('promo_products')
                        ->where(function ($query) use ($onepointProdukId, $idMerchant, $promoCollectionStart, $promoCollectionEnd, $id) {
                            $query->where('onepoint_produk_id', $onepointProdukId)
                                ->where('id_merchant', $idMerchant)
                                ->where(function ($subquery) use ($promoCollectionStart, $promoCollectionEnd, $id) {
                                    // Tambahkan pengecualian ID promo_collection yang sedang diedit
                                    $subquery->where('end', '>=', $promoCollectionStart)
                                        ->where('start', '<=', $promoCollectionEnd)
                                        ->where('promo_collection_id', '!=', $id);
                                });
                        }),
                ],
                'promo_product_name' => 'required|string|max:255',
                'promo_product_diskon' => 'required|string|max:255',
                'promo_collection_start' => 'required|date',
                'promo_collection_end' => 'required|date|after_or_equal:promo_collection_start',
                'id_merchant' => 'required|exists:onepoint_merchant,id',
            ]);
        } catch (ValidationException $exception) {
            return redirect()->back()
                ->withErrors($exception->validator)
                ->withInput();
        }


        $promoCollection = PromoCollection::findOrFail($id);


        $promoCollection->update([
            'name' => $request->input('promo_collection_name'),
            'start' => $request->input('promo_collection_start'),
            'end' => $request->input('promo_collection_end'),
            'id_merchant' => $request->input('id_merchant'),

        ]);


        $selectedOnepointProdukIds = $request->input('onepoint_produk_id', []);


        $existingOnepointProdukIds = $promoCollection->promoProducts->pluck('onepoint_produk_id')->toArray();


        $produkToDelete = array_diff($existingOnepointProdukIds, $selectedOnepointProdukIds);
        PromoProduct::where('promo_collection_id', $promoCollection->id)
            ->whereIn('onepoint_produk_id', $produkToDelete)
            ->delete();


        $produkToCreate = array_diff($selectedOnepointProdukIds, $existingOnepointProdukIds);
        foreach ($produkToCreate as $onepointProdukId) {
            PromoProduct::create([
                'promo_collection_id' => $promoCollection->id,
                'onepoint_produk_id' => $onepointProdukId,
                'name' => $request->input('promo_product_name'),
                'value_diskon' => $request->input('promo_product_diskon'),
                'start' => $request->input('promo_collection_start'),
                'end' => $request->input('promo_collection_end'),
                'id_merchant' => $request->input('id_merchant'),

            ]);
        }


        foreach ($selectedOnepointProdukIds as $onepointProdukId) {
            $promoProduct = PromoProduct::where('promo_collection_id', $promoCollection->id)
                ->where('onepoint_produk_id', $onepointProdukId)
                ->first();

            if ($promoProduct) {
                $promoProduct->update([
                    'name' => $request->input('promo_product_name'),
                    'value_diskon' => $request->input('promo_product_diskon'),
                    'start' => $request->input('promo_collection_start'),
                    'end' => $request->input('promo_collection_end'),

                ]);
            }
        }

        return redirect()->route('onepoint_menu_promo.index')->with('success', 'User created successfully');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
