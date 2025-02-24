<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\Order_detail_model as OrderDetailModel;
use App\Models\Order_model as OrderModel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRole;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Darryldecode\Cart\Facades\CartFacade;
use GuzzleHttp\Client;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class PaymentController extends Controller
{
    public function index(Request $r)
    {
        #get order number
        $OrderNumber = "waiters";


        # call nomer order
        if(isset($r->nomerOrder))
        {
            $nomerOrder = $r->nomerOrder;
        }

        #get value order
        // $total = Cart::getTotal();
        $total = $r->total;
        // $subtotal = Cart::getSubTotal();
        // $subtotalwithoutconditions = Cart::getSubTotalWithoutConditions();
        // $tax = ($subtotalwithoutconditions * 10/100);
        //$cartItems = Cart::getContent();

        # get cart data
        // $cart = CartFacade::getContent();

        # date time generate
        date_default_timezone_set('Asia/Jakarta');
        $date       = date('Y-m-d');
        $dateymd    = date('Ymd');
        $time       = date("H:i:s");

        $merchant = "midtrans";

      

        // $totalPrice = $subtotal;

        # simpan order pada tabel
        $orderModel = new OrderModel();
      
        
        //dd($orderModel);
   
         # call payment api section base on merchant selected
         $paymentResponse = [];
        //  if ($merchant == "midtrans"){
        //      $nomorOrderPayment = $OrderNumber . "-" . $dateymd;
            
        //      $orderModelData = OrderModel::where("nomerorder", $nomerOrder)->first();
        //      if ($orderModelData != null){
        //          if ($orderModelData->payment_id != null){
        //              $orderModel->payment_id = $orderModelData->payment_id;
        //          }
        //      } else {
        //          $paymentResponse = $this->requestMidtransPayment($nomorOrderPayment, $totalPrice);
        //          $orderModel->payment_id = $paymentResponse->token;					
        //      }
        //  }
         # end

        # create payment token
		$token = "";
		// $tokenPaymentController = new TokenPaymentController;
		// $updatedWho = $member[0]->fullname == null ? $member[0]->firstname : $member[0]->fullname;
		// try {
		// 	$token = $tokenPaymentController->getToken();
		// 	$tokenPaymentController->createPaymentToken($token, $member[0]->id, $updatedWho, "stripe", $nomerOrder);
		// } catch (\Throwable $th) {
		// 	throw new Exception("Error create payment : " .$th->getMessage(), 1);
		// }
		# end


         $paymentResponse = $this->requestMidtransPayment($nomerOrder, $total);

         //dd($paymentResponse);

         # mencari cart sub total
        //$cartSubTotal = CartFacade::getSubTotal();

         # return response base on merchant selected
         $result = [
            "msg" => "Success", 
            "data" => $paymentResponse, 
            // "token" => "qwertyasdfghzxcvbn"
        ];

        return response($result);
    }


    private function requestMidtransPayment($nomerOrder = "", $amount = 0){

		# sandbox
        $serverkey = "SB-Mid-server-vlSerrGDDiI6-_RdqLJgiWO-";
		$urlMidtrans = "https://app.sandbox.midtrans.com/snap/v1/transactions";

		# prod
		// $serverkey = "Mid-server-ewrQDVwL6fZRtc1PyO6obDMr";
		// $urlMidtrans = "https://app.midtrans.com/snap/v1/transactions";

        $authString = base64_encode($serverkey);
        
        $client = new Client();
        $response = $client->request('POST', $urlMidtrans, [
        'body' => '{"transaction_details":{"order_id":"'.$nomerOrder.'","gross_amount":"'.$amount.'"},"credit_card":{"secure":true}}',
        'headers' => [
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'Authorization' => 'Basic ' . $authString,
			'X-Append-Notification' =>  "'". url('midtrans-handling') . "'"
        ],
        ]);

        $res = $response->getBody();
        return json_decode($res);
    }
}