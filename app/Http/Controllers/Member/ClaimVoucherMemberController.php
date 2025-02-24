<?php

namespace App\Http\Controllers\Member;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Models\Claim_voucher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Member as MemberModel;
use App\Models\Notification;

class ClaimVoucherMemberController extends Controller
{
    public function index(Request $request)
    {

        $user = Auth::user();
        $userEmail = $user->email;
        $memberId = MemberModel::where('email', $userEmail)->first()->id;

        $response =  DB::select('select sum(point) as point from onepoint_log_claim_uniquecode as m where id_member = "' . $memberId . '"');
        $findpointused = DB::select('select sum(v.pointneed) as pointused from onepoint_log_claim_voucher as c
        inner join onepoint_voucher as v on v.id = c.id_onepoint_voucher
        where c.id_member = "' . $memberId . '"');

        $point = $response[0]->point;
        $pointused = $findpointused[0]->pointused;

        $totalUserPoint = $point - $pointused;

        $voucherPoint = Voucher::findOrFail($request->id);
        $id = $request->id;
        $data =
            DB::select('SELECT *, (SELECT COUNT(*) FROM onepoint_log_claim_voucher 
                WHERE onepoint_log_claim_voucher.id_onepoint_voucher = m.id) as claimed 
                FROM onepoint_voucher as m WHERE deleted = "false"AND m.id = ?
                AND ((NOW() >= m.date_start)AND ((NOW() - INTERVAL 1 DAY) <= m.date_end))', [$id]);


        $claim = $data[0]->claimed;
        $CheckQtyVoucher = $voucherPoint->qtyvoucher - $claim;

        if ($CheckQtyVoucher < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher Habis'
            ], 200);
        }

        if ($totalUserPoint >= $voucherPoint->pointneed) {
            $randomNumber = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
            $input['id_member'] = $memberId;
            $input['kode_voucher'] = $voucherPoint->kode_voucher . '-' . $randomNumber;
            $input['id_onepoint_voucher'] = $voucherPoint->id;
            $input['flag_used'] = 'false';
            $addClaimVoucher = Claim_voucher::create($input);

            $responseMobile =  DB::select('select sum(point) as point from onepoint_log_claim_uniquecode as m where id_member = "' . $memberId . '"');
            $findpointusedMobile = DB::select('select sum(v.pointneed) as pointused from onepoint_log_claim_voucher as c
            inner join onepoint_voucher as v on v.id = c.id_onepoint_voucher
            where c.id_member = "' . $memberId . '"');

            $pointMobile = $responseMobile[0]->point;
            $pointusedMobile = $findpointusedMobile[0]->pointused;

            $totalUserPointMobile = $pointMobile - $pointusedMobile;
            $voucherCount = Claim_voucher::where('id_member', $memberId)
            ->where('onepoint_log_claim_voucher.status', 'active')
            ->where('onepoint_log_claim_voucher.flag_used', 'false')
            ->join('onepoint_voucher', 'onepoint_log_claim_voucher.id_onepoint_voucher', '=', 'onepoint_voucher.id')
            ->count();

            $getVoucherJoin = Voucher::with('merchant')->where('onepoint_voucher.id', $request->id)->get()->toArray();


            $bodyNotif = 'Klaim Voucher ' .  $getVoucherJoin[0]['merchant']['merchant_name'] .  ' ' . $getVoucherJoin[0]['label'] . ' Berhasil';
            $titleNotif = 'Klaim Voucher';
            $notificationData = [
                'member_id' => $memberId,
                'notification_date' => Carbon::now(),
                'notification_title' => $titleNotif,
                'notification_content' => $bodyNotif,
                'notification_status' => 'Unread',
            ];

            $notification = Notification::create($notificationData);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Voucher Success Claim',
                    'voucherCount' => $voucherCount,
                    'point' => $totalUserPointMobile,
                ],
                200
            );
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Poin tidak mencukupi'
            ], 200);
        }
    }
}
