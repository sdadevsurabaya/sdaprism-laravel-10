<?php

namespace App\Http\Controllers\Back\OnepointSocialAccount;

use Illuminate\Http\Request;
use App\Models\SocialAccount;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class GetTableSocialAccountController extends Controller
{
    public function index()
    {
        $items = SocialAccount::with(['user:id,email'])->orderBy('id', 'desc')->get();

        $items = $items->map(function ($item) {
            // Ubah format tanggal di sini
            $item->created_at_human = Carbon::parse($item->created_at)->format('d-M-Y H:i:s');
        
            // Hapus properti created_at jika tidak diperlukan lagi
            unset($item->created_at);
        
            return $item;
        });
    

        return response()->json([
            'success' => true,
            'message' => 'Success Get Data',
            'data' => $items,
        ]);
    }


    public function destroy(Request $request){
        $id = $request->id;
        $item = SocialAccount::find($id);
    
        if ($item) {
            $item->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Success message and data deleted',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 404);
        }
    }
    
}
