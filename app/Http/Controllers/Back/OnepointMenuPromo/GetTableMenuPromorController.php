<?php

namespace App\Http\Controllers\Back\OnepointMenuPromo;

use App\Models\PromoProduct;
use Illuminate\Http\Request;
use App\Models\PromoCollection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class GetTableMenuPromorController extends Controller
{
    public function index()
    {
        $items = PromoCollection::orderBy('id', 'desc')
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

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Hapus PromoProduct terkait
            PromoProduct::where('promo_collection_id', $id)->delete();

            // Hapus PromoCollection
            PromoCollection::find($id)->delete();

            // Commit transaksi jika berhasil
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Success message and data deleted',
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete data',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
