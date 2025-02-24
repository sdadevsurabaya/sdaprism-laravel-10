<?php

namespace App\Http\Controllers\Member;

use App\Models\Member_model;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Auth;

class MemberBoardController extends Controller
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


        $title = "Account";
        $pages = "user";
       
        $userId = auth()->user()->id; 
        $member_models =  DB::select("select id from member_models where id_users_login = ".$userId);
        $user_id = $member_models[0]->id;
        //dump($userId);
        $GetMember_res =  DB::select('select * from member_models where id_users_login = ' . $userId);
        //dd($GetMember_res);
       

        if (empty($GetMember_res)) {
            return view('front/members/pages-500');
        } else {

 $GetMember = $GetMember_res[0];
            $GetOrder_res =  DB::select('select om.nomerorder,om.status,mm.id, mm.id_users_login, mm.username, mm.firstname,  om.tanggalorder,om.itemsubtotal
            ' .
                // ,odm.namaproduk, odm.subtotalproduk, odm.review
                'from member_models as mm
            inner JOIN order_models as om on mm.id = om.iduser
            where ' .
                // -- and DATE(om.tanggalorder) > '2022-09-01' 
                'om.iduser = ' . $user_id . ' order by om.tanggalorder asc');
            // dd($GetOrder_res);
            $GetOrderDetail_res =  DB::select('select om.nomerorder,om.status,mm.id, mm.id_users_login, mm.username, mm.firstname,  om.tanggalorder,om.itemsubtotal
            ,odm.namaproduk, odm.subtotalproduk, odm.review
            from member_models as mm
            inner JOIN order_models as om on mm.id = om.iduser
            inner JOIN order_detail_models as odm on om.nomerorder = odm.nomerorder
            where om.status="complete"' .
                // -- and DATE(om.tanggalorder) > '2022-09-01' 
                'and mm.id_users_login = ' . $userId . ' order by DATE(om.tanggalorder),om.nomerorder asc');

            $ordersarray = [];
            for ($o = 0; $o < count($GetOrder_res); $o++) {
                $orders = $GetOrder_res[$o];
                $GetOrderDetail =  DB::select('select * from order_detail_models as odm where odm.nomerorder = ' . "'" . $GetOrder_res[$o]->nomerorder . "'");
                $orders->detailorder = $GetOrderDetail;
                array_push($ordersarray, $orders);
            }

            ##GetHistoryRedeem
//             $GetHistoryRedeem_res =  DB::select('select mrl.id,mrl.created_at as tanggalredeem, mrl.iduser,mrl.idproduct_redeem, pm.product_name as namaproduk,
// 						((select SUM(odm.subtotalproduk)  from member_models as mm
//             inner JOIN order_models as om on mm.id = om.iduser
//             inner JOIN order_detail_models as odm on om.nomerorder = odm.nomerorder
//             where om.status="complete" and DATE(om.tanggalorder) <= DATE(mrl.created_at)  and mm.id_users_login = mrl.iduser) 
// 						-
// 						(select IFNULL(SUM(mrlg.member_point_redeem),0) from member_redeem_log_models as mrlg where mrlg.iduser = mrl.iduser and  DATE(mrlg.created_at) < DATE(mrl.created_at))
// 						)
// 						as total_point,
// 						mrl.member_point_redeem,
// -- 						(select IFNULL(SUM(mrlg.member_point_redeem),0) from member_redeem_log_models as mrlg where  DATE(mrlg.created_at) < DATE(mrl.created_at)) as historyredeempoint,
// 						((select SUM(odm.subtotalproduk)  from member_models as mm
//             inner JOIN order_models as om on mm.id = om.iduser
//             inner JOIN order_detail_models as odm on om.nomerorder = odm.nomerorder
//             where om.status="complete" and DATE(om.tanggalorder) <= DATE(mrl.created_at)  and mm.id_users_login = mrl.iduser)
// 						-
// 						(select SUM(mrlog.member_point_redeem) from member_redeem_log_models as mrlog where mrlog.iduser = mrl.iduser and  DATE(mrlog.created_at) <= DATE(mrl.created_at))) as finalpoint
// 						from member_redeem_log_models as mrl 
// 						LEFT JOIN product_models as pm on pm.id = mrl.idproduct_redeem
// 						where mrl.iduser = ' . $userId . ' order by DATE(mrl.created_at) asc');


                        $GetHistoryRedeem_res =  DB::select('select mrl.id,mrl.created_at as tanggalredeem, mrl.iduser,mrl.idproduct_redeem, pm.product_name as namaproduk,
						((select SUM(om.itemsubtotal)  from member_models as mm
            inner JOIN order_models as om on mm.id = om.iduser
            where om.status="complete" and DATE(om.tanggalorder) <= DATE(mrl.created_at)  and mm.id_users_login = mrl.iduser) 
						-
						(select IFNULL(SUM(mrlg.member_point_redeem),0) from member_redeem_log_models as mrlg where mrlg.iduser = mrl.iduser and  DATE(mrlg.created_at) < DATE(mrl.created_at))
						)
						as total_point,
						mrl.member_point_redeem,
-- 						(select IFNULL(SUM(mrlg.member_point_redeem),0) from member_redeem_log_models as mrlg where  DATE(mrlg.created_at) < DATE(mrl.created_at)) as historyredeempoint,
						((select SUM(om.itemsubtotal)  from member_models as mm
            inner JOIN order_models as om on mm.id = om.iduser
            where om.status="complete" and DATE(om.tanggalorder) <= DATE(mrl.created_at)  and mm.id_users_login = mrl.iduser)
						-
						(select SUM(mrlog.member_point_redeem) from member_redeem_log_models as mrlog where mrlog.iduser = mrl.iduser and  DATE(mrlog.created_at) <= DATE(mrl.created_at))) as finalpoint
						from member_redeem_log_models as mrl 
						LEFT JOIN product_models as pm on pm.id = mrl.idproduct_redeem
						where mrl.iduser = ' . $userId . ' order by DATE(mrl.created_at) asc');
            $totaluserredeem = 0;


            for ($sr = 0; $sr < count($GetHistoryRedeem_res); $sr++) {
                $totaluserredeem = $totaluserredeem + $GetHistoryRedeem_res[$sr]->member_point_redeem;
            }

            if ($totaluserredeem <> 0) {
                $totalpoint = floor($GetHistoryRedeem_res[count($GetHistoryRedeem_res) - 1]->finalpoint);
                // dump("test1");
            } else {
                ##query tanpa order dari vend
            // $totalpoint = floor(DB::select('select SUM(odm.subtotalproduk) as totalpoint  from member_models as mm
            // inner JOIN order_models as om on mm.id = om.iduser
            // inner JOIN order_detail_models as odm on om.nomerorder = odm.nomerorder
            // where om.status="complete" and mm.id_users_login = ' . $userId)[0]->totalpoint);
                ##query dengan vend
            $totalpoint = floor(DB::select('select SUM(om.itemsubtotal) as totalpoint  from order_models as om where om.status="complete" and om.iduser = ' . $user_id)[0]->totalpoint);

            // dump("test2");
            }


            if ($totalpoint < 300) {
                $caption = 'Robusta';
            } else {
                $caption = 'Arabica';
            }

            // dd($totalpoint);

            //checkstate membership
            $GetStateMembership_res =  DB::select('select * from member_membership_state_models where iduser = ' . $userId . ' ORDER BY id DESC LIMIT 1');

            if (count($GetStateMembership_res) != 1) {
                //dd("State : Unknown");
            } else {
                //dd($GetStateMembership_res);
            }

            // dump($userId);
            // dd($GetOrder_res);

            return view('front/members/dashboard', compact('userId', 'caption', 'member_models', 'GetOrder_res', 'GetOrderDetail_res', 'ordersarray', 'totalpoint', 'GetHistoryRedeem_res', 'GetMember', 'title', 'pages'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
}
