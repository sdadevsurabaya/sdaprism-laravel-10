<?php

namespace App\Http\Controllers\Front\Jne;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class GetProvinceController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $provinces = [];
        $response =  DB::select('select province_name from jne_api group by province_name order by province_name');
        for ($m = 0; $m < count($response); $m++) {
            $res = $response[$m];
            array_push($provinces, $res->province_name);
        }

        // get data general all data
        return response()->json([
            'success' => true,
            'message' => 'Get All Province ',
            'data' => $provinces
        ], 200);
    }
}
