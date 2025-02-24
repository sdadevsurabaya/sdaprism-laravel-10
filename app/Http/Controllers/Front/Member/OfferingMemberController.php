<?php

namespace App\Http\Controllers\Front\Member;

use App\Models\ItemOffering;
use App\Models\ItemRecommendation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Member as MemberModel;

class OfferingMemberController extends Controller
{
    public function index()
    {

        $user = Auth::user();
        $fullUrl = url('/');
        $today = now();
        $memberId = MemberModel::where('email', $user->email)->first()->id;

        $Offering = ItemOffering::where(function ($query) use ($memberId) {
            $query->where('id_member', $memberId)
                ->orWhereNull('id_member')
                ->orWhere('id_member', '');
        })
            ->with(['produk' => function ($query) use ($fullUrl) {
                $query->select('kategori', 'namaproduk', 'kemasan', 'harga', 'id', 'url')
                    ->selectRaw("CASE WHEN LENGTH(gambar) > 0 THEN CONCAT('$fullUrl', '/files/', gambar) ELSE NULL END AS gambar");
                // ->selectRaw("CASE WHEN LENGTH(gambar1) > 0 THEN CONCAT('$fullUrl', '/files/', gambar1) ELSE NULL END AS gambar1")
                // ->selectRaw("CASE WHEN LENGTH(gambar2) > 0 THEN CONCAT('$fullUrl', '/files/', gambar2) ELSE NULL END AS gambar2")
                // ->selectRaw("CASE WHEN LENGTH(gambar3) > 0 THEN CONCAT('$fullUrl', '/files/', gambar3) ELSE NULL END AS gambar3");
            }])
            ->where(function ($query) use ($today) {
                $query->where('recom_start_date', '<=', $today)
                    ->where('recom_end_date', '>=', $today);
            })
            ->where('status', 'active')
            ->get();
        $transformedOffering = $Offering->pluck('produk')->flatten()->all();


        // dd($transformedRecomended);
        return  view('front.members.offering', compact('transformedOffering'));
    }
}
