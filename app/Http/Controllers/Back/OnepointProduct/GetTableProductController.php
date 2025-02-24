<?php

namespace App\Http\Controllers\Back\OnepointProduct;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class GetTableProductController extends Controller
{
    public function index()
    {
        $items = Product::with(['brand:id,brand'])
            ->where('deleted', 'false')
            ->orderBy('id', 'desc')
            ->get();


        return response()->json([
            'success' => true,
            'message' => 'Success Get Data',
            'data' => $items,
        ]);
    }


    public function destroy(Request $request)
    {
        $id = $request->id;
        $item = Product::find($id);
        $item->deleted = 'true';
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Success message and data deleted',
        ]);
    }
}
