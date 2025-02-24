<?php

namespace App\Http\Controllers\Back\Onepoint_voucher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Claim_voucher;
use Illuminate\Support\Facades\DB;
use Hash;
use Illuminate\Support\Arr;

class Onepoint_voucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $data = DB::table('onepoint_voucher as v')
            ->select('v.*')
            ->selectSub(function ($query) {
                $query->from('onepoint_log_claim_voucher as cv')
                    ->whereColumn('cv.id_onepoint_voucher', 'v.id')
                    ->selectRaw('COUNT(*)');
            }, 'claimed')
            ->get();



        $data = [];
        $response =  DB::select('select * from onepoint_merchant as m');
        for ($m = 0; $m < count($response); $m++) {

            $res = $response[$m];
            // $responsevouchers =  DB::select('select *,(select count(*) from onepoint_log_claim_voucher where onepoint_log_claim_voucher.id_onepoint_voucher = m.id) as claimed from onepoint_voucher as m where m.id_merchant = '.$res->id.' and deleted = "false" and ((NOW() >= m.date_start) AND ((NOW() - INTERVAL 1 DAY) <= m.date_end))');
            $responsevouchers =  DB::select('select *,(select count(*) from onepoint_log_claim_voucher where onepoint_log_claim_voucher.id_onepoint_voucher = m.id) as claimed from onepoint_voucher as m where m.id_merchant = ' . $res->id . ' and deleted = "false" ');
            $res->vouchers = $responsevouchers;
            array_push($data, $res);
        }

        $merchant = $response;
        //dd($data);
        return view("back.Onepoint_voucher.index", compact("data", "merchant"))
            ->with("i", ($request->input("page", 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        return view("back.Onepoint_voucher.create");
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        $input = $request->all();
        $Onepoint_voucher = Voucher::create($input);
        return redirect()->route("onepoint_voucher.index")
            ->with("success", "Onepoint_voucher created successfully");
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $Onepoint_voucher = Voucher::find($id);
        return view("back.Onepoint_voucher.show", compact("Onepoint_voucher"));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $Onepoint_voucher = Voucher::find($id);
        return view("back.Onepoint_voucher.edit", compact("Onepoint_voucher"));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {

        $input = $request->all();
        $Onepoint_voucher = Voucher::find($id);
        $Onepoint_voucher->update($input);

        return redirect()->route("onepoint_voucher.index")
            ->with("success", "Onepoint_voucher updated successfully");
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $Onepoint_voucher = Voucher::find($id);
        $input["deleted"] =  'true';
        $Onepoint_voucher->update($input);

        return redirect()->route("onepoint_voucher.index")
            ->with("success", "Onepoint_voucher deleted successfully");
    }
}
