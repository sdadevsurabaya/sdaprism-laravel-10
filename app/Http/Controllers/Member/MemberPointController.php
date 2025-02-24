<?php

/**
 * author : Suryo Atmojo <suryoatm@gmail.com>
 * project : Supresso Laravel
 * Start-date : 19-09-2022
 */

namespace App\Http\Controllers\Member;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Claim_uniquecode;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Member as MemberModel;

class MemberPointController extends Controller
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {
        if (isset($request->kode)) {
            $user = Auth::user();
            $memberId = MemberModel::where('email', $user->email)->first()->id;
            // $memberId = MemberModel::where('email', $userEmail)->first();

            $kode = $request->kode;
            $iduser = $memberId;

            $response = DB::select('select * from onepoint_uniquecode where kode="' . $kode . '"');

            if (empty($response)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode invalid.'
                ], 200);
            }

            $expiredDate = Carbon::parse($response[0]->expired_date)->endOfDay();

            if ($expiredDate < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode Telah Expired.'
                ], 200);
            }

            $StatusUniquecode = $response[0]->status;
            $PointUniquecode = $response[0]->point;
            $PointUniquecodeId = $response[0]->id;


            if (empty($StatusUniquecode)) {
                DB::update('UPDATE onepoint_uniquecode SET status = "used" WHERE kode="' . $kode . '"');

                ##insert perulangan untuk claim uniquecode dengan metode kupon 1 point
                // for ($p=0; $p < $PointUniquecode; $p++) { 
                //     $Onepoint_voucher = Claim_uniquecode::create(['id_member' => $iduser, 'id_uniquecode' => $kode, 'point' => 1]);
                // }

                ##insert untuk claim uniquecode dengan metode value point sesuai dengan point kupon             
                $Onepoint_voucher = Claim_uniquecode::create(['id_member' => $iduser, 'id_uniquecode' => $PointUniquecodeId, 'point' => $PointUniquecode]);

                $pointMember =  DB::select('select sum(point) as point from onepoint_log_claim_uniquecode as m where id_member = "' . $iduser . '"');

                $findpointused = DB::select('select sum(v.pointneed) as pointused from onepoint_log_claim_voucher as c
                inner join onepoint_voucher as v on v.id = c.id_onepoint_voucher
                where c.id_member = "' . $iduser . '"');

                $point = $pointMember[0]->point;
                $pointused = $findpointused[0]->pointused;

                $totalPoint = $point - $pointused;
                $titleNotif = 'Klaim Poin';
                $bodyNotif = 'Anda Berhasil Mendapatkan ' . $PointUniquecode . ' Poin';


                $notificationData = [
                    'member_id' => $memberId,
                    'notification_date' => Carbon::now(),
                    'notification_title' => $titleNotif,
                    'notification_content' => $bodyNotif,
                    'notification_status' => 'Unread',
                ];

                $notification = Notification::create($notificationData);

                return response()->json([
                    'success' => true,
                    'kode' => $response[0]->kode,
                    'point' => $totalPoint,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode Sudah Terpakai.'
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kode Invalid.'
            ], 200);
        };
    }
}
