<?php

namespace App\Http\Controllers\Api\Vouchers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GetVouchersMembersController extends Controller
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

    public function getAllVouchersByIdMembers(Request $request)
    {
        $vouchers = [];
        $idmembers = ($request->idmembers);
        //         dd('select mvm.iduser, mm.fullname, mm.email, mvm.idvouchers, vm.kodecoupon, vm.jenis_voucher,
        // IF(vm.nominalbulat=0,vm.nominalpersen, vm.nominalpersen) as nominal,
        // IF(vm.nominalbulat=0,"persen", "bulat") as type, 
        // vm.poinneed, mvm.startdate, mvm.expired 
        // from member_vouchers_models as mvm 
        // left join vouchers_models as vm on vm.id = mvm.idvouchers
        // left join member_models as mm on mm.id_users_login = mvm.iduser
        // where mvm.startdate <= CURRENT_DATE() and mvm.expired >=CURRENT_DATE()
        // and mvm.iduser = "5" order by mvm.id desc');
        //         $vouchers = [];
        $response =  DB::select('select mvm.id,mvm.iduser, mm.fullname, mm.email, mvm.idvouchers, vm.kodecoupon, vm.jenis_voucher,
IF(vm.nominalbulat=0,vm.nominalpersen, vm.nominalpersen) as nominal,
IF(vm.nominalbulat=0,"persen", "bulat") as type, 
vm.poinneed, mvm.startdate, mvm.expired 
from member_vouchers_models as mvm 
left join vouchers_models as vm on vm.id = mvm.idvouchers
left join member_models as mm on mm.id_users_login = mvm.iduser
where mvm.startdate <= CURRENT_DATE() and mvm.expired >=CURRENT_DATE()
and mvm.status = "unused" and mvm.iduser = ' . $idmembers . ' order by mvm.id asc');

        for ($v = 0; $v < count($response); $v++) {
            $vrch = $response[$v];

            array_push($vouchers, $vrch);
        }

        // dd($merchandise);
        return $vouchers;
    }

    public function getByFilter(Request $request)
    {

        $response =  DB::select('select * from merchandise_product_models');
        return $response;
    }
}