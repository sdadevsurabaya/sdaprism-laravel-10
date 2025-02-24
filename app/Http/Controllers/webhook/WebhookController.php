<?php

namespace App\Http\Controllers\webhook;

use App\Http\Controllers\Api\Easyship\PostShippingController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Member\MemberController;
use App\Http\Controllers\TokenPaymentController;
use App\Http\Controllers\webhook\CustomerEmailController;
use App\Models\NotifPaymentLogModel;
use App\Models\Order_detail_model as OrderDetailModel;
use App\Models\Order_model as OrderModel;
use App\Models\Product_model as ProductModel;
use App\Models\TokenPaymentModel;
use Darryldecode\Cart\Facades\CartFacade;
use GuzzleHttp\Client as GuzzleClient;
use Exception;
use Illuminate\Http\Request;
use stdClass;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{

    private function getJNEAutData($type = "prod"){
        if ($type == "prod") {
            return [
                "username" => "ASIATERRA",
                "key" => "82f6b11eba4b7d92c5f48567d3e55149",
            ];
        } else {
            return [
                "username" => "TESTAPI",
                "key" => "25c898a9faea1a100859ecd9ef674548",
            ];
        }
    }

    public function success(Request $r){

        $res_allproduct = DB::select('select p.*, b.name as brand, c.name as category, sc.name as subcategory from pos_products as p
        LEFT JOIN pos_brand as b on b.id = p.id_brand
        LEFT JOIN pos_category as c on c.id = p.id_category
        LEFT JOIN pos_sub_category as sc on sc.id = p.id_sub_category where p.deleted="false"');

        $title = 'home';
        $pages = 'landing';

        // if (!isset($r->token))
        //     return redirect('/');

        // // get order data for email confirmation
        // $nomerOrder     = $this->confirmPaymentAndGetOrderNumber($r->token);
        // $orderData      = OrderModel::where('nomerorder', $nomerOrder)->first();
        // $orderDetails   = OrderDetailModel::where('nomerorder', $nomerOrder)->get();
        
        // $custData = new stdClass;
        // $custData->orderData        = $orderData;
        // $custData->orderDetailDatas = $orderDetails;

        // // send email confirmation
        // $customerEmailController = new CustomerEmailController;
        // $customerEmailController->sendEmailConfirmation($custData);

        // // send api shipping
        // $this->shippingJNE($custData);

        // // clear cart
        CartFacade::clear();

        // // update stockbuy in master product
        // $this->addStockBuy($custData);

 
        $nomerOrder = 123456789;

        return view('front/webhook/success', ['nomerOrder' => $nomerOrder, 'title' => 'Success payment'], compact('title', 'pages','res_allproduct'));
    }

    public function testShippingJNE(Request $r){
        $nomerOrder = $r->nomor;

        $arrNomor = explode("-", $nomerOrder);
        if ($arrNomor > 0){
            $nomerOrder = $arrNomor[0] . "/" . $arrNomor[1];
        }

        $orderData      = OrderModel::where('nomerorder', $nomerOrder)->first();
        $orderDetails   = OrderDetailModel::where('nomerorder', $nomerOrder)->get();
        
        $custData = new stdClass;
        $custData->orderData        = $orderData;
        $custData->orderDetailDatas = $orderDetails;

        $responseJNE = $this->shippingJNE($custData);

        $result = [
            "data" => $responseJNE
        ];

        return response($result);
    }

    public function fail(){
        $res_allproduct = DB::select('select p.*, b.name as brand, c.name as category, sc.name as subcategory from pos_products as p
        LEFT JOIN pos_brand as b on b.id = p.id_brand
        LEFT JOIN pos_category as c on c.id = p.id_category
        LEFT JOIN pos_sub_category as sc on sc.id = p.id_sub_category where p.deleted="false"');
        // clear cart
        CartFacade::clear();

        $title = 'home';
        $pages = 'landing';
        return view('front/webhook/fail', ['title' => 'Failed payment'], compact('title', 'pages','res_allproduct'));
    }

    private function addStockBuy($custData) : void {
        foreach($custData->orderDetailDatas as $item){

            // handle if item is not from product_models 
            $idproduct = $this->getIdProductFromNonProductId($item->idproduct);
            $masterProduct = ProductModel::where('id', $idproduct)->first();
            if ($masterProduct != null){
                $masterProduct->stockbuy = $masterProduct->stockbuy + $item->qty;
                $masterProduct->save();
            }
        }
    }

    public function testNonProduct(Request $r){
        $result = $this->getIdProductFromNonProductId($r->id);
        if ($result == null)
            return response(["msg"=>"data is null", "result" => $result], 404);
        return response(["msg"=>"success", "result" => $result], 200);
    }

    // non product tools function
    private function getNonProductData(){
        return [
            ["non_id" => "Gifting-Premium-I", "id" => "147"],
            ["non_id" => "Gifting-Premium-II", "id" => "147"],
        ];
    }
    
    public function getIdProductFromNonProductId($idproduct){
        
        $arrNonProduct = $this->getNonProductData();
        
        $filtered = array_filter($arrNonProduct, function ($value) use ($idproduct) {
            return $value["non_id"] == $idproduct;
        });
        
        if (count($filtered) > 0){
            foreach ($filtered as $item) {
                return $item["id"];
            }
        }
        
        return $idproduct;
    }

    public function isNonProduct($idproduct){
        $arrNonProduct = $this->getNonProductData();

        $filtered = array_filter($arrNonProduct, function ($value) use ($idproduct) {
            return $value["non_id"] == $idproduct;
        });
    
        return count($filtered) > 0;
    }
    // end
    
    // gift set tools function
    private function isProductGiftSet($idproduct){
        $product =  ProductModel::where('id', $idproduct)->first(['id', 'product_collection']);
        if ($product != null){
            return $product->product_collection == '12';
        }
        return false;
    }

    public function isGiftSet($idGiftSet){
        $arrId = explode("-", $idGiftSet);
        if ($arrId == null || count($arrId) == 0)
            return false;

        $id = $arrId[0];
        return $this->isProductGiftSet($id);
    }

    public function getGiftSetItemId($idGiftSetItem){
        $getBefore = explode("|",$idGiftSetItem);
        if (count($getBefore) > 0){
            $getItemsId = explode("-",$getBefore[0]);
            if (count($getItemsId) > 0){
                return $getItemsId[2];
            }
        }
    }

    public function getGiftSetIdFromItem($idGiftSetItem){
        $arrIdGiftSetItem = explode("|",$idGiftSetItem);
        if (count($arrIdGiftSetItem) > 0){
            return $arrIdGiftSetItem[1];
        }
    }

    public function getGiftSetId($idGiftSet){
        $arrId = explode("-",$idGiftSet);
        $id    = $arrId[0];
        return $id;
    }
    // end

    private function shippingEasyship($custData) : void {
        
        $categoryship = "Dry Food & Supplements";
		$totalweight  = 0;
		$barang = '';
		$index  = 0;
		
        foreach ($custData->orderDetailDatas as $item) {

            // handle if item is not from product_models 
            $idproduct = $this->getIdProductFromNonProductId($item->idproduct);

            $product = ProductModel::where('id', $idproduct)->first(['product_name', 'sku', 'product_height', 'product_width', 'product_length', 'product_weight']);
            if ($product == null)
                throw new Exception("Cannot found product data in product_models check your id, your id is " . $idproduct, 1);
            
            $shippingWeight = $this->convertShippingWeight($product->product_weight);
            
            $totalweight = $totalweight + $shippingWeight;
			if ((int)$item->hargaproduk == 0) {
				$price = 0.001;
			} else {
				$price = (int)$item->hargaproduk;
			}

			if (($index >= 0) && ($index < ($custData->orderDetailDatas->count() - 1))) {
				$barang = $barang . "{\"description\": \"".$item->namaproduk."\" ,\"sku\": \"".$product->sku."\",\"actual_weight\": " . $shippingWeight . ",\"height\": \"".$product->product_height."\", \"width\": \"".$product->product_width."\", \"length\": \"".$product->product_length."\", \"category\": \"$categoryship\",\"declared_currency\": \"SGD\",\"declared_customs_value\": " . $price . ",\"quantity\": " . $item->qty . "}," . "\r\n";
			} else if ($index == ($custData->orderDetailDatas->count() - 1)) {
				$barang = $barang . "{\"description\": \"".$item->namaproduk ."\",\"sku\": \"".$product->sku."\",\"actual_weight\": " . $shippingWeight . ",\"height\": \"".$product->product_height."\", \"width\": \"".$product->product_width."\", \"length\": \"".$product->product_length."\", \"category\": \"$categoryship\",\"declared_currency\": \"SGD\",\"declared_customs_value\": " . $price . ",\"quantity\": " . $item->qty . "}" . "\r\n";
			}
			$index++;
		}

		$kuririd        = $custData->orderData->shipping_id;
        $orderNumber    = $custData->orderData->nomerorder;
        $countryCode    = $custData->orderData->country_code;
        $kodepos    = $custData->orderData->kodepos;
        $provinsi   = $custData->orderData->provinsi;
        $fullname   = $custData->orderData->namalengkap;
        $alamat1    = $custData->orderData->alamat;
        $alamat2    = $custData->orderData->alamatdua;
        $email  = $custData->orderData->email;
        $kota   = $custData->orderData->kota;
        $phone  = $custData->orderData->phone;

        $postShippingController = new PostShippingController;    
        try {
            $postShippingController->postshipping($orderNumber, $kuririd, $countryCode, $kota, $kodepos, $provinsi, $fullname, $alamat1, $alamat2, $phone, $email, $totalweight, $barang);
        } catch (\Throwable $th) {
            throw new Exception("Error when post shipping : ".$th->getMessage());
        }
    }

    private function shippingJNE($custData){

        $order = $custData->orderData;
        $orderDetails = $custData->orderDetailDatas;

        # init data shipping, start
        $SHIPPER_ADDR1 = 'Pakuwon Square AK-1/37';
		$SHIPPER_ADDR2 = 'Babatan';
		$SHIPPER_ADDR3 = 'Wiyung';
		$SHIPPER_CITY   = 'SURABAYA';
		$SHIPPER_REGION = 'JAWA TIMUR';
		$SHIPPER_ZIP    = '60227';
		$SHIPPER_PHONE  = '+623199000033';

        $weight = 0;
        foreach ($orderDetails as $detail) {
            $product = ProductModel::where("id", $detail->idproduct)->first('product_weight');
            if ($product != null) {
                $weight = $weight + $product->product_weight;
            }
        }

        # maping catatan
        $arrCatatan = explode('&&', $order->addcatatan);
        if (count($arrCatatan) > 0)
            $order->addcatatan = $arrCatatan[0];
        
        # maping service JNE
        $order->pengiriman = trim($order->pengiriman, " ");    
        $arrPengiriman = explode('-', $order->pengiriman);
        if (count($arrPengiriman) > 0)
            $order->pengiriman = trim($arrPengiriman[1], " ");

        $authData = $this->getJNEAutData();
        $userName   = $authData['username'];
        $apiKey     = $authData['key'];
        // $url        = "http://apiv2.jne.co.id:10102/tracing/api/generatecnote";
        $url        = "http://apiv2.jne.co.id:10101/tracing/api/generatecnote";
        
        $shipingCityCodePengirim = "SUB10000";
        $branchCode = "SUB000";
        $custId = "11166400";
        # init data shipping, end

        # mapping shipping post fields
        $arrPostShipping = [    
            ["param" => 'OLSHOP_BRANCH', "value" => $branchCode],
            ["param" => 'OLSHOP_ORDERID', "value" => $order->nomerorder],
            ["param" => 'OLSHOP_CUST', "value" => $custId],
            ["param" => 'OLSHOP_SHIPPER_NAME', "value" => "Supresso"],
            ["param" => 'OLSHOP_SHIPPER_ADDR1', "value" => $SHIPPER_ADDR1],
            ["param" => 'OLSHOP_SHIPPER_ADDR2', "value" => $SHIPPER_ADDR2],
            ["param" => 'OLSHOP_SHIPPER_ADDR3', "value" => $SHIPPER_ADDR3],
            ["param" => 'OLSHOP_SHIPPER_CITY', "value" => $SHIPPER_CITY],
            ["param" => 'OLSHOP_SHIPPER_REGION', "value" => $SHIPPER_REGION],
            ["param" => 'OLSHOP_SHIPPER_ZIP', "value" => $SHIPPER_ZIP],
            ["param" => 'OLSHOP_SHIPPER_PHONE', "value" => $SHIPPER_PHONE],
            ["param" => 'OLSHOP_RECEIVER_NAME', "value" => $order->namalengkap],
            ["param" => 'OLSHOP_RECEIVER_ADDR1', "value" => $order->alamat],
            ["param" => 'OLSHOP_RECEIVER_ADDR2', "value" => $order->alamatdua == null ? "-" : $order->alamatdua],
            ["param" => 'OLSHOP_RECEIVER_ADDR3', "value" => "-"],
            ["param" => 'OLSHOP_RECEIVER_CITY', "value" => $order->kota],
            ["param" => 'OLSHOP_RECEIVER_REGION', "value" => $order->provinsi],
            ["param" => 'OLSHOP_RECEIVER_ZIP', "value" => $order->kodepos],
            ["param" => 'OLSHOP_RECEIVER_PHONE', "value" => $order->phone],
            ["param" => 'OLSHOP_QTY', "value" => 1],
            ["param" => 'OLSHOP_WEIGHT', "value" => $weight],
            ["param" => 'OLSHOP_GOODSDESC', "value" => $order->addcatatan],
            ["param" => 'OLSHOP_GOODSVALUE', "value" => $order->itemsubtotal],
            ["param" => 'OLSHOP_GOODSTYPE', "value" => "2"],
            ["param" => 'OLSHOP_INST', "value" => "FRAGILE"],
            ["param" => 'OLSHOP_INS_FLAG', "value" => "N"],
            ["param" => 'OLSHOP_ORIG', "value" => $shipingCityCodePengirim],
            ["param" => 'OLSHOP_DEST', "value" => "SUB10003"],
            ["param" => 'OLSHOP_SERVICE', "value" => $order->pengiriman],
            ["param" => 'OLSHOP_COD_FLAG', "value" => "N"],
            ["param" => 'OLSHOP_COD_AMOUNT', "value" => "0"],
        ];

        $strPostShipping = "username=" . $userName . "&api_key=" . $apiKey;
        foreach ($arrPostShipping as $shipping) {
            $strPostShipping .= "&" . $shipping["param"] . "=" . $shipping["value"];
        }

        // return $strPostShipping; 

        $curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $strPostShipping,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded'
			),
		));

        $response = curl_exec($curl);
		curl_close($curl);        
        $jneresp = json_decode($response, true);

        if ($jneresp['detail'][0]["status"] == "sukses") {
            $noResi = $jneresp['detail'][0]['cnote_no'];
            $getOrderData = OrderModel::where("id", $order->id)->first();
            $getOrderData->tracking_number = $noResi;
            try {
                $getOrderData->save();
            } catch (\Throwable $th) {
                throw new Exception("Error when update resi to table : ".$th->getMessage(), 1);
            }
        }
    }

    public function convertShippingWeight($weight = 0){
        if ($weight == 0)
            return $weight;
        if ($weight < 100)
            return 0.1;
        return ($weight / 1000) + 0.1;
    }

    private function confirmPaymentAndGetOrderNumber($token) : String {

        // get member information
        $memberController = new MemberController();
        $member = $memberController->GetMemberInformation();
        
        // get payment token dan nomor order
        $tokenPaymentModel  = new TokenPaymentModel();
        // $tokenPayment       = $tokenPaymentModel->where('token', $member[0]->payment_token)->first();
        $tokenPayment       = $tokenPaymentModel->where('token', $token)->first();
        
        // get data order untuk update payment status
        $orderModel     = new OrderModel();
        $updateOrder    = $orderModel->where('nomerorder', $tokenPayment->order_number)->first();
                
        // update payment status
        $updateOrder->payment_status = 'succeeded';
        try {
           $updateOrder->save();
        } catch (\Throwable $th) {
            throw new Exception("Error when update order model : ".$th->getMessage(), 1);
        }

        // update tokenPayment
        $tokenPaymentController = new TokenPaymentController;
        try {
            $tokenPaymentController->updatePaymentToken($member[0]->payment_token, "success", $member[0]->fullname, $tokenPayment->order_number);
        } catch (\Throwable $th) {
            throw new Exception("Error when update success token payment : ".$th->getMessage(), 1);
        }

        // return nomororder created
        return $tokenPayment->order_number;
    }

    public function mitrandsHandlingNotification(Request $r){
        $model = new NotifPaymentLogModel();
        $model->merchant     = 'midtrans';
        $model->status       = $r->transaction_status;
        $model->payment_type = $r->payment_type;
        $model->status_code  = $r->status_code;
        $model->order_number = $r->order_id;    
        $model->gross_amount = $r->gross_amount;

        try {
            $model->save();
        } catch (\Throwable $th) {
            throw new Exception("error handling payment notification : ".$th->getMessage(), 1);
        }
    }

    public function getJNEHistoryTracking(Request $r){

        $cnote = $r->cnote;
        $id = $r->id;
        $authData = $this->getJNEAutData();
        $strPostShipping = "username=" . $authData['username'] . "&api_key=" . $authData['key'];
        $curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://apiv2.jne.co.id:10101/tracing/api/list/v1/cnote/' . $cnote,
			CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $strPostShipping,
			CURLOPT_RETURNTRANSFER => true,
		));

        $response = curl_exec($curl);
        $jneresp = json_decode($response, true);
		curl_close($curl);

        if(isset($jneresp["cnote"])){
            $statusawb = $jneresp["cnote"]["pod_status"];
            if ($statusawb == "DELIVERED"){
                # update status order to complete;
                $orderModel = OrderModel::where('id', $id)->first();
                if ($orderModel == null)
                    return response("Error : order data not full ", 400);

                $orderModel->status = "complete";
                $orderModel->statusawb   = $statusawb;
                $orderModel->statustrack = $statusawb;

                try {
                    $orderModel->save();
                    return response("Succeeded sync JNE", 200);

                } catch (\Throwable $th) {
                    return response("Error : " . $th->getMessage(), 400);
                }
            }
        };

        return response("Error : fetching api ", 400);       

        // $res = json_decode($r->getBody(), true, 512, JSON_BIGINT_AS_STRING);
    }
}
