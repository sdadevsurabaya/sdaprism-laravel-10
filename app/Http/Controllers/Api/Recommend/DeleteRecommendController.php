<?php

namespace App\Http\Controllers\Api\Recommend;


use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\ItemRecommendation;
use App\Http\Controllers\Controller;

class DeleteRecommendController extends Controller
{
    public function index(Request $request)
    {
        try {
            $recommend = ItemRecommendation::findOrFail($request->id);
            $recommend->update(['deleted' => Carbon::now()]);
            return response()->json([
                'success' => true,
                'message' => 'Success Delete records Recommend.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 200);
        }
    }
}
