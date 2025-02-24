<?php

namespace App\Http\Controllers\Api\Uniquecode;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GenerateUniquecodeController extends Controller
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
        date_default_timezone_set('Asia/Jakarta');
        for ($i=0; $i <2 ; $i++) { 
            $uniquecode = (date("y").date("m").date("d").date("h").date("i").rand(111111, 999999));
            $uniquecode = str_split($uniquecode, 4);
            $uniquecode = implode("-",$uniquecode);
            DB::insert("insert into onepoint_uniquecode(kode) values ('$uniquecode')");
        }
        // DB::insert()
        // $uniquecode = [];
        // $response =  DB::select('select * from onepoint_uniquecode');
        // for ($m = 0; $m < count($response); $m++) {
        //     $res = $response[$m];
        //     array_push($uniquecode, $res);
        // }
        // return $uniquecode;
    
    }

    public function getByFilter(Request $request)
    {

        $response =  DB::select('select * from merchandise_product_models');
        return $response;
    }
}