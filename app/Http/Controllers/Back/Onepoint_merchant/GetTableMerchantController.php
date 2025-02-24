<?php

namespace App\Http\Controllers\Back\Onepoint_merchant;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;

class GetTableMerchantController extends Controller
{
    public function index()
    {
        $items = Merchant::where('deleted', 'false')
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
        $item = Merchant::find($id);
        $item->deleted = 'true';
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Success message and data deleted',
        ]);
    }
}
