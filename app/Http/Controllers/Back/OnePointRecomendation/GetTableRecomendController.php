<?php

namespace App\Http\Controllers\Back\OnePointRecomendation;

use Illuminate\Http\Request;
use App\Models\ItemRecommendation;
use App\Http\Controllers\Controller;

class GetTableRecomendController extends Controller
{
    public function index()
    {
        $items = ItemRecommendation::with(['produk' => function ($query) {
            $query->select('id', 'namaproduk', 'kemasan', 'harga'); 
        }, 'member' => function ($query) {
            $query->select('id', 'email');
        }])
        ->orderBy('id', 'desc')
        ->whereNull('deleted')
        ->get();
    

        return response()->json([
            'success' => true,
            'message' => 'Success message and data added',
            'data' => $items,
        ]);
    }


    public function destroy(Request $request){
        $id = $request->id;
        $item = ItemRecommendation::find($id);
        $item->deleted = now();
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Success message and data deleted',
        ]);
    }
}
