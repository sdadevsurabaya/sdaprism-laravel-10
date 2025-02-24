<?php

namespace App\Http\Controllers\Front;


use App\Models\Product_model;
use App\Models\Product_collection_model;
use App\Models\Product_type_model;
use App\Models\Product_form_model;
use App\Models\Product_package_model;
use App\Models\File;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FrontExploreController extends Controller
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

        $product_Aceh_Gayo = [];
        $product_Sumatra_Mandheling = [];
        $product_Sumatra_Mandheling_Rainforest = [];
        $product_Lampung = [];
        $product_Peaberry = [];
        $product_Manglayang = [];
        $product_West_Java = [];
        $product_Java = [];
        $product_Bali_Kintamani = [];
        $product_Flores_Bajawa = [];
        $product_Toraja_Kalosi = [];
        $product_Papua = [];

        $product_models = [];
        // $product_models_res =  DB::table('product_models')->where('deleted', 'false')
        $product_models_res =  DB::select('select pm.*,pcm.product_collection_name, ptm.product_type_name,
                pfm.product_form_name,
                ppm.product_package_name,
            (select discount_models.discount from discount_models 
            LEFT JOIN discount_cluster_models as dcm on discount_models.discount_cluster_id = dcm.id
            where  (NOW() >= dcm.active_date AND NOW() <= dcm.off_date) and discount_models.product_id = pm.id and discount_models.status ="active"
						 ) 
						as disc_event,
						(select dcm.nama from discount_models 
            LEFT JOIN discount_cluster_models as dcm on discount_models.discount_cluster_id = dcm.id
            where  (NOW() >= dcm.active_date AND NOW() <= dcm.off_date) and discount_models.product_id = pm.id and discount_models.status ="active"
						 ) 
						as event_promo
            from product_models as pm
             LEFT JOIN product_collection_models as pcm on pcm.id = pm.product_collection
            LEFT JOIN product_type_models as ptm on ptm.id = pm.product_type
            LEFT JOIN product_form_models as pfm on pfm.id = pm.product_form
            LEFT JOIN product_package_models as ppm on ppm.id = pm.product_package
            ');

        for ($p = 0; $p < count($product_models_res); $p++) {
            $prdct = $product_models_res[$p];
            if (!empty($prdct->fileimages)) {
                $prdct->images = json_decode($product_models_res[$p]->fileimages);
            } else {
                $prdct->images = null;
            }
            if (!empty($prdct->disc_event)) {
                $prdct->product_price_after_disc =   ($prdct->product_price) - (($prdct->product_price) * ($prdct->disc_event) / 100);
            } else {
                $prdct->product_price_after_disc =  $prdct->product_price;
            }

            // dump($prdct->product_name);
            // dd((strpos($prdct->product_name, 'Sumatra Mandheling d'))!==false);

            array_push($product_models, $prdct);
            $str = $prdct->product_name;
            if ((strpos($prdct->product_name, 'Aceh Gayo')) !== false) {
                // $str = $str . " Aceh";
                //dump($str);
                array_push($product_Aceh_Gayo, $prdct);
            } else if (strpos($prdct->product_name, "Rainforest") !== false) {
                // $str = $str . " Sumatra Mandheling";
                //dump($str);
                array_push($product_Sumatra_Mandheling_Rainforest, $prdct);
            } else if (strpos($prdct->product_name, "Sumatra Mandheling") !== false) {
                // $str = $str . " Sumatra Rainforest";
                //dump($str);
                array_push($product_Sumatra_Mandheling, $prdct);
            } else if (strpos($prdct->product_name, "Lampung") !== false) {
                // $str = $str . " lampung";
                //dump($str);
                array_push($product_Lampung, $prdct);
            } else if (strpos($prdct->product_name, "Peaberry") !== false) {
                // $str = $str . " peaberry";
                //dump($str);
                array_push($product_Peaberry, $prdct);
            } else if (strpos($prdct->product_name, "Manglayang") !== false) {
                // $str = $str . " manglayang";
                //dump($str);
                array_push($product_Manglayang, $prdct);
            } else if (strpos($prdct->product_name, "West Java") !== false) {
                // $str = $str . " west java";
                //dump($str);
                array_push($product_West_Java, $prdct);
            } else if (strpos($prdct->product_name, "Java") !== false) {
                // $str = $str . " java";
                //dump($str);
                array_push($product_Java, $prdct);
            } else if (strpos($prdct->product_name, "Bali Kintamani") !== false) {
                // $str = $str . " bali kintamani";
                //dump($str);
                array_push($product_Bali_Kintamani, $prdct);
            } else if (strpos($prdct->product_name, "Flores Bajawa") !== false) {
                // $str = $str . " flores";
                //dump($str);
                array_push($product_Flores_Bajawa, $prdct);
            } else if (strpos($prdct->product_name, "Toraja Kalosi") !== false) {
                // $str = $str . " toraja";
                //dump($str);
                array_push($product_Toraja_Kalosi, $prdct);
            } else if (strpos($prdct->product_name, "Papua") !== false) {
                // $str = $str . " papua";
                //dump($str);
                array_push($product_Papua, $prdct);
            } else {
                // $str = $str . " unidentified";
                //dump($str);
                //dump($prdct->product_name);
            }
        }

        // dump("product_Aceh_Gayo");
        // dump($product_Aceh_Gayo);
        // dump("product_Sumatra_Mandheling");
        // dump($product_Sumatra_Mandheling);
        // dump("product_Sumatra_Mandheling_Rainforest");
        // dump($product_Sumatra_Mandheling_Rainforest);
        // dump("product_Lampung");
        // dump($product_Lampung);
        // dump("product_Peaberry");
        // dump($product_Peaberry);
        // dump("product_Manglayang");
        // dump($product_Manglayang);
        // dump("product_West_Java");
        // dump($product_West_Java);
        // dump("product_Java");
        // dump($product_Java);
        // dump("product_Bali_Kintamani");
        // dump($product_Bali_Kintamani);
        // dump("product_Flores_Bajawa");
        // dump($product_Flores_Bajawa);
        // dump("product_Toraja_Kalosi");
        // dump($product_Toraja_Kalosi);
        // dump("product_Papua");
        // dump($product_Papua);

       
        // dump(strpos("Aceh Gayo Coffee Capsules", "Acehg"));
        // dd($product_models);

        $title = "Explore";
        $pages = "explore";
        return view('front/explore', compact('title', 'pages', 'product_models','product_Aceh_Gayo',
        'product_Sumatra_Mandheling',
        'product_Sumatra_Mandheling_Rainforest',
        'product_Lampung',
        'product_Peaberry',
        'product_Manglayang',
        'product_West_Java',
        'product_Java',
        'product_Bali_Kintamani',
        'product_Flores_Bajawa',
        'product_Toraja_Kalosi',
        'product_Papua'));
    }
}
