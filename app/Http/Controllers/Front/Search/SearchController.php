<?php

namespace App\Http\Controllers\Front\Search;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\HistorySearch;
use App\Models\PopularSearch;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Member as MemberModel;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $memberId = MemberModel::where('email', $user->email)->first()->id;

        $keyword = $request->keyword;
        if (empty($keyword)) {
            abort(404);
        }


        $popularSearch = PopularSearch::where('query', 'LIKE', "%$keyword%")->first();

        $history = HistorySearch::firstOrCreate([
            'id_member' => $memberId,
            'query' => $keyword,
        ]);

        if ($popularSearch) {

            $popularSearch->increment('search_count');
        } else {

            PopularSearch::create([
                'query' => $keyword,
                'search_count' => 1,
            ]);
        }

        $fullUrl = url('/');
        $data = Product::select('kategori', 'namaproduk', 'kemasan', 'harga', 'id_brand', 'id', 'url')->selectRaw("CASE WHEN LENGTH(gambar) > 0 THEN CONCAT('$fullUrl', '/files/', gambar) ELSE NULL END AS gambar")
            ->where('product_status', '1')
            ->where('deleted', 'false')
            ->where(function ($query) use ($keyword) {
                $query->whereHas('brand', function ($brandQuery) use ($keyword) {
                    $brandQuery->where('brand', 'LIKE', "%$keyword%");
                })
                    ->orWhere('namaproduk', 'LIKE', "%$keyword%")
                    ->orWhere('slug', 'LIKE', "%$keyword%");
            })
            ->get()->toArray();


        return  view('front.members.search', compact('data'));
    }
}
