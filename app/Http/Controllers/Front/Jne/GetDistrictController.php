<?php

namespace App\Http\Controllers\Front\Jne;

use App\Models\Jne;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetDistrictController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $province = $request->province;
        $city = $request->city;

        $getJneDistrict = Jne::select('district_name')->where('province_name', $province)->where('city_name', $city)->distinct()->pluck('district_name')->toArray();

        return response()->json(
            [
                'success' => true,
                'message' => 'Get ALL District',
                'data' => $getJneDistrict
            ],
            200
        );
    }
}
