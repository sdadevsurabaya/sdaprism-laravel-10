<?php

namespace App\Http\Controllers\Back\OnepointUsers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class GetTableUserController extends Controller
{
    public function index()
    {
        $items = User::with('roles')
            ->orderBy('id', 'desc')
            ->get();
    

        return response()->json([
            'success' => true,
            'message' => 'Success Get Data',
            'data' => $items,
        ]);
    }


    public function destroy(Request $request){
        try {
            $id = $request->id;
            $item = User::find($id);
    
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }
    
            $item->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Success message and data deleted',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
