<?php

namespace App\Http\Controllers\Back\OnePopularSearch;

use App\Http\Controllers\Controller;
use App\Models\PopularSearch;
use Illuminate\Http\Request;

class GetTablePopularSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = PopularSearch::whereNull('deleted')
            ->orderBy('search_count', 'desc')
            ->get();
    

        return response()->json([
            'success' => true,
            'message' => 'Success message and data added',
            'data' => $items,
        ]);
    }


    public function destroy(Request $request){
        $id = $request->id;
        $item = PopularSearch::find($id);
        $item->deleted = now();
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Success message and data deleted',
        ]);
    }
}
