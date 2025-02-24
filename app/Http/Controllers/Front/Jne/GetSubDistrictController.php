<?php

namespace App\Http\Controllers\Front\Jne;

use App\Models\Jne;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetSubDistrictController extends Controller
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
        $district = $request->district;

        $getJneSubDistrict = Jne::select('subdidstrict_name')->where('province_name', $province)->where('city_name', $city)->where('district_name', $district)->distinct()->pluck('subdidstrict_name')->toArray();
        $getJneZipCode = Jne::select('zip_code')->where('province_name', $province)->where('city_name', $city)->where('district_name', $district)->distinct()->pluck('zip_code')->toArray();

        $data = [
            'success' => true,
            'data' => $getJneSubDistrict,
            'GetZipCode' => $getJneZipCode,
        ];

        $zipCode = array_shift($data["GetZipCode"]);

        $data["GetZipCode"] = $zipCode;
        return $data;
    }
}
