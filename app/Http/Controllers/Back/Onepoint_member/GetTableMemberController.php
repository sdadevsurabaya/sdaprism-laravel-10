<?php

namespace App\Http\Controllers\Back\Onepoint_member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class GetTableMemberController extends Controller
{
    public function index()
    {
        $data = Member::with('user')->orderBy("id", "DESC")->get();
        return response()->json([
            'success' => true,
            'message' => 'Success Get Data',
            'data' => $data,
        ]);
    }


    public function addmember()
    {
        // $data = Member::with('user')->orderBy("id", "DESC")->get();
        $data = Member::with('user')
    ->whereHas('user', function ($query) {
        $query->where('name', '!=', 'null');
    })
    ->orderBy("id", "DESC")
    ->get();
        return response()->json([
            'success' => true,
            'message' => 'Success Get Data',
            'data' => $data,
        ]);
    }


    public function destroy(Request $request)
    {
        try {
            $id = $request->id;
            $item = Member::find($id);

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
