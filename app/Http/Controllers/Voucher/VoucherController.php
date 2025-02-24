<?php

namespace App\Http\Controllers\Voucher;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class VoucherController extends Controller
{
    public function index(Request $request)
    {

        $vouchers = [];
        $response =  DB::select('select merchant_name, id from onepoint_merchant as m');
        for ($m = 0; $m < count($response); $m++) {

            $res = $response[$m];
            $responsevouchers =  DB::select('SELECT id, label, pointneed, date_end, short_desc, qtyvoucher,
            (SELECT COUNT(*) FROM onepoint_log_claim_voucher WHERE onepoint_log_claim_voucher.id_onepoint_voucher = m.id) AS claimed
            FROM onepoint_voucher AS m
            WHERE m.id_merchant = ' . $res->id . ' 
            AND deleted = "false" AND type_flag = "claim" 
            AND ((NOW() >= m.date_start) AND ((NOW() - INTERVAL 1 DAY) <= m.date_end))
            HAVING qtyvoucher - claimed > 0');
            foreach ($responsevouchers as $voucher) {
                $voucher->voucher_persen = round(($voucher->claimed / $voucher->qtyvoucher) * 100, 2);
            }
            $res->vouchers = $responsevouchers;
            array_push($vouchers, $res);
        }

        $searchTerm = $request->search;


        $filteredVouchers = collect($vouchers)->filter(function ($voucher) use ($searchTerm) {
            if (is_object($voucher) && property_exists($voucher, 'vouchers')) {

                if (is_array($voucher->vouchers) && count($voucher->vouchers) > 0) {
                    foreach ($voucher->vouchers as $singleVoucher) {

                        if (
                            stripos($singleVoucher->label, $searchTerm) !== false ||
                            stripos($singleVoucher->short_desc, $searchTerm) !== false
                        ) {
                            return true;
                        }
                    }
                }
            }
            return false;
        });

        if ($searchTerm) {
            $vouchers = $filteredVouchers;
        } else {
            $vouchers =  $vouchers;
        }

        // dd($vouchers);
        $buttonClaim = true;
        return view('page-sdamember.voucher', compact(
            'vouchers',
            'buttonClaim'
        ));
    }
}
