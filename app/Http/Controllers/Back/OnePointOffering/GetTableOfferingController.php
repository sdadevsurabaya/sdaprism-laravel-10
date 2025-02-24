<?php

namespace App\Http\Controllers\Back\OnePointOffering;

use App\Http\Controllers\Controller;
use App\Models\ItemOffering;
use Illuminate\Http\Request;

class GetTableOfferingController extends Controller
{
    public function index()
    {
        $items = ItemOffering::with(['produk' => function ($query) {
            $query->select('id', 'namaproduk', 'kemasan', 'harga'); 
        }, 'member' => function ($query) {
            $query->select('id', 'email');
        }])
        ->orderBy('id', 'desc')
        ->whereNull('deleted')
        ->get();
    

        return response()->json([
            'success' => true,
            'message' => 'Success Get Data',
            'data' => $items,
        ]);
    }


    public function destroy(Request $request){
        $id = $request->id;
        $item = ItemOffering::find($id);
        $item->deleted = now();
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Success message and data deleted',
        ]);
    }
}
