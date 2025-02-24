<?php
/**
 * author : Suryo Atmojo <suryoatm@gmail.com>
 * project : Supresso Laravel
 * Start-date : 19-09-2022
 */

namespace App\Http\Controllers\Member;

use App\Models\Member_model;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;

use App\Models\User;


use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Vend\VendController;

class MemberOrderController extends Controller
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
       #get data member web dari database
        $resmember_models =  DB::table('member_models')
            ->get();
  
        #restructur data, mencari status vend apakah memebr web sudah ada di vend atau belum
        $dataMember = [];
        for ($i = 0; $i < count($resmember_models); $i++) {
            $dataMember = $resmember_models;

            if ($resmember_models[$i]->id_users_login !=null) {
                $res_order_member = DB::select("SELECT * from order_models where iduser =".$resmember_models[$i]->id_users_login." Order By tanggalorder desc");
           
                $dataMember[$i]->order = $res_order_member;
              
            }
            else{
                $dataMember[$i]->order = [];
               
            } 
        }

        $member_models =  $dataMember;
        // dd($member_models[0]);
      
        return view('members/ordermember', compact('member_models'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

}
