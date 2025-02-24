<?php

namespace App\Http\Controllers\Back\OnepointLogVoucher;

use App\Http\Controllers\Controller;
use App\Models\Claim_voucher;
use Illuminate\Http\Request;

class GetTableLogClaimVoucherController extends Controller
{
    public function index()
    {
        $items = Claim_voucher::orderBy('id', 'desc')
            ->with(['member' => function ($query) {
                $query->select('id', 'email');
            }])
            ->with(['voucher' => function ($query) {
                $query->select('id', 'label', 'date_start', 'date_end', 'pointneed',  'status')
                    ->selectRaw("CONCAT(disctype, ' ', discvalue) AS disc");
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
        $item = Claim_voucher::find($id);
        $item->deleted = now();
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Success message and data deleted',
        ]);
    }
}
