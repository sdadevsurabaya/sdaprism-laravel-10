<?php

namespace App\Http\Controllers\Api\Recommend;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ItemRecommendation;

class GetRecommendController extends Controller
{
    public function index(Request $request)
    {
        $data = ItemRecommendation::all();
        return response()->json([
            'success' => true,
            'message' => 'Get Recommend All',
            'data' => $data,
        ], 200);
    }
}
