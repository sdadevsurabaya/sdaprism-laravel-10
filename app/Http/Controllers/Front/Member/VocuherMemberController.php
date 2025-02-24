<?php

namespace App\Http\Controllers\Front\Member;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Member as MemberModel;

class VocuherMemberController extends Controller
{
    public function index(Request $request)
    {

        $user = Auth::user();
        $userEmail = $user->email;
        $memberId = MemberModel::where('email', $userEmail)->first();

        $iduser = $memberId->id;
        $emailuser = $memberId->email;
        $idmerchant = $request->idmerchant;

        $merchant = [];


        $response =  DB::select('select id, merchant_name from onepoint_merchant as m');

        if (!empty($idmerchant)) {
            $response =  DB::select('select id, merchant_name from onepoint_merchant as m where id=' . $idmerchant);
        }

        #get date minus one day
        $now = new DateTime(); // Create a DateTime object for the current date and time
        $now->modify('-1 day'); // Add one day to the current date and time
        $formattedDate = $now->format('Y-m-d H:i:s');


        for ($m = 0; $m < count($response); $m++) {

            $res = $response[$m];
            $vouchers = [];
            // $responsevouchers =  DB::select('select * from onepoint_voucher as m where m.id_merchant = '.$res->id);
            $voucherMember = DB::table('onepoint_log_claim_voucher')
                ->join('onepoint_member', 'onepoint_log_claim_voucher.id_member', '=', 'onepoint_member.id')
                ->join('onepoint_voucher', 'onepoint_log_claim_voucher.id_onepoint_voucher', '=', 'onepoint_voucher.id')
                ->where('onepoint_voucher.id_merchant', '=', $res->id)
                ->where('onepoint_voucher.date_start', '<=', date('Y-m-d H:i:s'))
                ->where('onepoint_voucher.date_end', '>=', $formattedDate)
                ->where('onepoint_log_claim_voucher.flag_used', '=', 'false')
                // ->where('(NOW() - INTERVAL 1 DAY)', '<=', 'onepoint_voucher.date_end')
                ->where(
                    function ($query) use ($iduser, $emailuser) {
                        $query->where('onepoint_member.email', '=', $emailuser);
                        $query->orWhere('onepoint_member.id', '=', $iduser);
                    }

                )
                ->select(
                    'onepoint_log_claim_voucher.id',
                    'onepoint_voucher.label',
                    'onepoint_log_claim_voucher.kode_voucher',
                    'onepoint_voucher.pointneed',
                    'onepoint_voucher.short_desc',
                    'onepoint_voucher.date_end',

                )
                // ->selectRaw("DATE_FORMAT(onepoint_voucher.date_end, '%e %M %Y') AS formatted_date")

                ->get();

            if (!$voucherMember->isEmpty()) {
                $res->vouchers = $voucherMember;
                $merchant[] = $res;
                // $res->vouchers = $voucherMember;
                // array_push($vouchers, $res);
            }
            // $res->vouchers = $voucherMember;
            // array_push($merchant, $res);
        }

        //and ((NOW() >= m.date_start) AND ((NOW() - INTERVAL 1 DAY) <= m.date_end))

        // dd($merchant);
        $vouchers = $merchant;
        $buttonClaim = false;

        return view('page-sdamember.voucher', compact(
            'vouchers',
            'buttonClaim'
        ));
    }
}
