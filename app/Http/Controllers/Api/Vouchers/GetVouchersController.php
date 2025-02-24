<?php

namespace App\Http\Controllers\Api\Vouchers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Voucher;
use App\Models\Claim_voucher;
use App\Models\Merchant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GetVouchersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index()
    {
        // $response =  DB::select('select * from onepoint_voucher');
        // $voucher = DB::table('onepoint_voucher')
        // ->join('onepoint_merchant', 'onepoint_voucher.id_merchant', '=', 'onepoint_merchant.id')
        // ->select('*')
        // ->get();

        $merchant = [];
        $response =  DB::select('select * from onepoint_merchant as m');
        for ($m = 0; $m < count($response); $m++) {

            $res = $response[$m];
            $responsevouchers =  DB::select('select *,(select count(*) from onepoint_log_claim_voucher where onepoint_log_claim_voucher.id_onepoint_voucher = m.id) as claimed from onepoint_voucher as m where m.id_merchant = ' . $res->id . ' and deleted = "false" and ((NOW() >= m.date_start) AND ((NOW() - INTERVAL 1 DAY) <= m.date_end))');
            $res->vouchers = $responsevouchers;
            array_push($merchant, $res);
        }

        return response()->json([
            'success' => true,
            'vouchers' => $merchant,
        ]);
    }

    public function getByFilter(Request $request)
    {

        $response =  DB::select('select * from merchandise_product_models');
        return $response;
    }


    public function IndexMobile()
    {
        // $response =  DB::select('select * from onepoint_voucher');
        // $voucher = DB::table('onepoint_voucher')
        // ->join('onepoint_merchant', 'onepoint_voucher.id_merchant', '=', 'onepoint_merchant.id')
        // ->select('*')
        // ->get();

        $urlVoucher = url('/detail-voucher');

        $merchant = [];
        $response = DB::select('SELECT merchant_name, id FROM onepoint_merchant AS m WHERE deleted = "false"');
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
                $voucher->urlVoucher = $urlVoucher;
            }
            $res->vouchers = $responsevouchers;
            array_push($merchant, $res);
        }

        $totalVouchers = Voucher::where('deleted', 'false')
            ->whereDate('date_start', '<=', now())
            ->whereDate('date_end', '>=', now()->subDay())
            ->count();

        return response()->json([
            'success' => true,
            'totalVouchers' => $totalVouchers,
            'vouchers' => $merchant,
        ]);
    }
}
