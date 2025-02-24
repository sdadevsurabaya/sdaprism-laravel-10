<?php

namespace App\Http\Controllers\Api\Uniquecode;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Claim_uniquecode;

class GetUniquecodeController extends Controller
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

    public function index(Request $request)
    {
        if (isset($request->kode)) {
           
            $kode = $request->kode;
            $iduser = $request->iduser;
            $response =  DB::select('select * from onepoint_uniquecode where kode="'.$kode.'"');

            $StatusUniquecode = $response[0]->status;
            $PointUniquecode = $response[0]->point;
            

            if (empty($StatusUniquecode)) {
                DB::update('UPDATE onepoint_uniquecode SET status = "used" WHERE kode="'.$kode.'"');
                
                ##insert perulangan untuk claim uniquecode dengan metode kupon 1 point
                // for ($p=0; $p < $PointUniquecode; $p++) { 
                //     $Onepoint_voucher = Claim_uniquecode::create(['id_member' => $iduser, 'id_uniquecode' => $kode, 'point' => 1]);
                // }

                ##insert untuk claim uniquecode dengan metode value point sesuai dengan point kupon             
                    $Onepoint_voucher = Claim_uniquecode::create(['id_member' => $iduser, 'id_uniquecode' => $kode, 'point' => $PointUniquecode]);
                
                return response()->json([
                    'success' => true,
                    'id' => $response[0]->id,
                    'kode' => $response[0]->kode,
                    'point' => $response[0]->point,
                    'status' => $response[0]->status,
                    'deleted' => $response[0]->deleted,
                    'created_at' => $response[0]->created_at,
                    'updated_at' => $response[0]->updated_at,
                ]);
            }
            else
            {
                return response()->json([
                    'kode' => $response[0]->kode,
                    'status' =>$StatusUniquecode,
                    'success' => false,
                    'message' => 'Uniquecode already used.'], 200);
            }

           
        }
        else
        {
            return response()->json([
                'success' => false,
                'message' => 'Uniquecode are invalid.'], 200);
        };

        $uniquecode = [];
        
        for ($m = 0; $m < count($response); $m++) {
            $res = $response[$m];
            array_push($uniquecode, $res);
        }
        return $uniquecode;
    
    }

    public function getByFilter(Request $request)
    {

        $response =  DB::select('select * from merchandise_product_models');
        return $response;
    }
}