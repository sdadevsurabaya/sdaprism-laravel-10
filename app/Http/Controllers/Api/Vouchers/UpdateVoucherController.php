<?php

namespace App\Http\Controllers\Api\Vouchers;

use App\Http\Controllers\Controller;
use App\Models\Uniquecode;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateVoucherController extends Controller
{

    public function index(Request $request)
    {
        $id = $request->id;
        $status = $request->status;

        $updatevoucher = Uniquecode::find($id);
        $input = ['status' => $status];
        $updatevoucher->update($input);

        return response()->json([
            'success' => true,
            'message' => 'update status voucher success',
            'data' => $updatevoucher,
        ], Response::HTTP_OK);
    }
}
