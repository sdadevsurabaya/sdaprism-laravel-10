<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MerchantController extends Controller
{
    public function index()
    {
        $fullUrl = url('/merchant') . '/';

        $merchants = Merchant::select('merchant_name', 'label', DB::raw("CONCAT('$fullUrl', image) as image"), 'url')->get();

        // dd($merchant);

        return view('page-sdamember.merchant', compact(
            'merchants',
        ));
    }
}
