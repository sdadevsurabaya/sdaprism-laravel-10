<?php

namespace App\Http\Controllers\Front\Jne;

use App\Models\Jne;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetCityController extends Controller
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
        $getJneCity = Jne::select('city_name')->where('province_name', $province)->distinct()->pluck('city_name')->toArray();


        return response()->json([
            'success' => true,
            'message' => 'Get ALL City',
            'data' => $getJneCity
        ], 200);
    }
}
