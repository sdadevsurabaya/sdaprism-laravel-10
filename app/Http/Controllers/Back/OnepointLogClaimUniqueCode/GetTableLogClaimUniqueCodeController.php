<?php

namespace App\Http\Controllers\Back\OnepointLogClaimUniqueCode;

use App\Http\Controllers\Controller;
use App\Models\Claim_uniquecode;
use Illuminate\Http\Request;

class GetTableLogClaimUniqueCodeController extends Controller
{
    public function index()
    {
        $items = Claim_uniquecode::orderBy('id', 'desc')
            ->with(['member' => function ($query) {
                $query->select('id', 'email');
            }])
            ->with(['uniquecode' => function ($query) {
                $query->select('id', 'kode', 'label');
            }])
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
        $item = Claim_uniquecode::find($id);
        $item->deleted = now();
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Success message and data deleted',
        ]);
    }
}
