<?php

namespace App\Http\Controllers\Api\Vend;

use App\Models\Member_address_model;
use App\Http\Resources\MemberAddressResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client as GuzzleClient;
use App\Http\Controllers\Vend\VendController;

class GetSalesController extends Controller
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

    public function get(Request $request)
    {
       $nomerorder =  $request->nomerorder;
       $type = $request->type;

       if ($type <> 'stripe') {
          $res = $this->getvend($nomerorder);
        return $res;
        }
        else
        {
      
            $res = $this->getstripe($nomerorder);
            return $res;
        }

    }

    public function getstripe($nomerordervend)
    {

        $product_order = [];

        //dump($nomerordervend);

        $res_orderweb = DB::select("SELECT * from order_models where nomerorder = '".$nomerordervend."'");
        if (!empty($res_orderweb[0])) {
            $res_productdetail = DB::select("SELECT * from order_detail_models as odm 
            INNER JOIN product_models as pm  on odm.idproduct = pm.id 
            where  odm.nomerorder = '".$nomerordervend."'");
            
        }

        
        // dump($res_orderweb); 
        // dump($res_productdetail);

        //dd(count($res_productdetail)>=1);
      
        if (count($res_productdetail) >= 1) {
            //$productdetail = $res_productdetail[0];
            $productdetail = $res_orderweb[0];
            $test = new \StdClass();
            $test = $productdetail;
            $test->line_items = ["qwerty","asdf","zxcv"];
            //$productdetail->id = $productdetail->id;
            //$productdetail->id = $test->id;           
            array_push($product_order, $test); 
            //dd("test");
        }
        else
        {
            //dd("ayam");
        }
      
       
        $orderprdct = new \StdClass();
        //$orderprdct->data = [$productdetail];
        $orderprdct= 
        [
            'data'=>$product_order,
            'total_count'=>0,
            'page_info'=>0,
            'version'=>0,
        ];
        //dd($orderprdct);
        //$orderprdct->data[0]->fileimages = $productdetail->fileimages; 
        //dd("ayam");
        // if (!empty($orderprdct->data[0]->fileimages)) {
        //     //$orderprdct->data[0]->images = json_decode($productdetail->fileimages);
        // } else {
        //     $orderprdct->data[0]->fileimages=null;
        //     $orderprdct->data[0]->images = null;
        // }

        $res=$orderprdct;
        //dd($orderprdct);
        // $orderprdct->data = $productdetail;
        //$res["line_items"][0]=$productdetail;
        
        // $res = $orderprdct;
//dd($orderprdct);
        //$res["data"][0]="kucing";
        return  $orderprdct;
    }


    public function getvend($nomerordervend)
    {
        $res_allproductvend = (new VendController)->getallproduct();
        $allproductvend = ($res_allproductvend["data"]);

        //$invoice = '2022.0002495';
        $invoice = $nomerordervend;

        $headers = [
            'Authorization' => 'Bearer Cl2iG3P2M5cs6JTkGGWpl_VETaQqAMucGlqehvCv',
            'Cookie' => 'rguserid=4f34753d-6764-412e-b2fe-6e99d2c0871a; rguuid=true; rgisanonymous=true',
            'Key' => 'e791365f6d3d4bb1a6f03c8ad509f106'
        ];
        $client = new GuzzleClient([
            'headers' => $headers,
        ]);

        $r = $client->request(
            'GET',
            'https://supressocoffee.vendhq.com/api/2.0/search',
            [
                'query' =>
                [
                    'type' => 'sales',
                    'invoice_number' => $invoice,
                ]
            ]
        );
        $response = ($r->getBody()->getContents());
        $res = json_decode($r->getBody(), true, 512, JSON_BIGINT_AS_STRING);
        $salesdetail = $res["data"][0];
        $resorderdetail = $salesdetail["line_items"];
        $product_order = [];

// dump($resorderdetail);


        for ($p = 0; $p < count($resorderdetail); $p++) {
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
        
        if (count($res_productdetail) <> 0) {

            $orderprdct->vend_id = $resorderdetail[$p]["id"];
            $orderprdct->vend_product_id = $resorderdetail[$p]["product_id"];
            $orderprdct->vend_salesperson_id = $resorderdetail[$p]["salesperson_id"];
            $orderprdct->vend_tax_id = $resorderdetail[$p]["tax_id"];
            $orderprdct->vend_discount_total = $resorderdetail[$p]["discount_total"];
            $orderprdct->vend_discount = $resorderdetail[$p]["discount"];
            $orderprdct->vend_price_total = $resorderdetail[$p]["price_total"];
            $orderprdct->vend_price = $resorderdetail[$p]["price"];
            $orderprdct->vend_cost_total = $resorderdetail[$p]["cost_total"];
            $orderprdct->vend_cost = $resorderdetail[$p]["cost"];
            $orderprdct->vend_tax_total = $resorderdetail[$p]["tax_total"];
            $orderprdct->vend_tax = $resorderdetail[$p]["tax"];
            $orderprdct->vend_quantity = $resorderdetail[$p]["quantity"];
            $orderprdct->vend_loyalty_value = $resorderdetail[$p]["loyalty_value"];
            $orderprdct->vend_note = $resorderdetail[$p]["note"];
            $orderprdct->vend_price_set = $resorderdetail[$p]["price_set"];
            $orderprdct->vend_status = $resorderdetail[$p]["status"];
            $orderprdct->vend_sequence = $resorderdetail[$p]["sequence"];
            $orderprdct->vend_gift_card_number = $resorderdetail[$p]["gift_card_number"];
            $orderprdct->vend_promotions = $resorderdetail[$p]["promotions"];
            $orderprdct->vend_unit_loyalty_value = $resorderdetail[$p]["unit_loyalty_value"];
            $orderprdct->vend_unit_cost = $resorderdetail[$p]["unit_cost"];
            $orderprdct->vend_unit_discount = $resorderdetail[$p]["unit_discount"];
            $orderprdct->vend_unit_price = $resorderdetail[$p]["unit_price"];
            $orderprdct->vend_unit_tax = $resorderdetail[$p]["unit_tax"];
            $orderprdct->vend_total_cost = $resorderdetail[$p]["total_cost"];
            $orderprdct->vend_total_discount = $resorderdetail[$p]["total_discount"];
            $orderprdct->vend_total_loyalty_value = $resorderdetail[$p]["total_loyalty_value"];
            $orderprdct->vend_total_price = $resorderdetail[$p]["total_price"];
            $orderprdct->vend_total_tax = $resorderdetail[$p]["total_tax"];
            $orderprdct->vend_is_return = $resorderdetail[$p]["is_return"];
            $productdetail = $res_productdetail[0];
            $orderprdct->id = $productdetail->id;
            $orderprdct->idproduct = $productdetail->id;
            $orderprdct->namaproduk = $productdetail->product_name;
            $orderprdct->gambar = $productdetail->fileimages;
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
            $orderprdct->product_collection_name = $productdetail->product_collection_name;
            $orderprdct->product_type_name = $productdetail->product_type_name;
            $orderprdct->product_form_name = $productdetail->product_form_name;
            $orderprdct->product_package_name = $productdetail->product_package_name;
            array_push($product_order, $orderprdct);
        }
        else
        {
            ##find product on vend
            $hd = [
                'Authorization' => 'Bearer Cl2iG3P2M5cs6JTkGGWpl_VETaQqAMucGlqehvCv',
                'Cookie' => 'rguserid=4f34753d-6764-412e-b2fe-6e99d2c0871a; rguuid=true; rgisanonymous=true',
                'Key' => 'e791365f6d3d4bb1a6f03c8ad509f106'
            ];
            $cl = new GuzzleClient([
                'headers' => $hd,
            ]);
    
            $rs = $cl->request(
                'GET',
                'https://supressocoffee.vendhq.com/api/2.0/products/'.$resorderdetail[$p]["product_id"],
            );
            //$response = ($rs->getBody()->getContents());
            $resp = json_decode($rs->getBody(), true, 512, JSON_BIGINT_AS_STRING);
            //dd($resp["data"]["variant_name"]);
            
            $orderprdct->vend_id = $resorderdetail[$p]["id"];
            $orderprdct->vend_product_id = $resorderdetail[$p]["product_id"];
            $orderprdct->vend_salesperson_id = $resorderdetail[$p]["salesperson_id"];
            $orderprdct->vend_tax_id = $resorderdetail[$p]["tax_id"];
            $orderprdct->vend_discount_total = $resorderdetail[$p]["discount_total"];
            $orderprdct->vend_discount = $resorderdetail[$p]["discount"];
            $orderprdct->vend_price_total = $resorderdetail[$p]["price_total"];
            $orderprdct->vend_price = $resorderdetail[$p]["price"];
            $orderprdct->vend_cost_total = $resorderdetail[$p]["cost_total"];
            $orderprdct->vend_cost = $resorderdetail[$p]["cost"];
            $orderprdct->vend_tax_total = $resorderdetail[$p]["tax_total"];
            $orderprdct->vend_tax = $resorderdetail[$p]["tax"];
            $orderprdct->vend_quantity = $resorderdetail[$p]["quantity"];
            $orderprdct->vend_loyalty_value = $resorderdetail[$p]["loyalty_value"];
            $orderprdct->vend_note = $resorderdetail[$p]["note"];
            $orderprdct->vend_price_set = $resorderdetail[$p]["price_set"];
            $orderprdct->vend_status = $resorderdetail[$p]["status"];
            $orderprdct->vend_sequence = $resorderdetail[$p]["sequence"];
            $orderprdct->vend_gift_card_number = $resorderdetail[$p]["gift_card_number"];
            $orderprdct->vend_promotions = $resorderdetail[$p]["promotions"];
            $orderprdct->vend_unit_loyalty_value = $resorderdetail[$p]["unit_loyalty_value"];
            $orderprdct->vend_unit_cost = $resorderdetail[$p]["unit_cost"];
            $orderprdct->vend_unit_discount = $resorderdetail[$p]["unit_discount"];
            $orderprdct->vend_unit_price = $resorderdetail[$p]["unit_price"];
            $orderprdct->vend_unit_tax = $resorderdetail[$p]["unit_tax"];
            $orderprdct->vend_total_cost = $resorderdetail[$p]["total_cost"];
            $orderprdct->vend_total_discount = $resorderdetail[$p]["total_discount"];
            $orderprdct->vend_total_loyalty_value = $resorderdetail[$p]["total_loyalty_value"];
            $orderprdct->vend_total_price = $resorderdetail[$p]["total_price"];
            $orderprdct->vend_total_tax = $resorderdetail[$p]["total_tax"];
            $orderprdct->vend_is_return = $resorderdetail[$p]["is_return"];

            $orderprdct->product_name = $resp["data"]["variant_name"];

            //variant_name


            array_push($product_order, $orderprdct);
        }
       

        }
        //dump($res);
        //dd($product_order);

        $sales = new \StdClass();
        $res["data"][0]["line_items"]=$product_order;

        //$res = json_decode($r->getBody(), true, 512, JSON_BIGINT_AS_STRING);
        return $res;
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
