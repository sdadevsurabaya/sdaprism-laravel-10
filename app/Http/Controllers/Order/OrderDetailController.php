<?php

namespace App\Http\Controllers\Order;


use App\Models\Order_model;
use App\Models\Order_detail_model;

use App\Models\File;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Vend\VendController;

class OrderDetailController extends Controller
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
        //dump("test detail order");

        $nomerorder = $request->id;
        $payment = $request->payment;
        //dump($nomerorder);
        //dump($payment);


        if ($payment == 'cash') {
            $res_allproductvend = (new VendController)->getallproduct();
            $allproductvend = ($res_allproductvend["data"]);
            //dump($allproductvend);
            //dump(gettype($res_allproductvend));

            $res_salesdetail = (new VendController)->getvendsalesbyinvoice($nomerorder);
            $salesdetail = $res_salesdetail["data"][0];
            $resorderdetail = $salesdetail["line_items"];

            //dump($res_salesdetail);
            //dump($salesdetail);
            //dump("====list item====");
            //dump($resorderdetail);

            $product_order = [];


            for ($p = 0; $p < count($resorderdetail); $p++) {
                // $orderprdct = $resorderdetail[$p];
                $orderprdct = new \StdClass();
                $sku  = $this->searchbyid($resorderdetail[$p]["product_id"], $allproductvend);

                $res_productdetail = DB::select('select pm.*, pcm.product_collection_name, ptm.product_type_name,
                pfm.product_form_name,
                ppm.product_package_name,
            (select discount_models.discount from discount_models 
            LEFT JOIN discount_cluster_models as dcm on discount_models.discount_cluster_id = dcm.id
            where  (NOW() >= dcm.active_date AND NOW() <= dcm.off_date) and discount_models.product_id = pm.id
						 ) 
						as disc_event,
						(select dcm.nama from discount_models 
            LEFT JOIN discount_cluster_models as dcm on discount_models.discount_cluster_id = dcm.id
            where  (NOW() >= dcm.active_date AND NOW() <= dcm.off_date) and discount_models.product_id = pm.id
						 ) 
						as event_promo
            from product_models as pm
            LEFT JOIN product_collection_models as pcm on pcm.id = pm.product_collection
            LEFT JOIN product_type_models as ptm on ptm.id = pm.product_type
            LEFT JOIN product_form_models as pfm on pfm.id = pm.product_form
            LEFT JOIN product_package_models as ppm on ppm.id = pm.product_package
            where pm.sku = 
            ' . $sku);

                // $res_productdetail = DB::select("select * from product_models as pm  where pm.sku = '" . $sku . "'");

                if (count($res_productdetail) <> 0) {
                    $productdetail = $res_productdetail[0];
                    //dump("productydetail");
                    //dump($productdetail);
                    $orderprdct->id = $productdetail->id;

                    $orderprdct->nomerorder = $salesdetail["id"];

                    $orderprdct->idproduct = $productdetail->id;
                    $orderprdct->iduser = $salesdetail["user_id"];
                    $orderprdct->namaproduk = $productdetail->product_name;
                    $orderprdct->gambar = $productdetail->fileimages;

                    $orderprdct->discon = $resorderdetail[$p]["discount"];
                    $orderprdct->txtdiskon = 0;

                    $orderprdct->tax = $resorderdetail[$p]["total_tax"];
                    $orderprdct->hargaproduk = $resorderdetail[$p]["price"];
                    $orderprdct->hargabelumdiskon = 0;
                    $orderprdct->qty = $resorderdetail[$p]["quantity"];
                    $orderprdct->subtotalproduk = $resorderdetail[$p]["total_price"];
                    $orderprdct->note = $resorderdetail[$p]["note"];
                    // dd("end") ; 
                    $orderprdct->review = '';
                    $orderprdct->status = $resorderdetail[$p]["status"];
                    $orderprdct->deleted = '';
                    $orderprdct->created_at = $productdetail->created_at;
                    $orderprdct->updated_at = $productdetail->updated_at;
                    $orderprdct->sku = $sku;
                    $orderprdct->product_variant = $productdetail->product_variant;
                    $orderprdct->product_name = $productdetail->product_name;
                    $orderprdct->product_detail = $productdetail->product_detail;
                    $orderprdct->product_shortdetail = $productdetail->product_shortdetail;
                    $orderprdct->product_brand = $productdetail->product_brand;
                    $orderprdct->product_collection = $productdetail->product_collection;
                    $orderprdct->product_type = $productdetail->product_type;
                    $orderprdct->product_form = $productdetail->product_form;
                    $orderprdct->product_package = $productdetail->product_package;
                    $orderprdct->product_price = $productdetail->product_price;
                    $orderprdct->product_price_currency = $productdetail->product_price_currency;
                    $orderprdct->product_weight = $productdetail->product_weight;
                    $orderprdct->product_width = $productdetail->product_width;
                    $orderprdct->product_height = $productdetail->product_height;
                    $orderprdct->product_length = $productdetail->product_length;
                    $orderprdct->product_acidityscore = $productdetail->product_acidityscore;
                    $orderprdct->product_aciditydesc = $productdetail->product_aciditydesc;
                    $orderprdct->product_bodyscore = $productdetail->product_bodyscore;
                    $orderprdct->product_bodydesc = $productdetail->product_bodydesc;
                    $orderprdct->product_roastdesc = $productdetail->product_roastdesc;
                    $orderprdct->product_typedesc = $productdetail->product_typedesc;
                    $orderprdct->product_intensity = $productdetail->product_intensity;
                    $orderprdct->product_default_discount = $productdetail->product_default_discount;
                    $orderprdct->status_stock = $productdetail->status_stock;
                    $orderprdct->fileimages = $productdetail->fileimages;
                    if (!empty($orderprdct->fileimages)) {
                        $orderprdct->images = json_decode($productdetail->fileimages);
                    } else {
                        $orderprdct->images = null;
                    }
                    array_push($product_order, $orderprdct);
                }
            }

            //dump("product_order vend");
            //dump($product_order);
        } else {

            $resorder = DB::select(
                "select * from order_models as om where om.nomerorder = '" . $nomerorder . "' ORDER BY om.id desc"
            );
            //dump($resorder);
            //dump("select * from order_detail_models as odm inner join product_models as pm on odm.idproduct = pm.id where odm.nomerorder = '" . $nomerorder . "' ORDER BY odm.id desc");
            $resorderdetail = DB::select(
                "select * from order_detail_models as odm inner join product_models as pm on odm.idproduct = pm.id where odm.nomerorder = '" . $nomerorder . "' ORDER BY odm.id desc"
            );
            //dump($resorderdetail);
            $product_order = [];

            for ($p = 0; $p < count($resorderdetail); $p++) {
                $orderprdct = $resorderdetail[$p];

                if (!empty($orderprdct->fileimages)) {
                    $orderprdct->images = json_decode($resorderdetail[$p]->fileimages);
                } else {
                    $orderprdct->images = null;
                }


                array_push($product_order, $orderprdct);
                //dump("product_order stripe");
                //dump($product_order);
            }
        }


        //dd($resorder);
        //dd("end test detail order");
        return view('orders/show', compact('product_order'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return view('products/show', compact('product_models', 'pict'));
    }

    function searchbyid($id, $array)
    {
        //
        foreach ($array as $key => $val) {
            ////dump($val['email']);
            if ($val['id'] === $id) {
                return $val['sku'];
            }
        }
        return null;
    }
}
