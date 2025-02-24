<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Exception\CardException;
use Stripe\StripeClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Srmklive\PayPal\Services\ExpressCheckout;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

use App\Models\Order_model;
use App\Models\Order_detail_model;

use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

class StripeController extends Controller
{
	private $stripe;
	public function __construct()
	{
		$this->stripe = new StripeClient(config('stripe.api_keys.secret_key'));
	}

	public function index()
	{
		return view('front.cobastripe');
	}



	public function payment(Request $request)
	{

		//$this->createSession()
		$checkoutsession = $this->createCheckoutSession(898989);
		// $result = [
		//     'sessionId' => $checkoutsession['id'],
		// ];
		// return $this->sendResponse($result, 'Session created successfully.');
		dd($checkoutsession);

		$validator = Validator::make($request->all(), [
			'fullName' => 'required',
			'cardNumber' => 'required',
			'month' => 'required',
			'year' => 'required',
			'cvv' => 'required'
		]);



		if ($validator->fails()) {
			$request->session()->flash('danger', $validator->errors()->first());
			return response()->redirectTo('/');
		}

		$token = $this->createToken($request);

		if (!empty($token['error'])) {
			$request->session()->flash('danger', $token['error']);
			// return response()->redirectTo('/');
			dd('danger, token error');
		}
		if (empty($token['id'])) {
			$request->session()->flash('danger', 'Payment failed.');
			//return response()->redirectTo('/');
			dd('danger, payment error');
		}

		$charge = $this->createCharge($token['id'], 17000);
		if (!empty($charge) && $charge['status'] == 'succeeded') {
			// $request->session()->flash('success', 'Payment completed.');
			dd('success Payment completed.');
		} else {
			// $request->session()->flash('danger', 'Payment failed.');
			dd('danger Payment failed.');
		}
		//return response()->redirectTo('/');

		// dump($token);
		// dd($request->fullName);
	}

	private function createCheckoutSession($totalamount)
	{

		##old_supresso==========================================
		/* 
$checkout_session = \Stripe\Checkout\Session::create([
	'success_url' => $domain_url . '/success',
	'cancel_url' => $domain_url . '/canceled',
	'payment_method_types' => ['card'],
	'mode' => 'payment',
	//'allow_promotion_codes' => true,
	'line_items' => [['price_data' => ['currency' => 'sgd','product_data' => ['name' => 'Supresso Coffee',],'unit_amount' => $totalamount,],'quantity' => 1,]], 
  ]);

  */
		##======================================================

		// $stripe = new \Stripe\StripeClient(
		//     'sk_test_4eC39HqLyjWDarjtT1zdp7dc'
		//   );
		$this->stripe->checkout->sessions->create([
			//     'success_url' => 'https://example.com/success',
			//     'cancel_url' => 'https://example.com/cancel',
			//     'line_items' => [
			//       [
			//         'price' => 'price_H5ggYwtDq4fbrJ',
			//         'quantity' => 2,
			//       ],
			//     ],
			//     'mode' => 'payment',
			//   ]);
			'success_url' => 'http://127.0.0.1:8000/updatepaymentstatus',
			'cancel_url' => 'https://example.com/cancels',
			'customer_email'       => 'webdevtest@gmail.com',
			'client_reference_id'  => '1234',
			'payment_method_types' => ['card'],
			'mode' => 'payment',
			//'allow_promotion_codes' => true,
			'line_items' => [
				[
					'price_data' =>
					[
						'currency' => 'sgd',
						'product_data' => ['name' => 'Supresso Coffee',],
						'unit_amount' => $totalamount,
					],
					'quantity' => 1,
				]
			],
		]);
	}

	public function getstripepayments(Request $request)
	{
		$id = $request->id;
		$res = "getstripepayments";
		$res = $this->stripe->paymentIntents->all(['limit' => 3]);
		$res = $this->stripe->paymentIntents->retrieve(
			$id,
			[]
		);
		$payment_status = $res->status;
		//dd($payment_status);
		return $res;
	}

	public function updatePaymentstatus(Request $request)
	{
		$id = $request->id;
		$res = "getstripepayments";
		$res = $this->stripe->paymentIntents->all(['limit' => 3]);
		$res = $this->stripe->paymentIntents->retrieve(
			$id,
			[]
		);
		$payment_status = $res->status;
		if ($payment_status == "succeeded") {
			DB::update("UPDATE order_models SET payment_status ='" . $payment_status . "'WHERE payment_id='" . $id . "';");
		} else if ($payment_status == "requires_payment_method") {
			DB::update("UPDATE order_models SET payment_status ='" . $payment_status . "'WHERE payment_id='" . $id . "';");
		} else {
			DB::update("UPDATE order_models SET payment_status ='" . $payment_status . "'WHERE payment_id='" . $id . "';");
		}
		//dd($payment_status);
		//return $res;
		// dd($payment_status);
		return response()->redirectTo('/admin/orders');
	}

	public function createSession(Request $request)
	{
		$cartItem = \Cart::getContent();


		
		// echo $request->shippingid;
		
		// dd("asd");
		// dd("stripe controller/createsession");

		$OrderNumber = app('App\Http\Controllers\Order\OrderController')->getOrderNumber();
		$member = app('App\Http\Controllers\Member\MemberController')->GetMemberInformation();
		date_default_timezone_set('Asia/Jakarta');
		$date = date('Y-m-d');
		$dateymd = date('Ymd');
		$time = date("H:i:s");
		// dump($time);
		$couponDiscount = (session('coupon'));
		$cartSubTotal = \Cart::getSubTotal();
		$totalbeforediscount = session('totalbeforediscount');
		if (((\Cart::getCondition('coupon')))==null)
		{
			$cartDiscount = 0;
		}
		else
		{
			$cartDiscount = ((\Cart::getCondition('coupon'))->getValue()) * -1;
		}
		//return (count($cartItem)-1);
		$categoryship = "Dry Food & Supplements";
		$totalweight = 0;
		$barang = '';
		$index = 0;
        foreach ($cartItem as $item) {
            $totalweight = $totalweight + ($item->attributes->gramature);
			if ((int)$item->price==0)
			{
				$price = 0.001;
			}
			else
			{
				$price = (int)$item->price;
			}
			
			if (($index>=0) && ($index<(count($cartItem)-1)))
			{
				//return "index ".$index." or dibawah".(count($cartItem)-1);
				$barang = $barang."{\"description\": \"" . $item->name . "\",\"sku\": \"sku\",\"actual_weight\": " . $item->attributes->gramature . ",\"height\": 5,\"width\": 15,\"length\": 15,\"category\": \"$categoryship\",\"declared_currency\": \"SGD\",\"declared_customs_value\": " .$price. ",\"quantity\": " . $item->quantity . "}," . "\r\n";
			}
			else if($index==(count($cartItem)-1))
			{
				$barang = $barang."{\"description\": \"" . $item->name . "\",\"sku\": \"sku\",\"actual_weight\": " . $item->attributes->gramature . ",\"height\": 5,\"width\": 15,\"length\": 15,\"category\": \"$categoryship\",\"declared_currency\": \"SGD\",\"declared_customs_value\": " .$price. ",\"quantity\": " . $item->quantity . "}" . "\r\n";
			}
			$index++;
        }
		//$barang =  $barang . "{\"description\": \"'" . "kucing anggora" . "'\",\"sku\": \"sku\",\"actual_weight\": " . "4000" . ",\"height\": 5,\"width\": 15,\"length\": 15,\"category\": \"Dry Food & Supplements\",\"declared_currency\": \"SGD\",\"declared_customs_value\": " . "2000" . ",\"quantity\": " . "5" . "}," . "\r\n";
		$totalweight = $totalweight/1000;

		//return $barang;
		

		//return $request->kodecountry;

		$kuririd = $request->shippingid;
		$product_form = app('App\Http\Controllers\Api\Easyship\PostShippingController')->postshipping($OrderNumber,$kuririd,$request->kodecountry,$member[0]->kota,$member[0]->kodepos,$member[0]->provinsi,$member[0]->fullname,$member[0]->alamat,$member[0]->alamat,$member[0]->notelp,$member[0]->email,$totalweight,$barang);

return $product_form;
		// echo $request->shipping;
		
		dump("asd");
		dd($product_form);

		//dd($cartItem);

		## kirim pembayaran pada stripe
		$total = $request->total * 100;
		$session = $this->stripe->checkout->sessions->create([
			'success_url' => 'https://example.com/success',
			'cancel_url' => 'https://example.com/cancel',
			'customer_email'       => $member[0]->email,
			'client_reference_id'  => '1234',
			'payment_method_types' => ['card'],
			'mode' => 'payment',
			'line_items' => [
				[
					'price_data' =>
					[
						'currency' => 'sgd',
						'product_data' => ['name' => 'Supresso Coffee',],
						'unit_amount' => $total,
					],
					'quantity' => 1,
				]
			],
		]);
		// $result = [
		//     'sessionId' => $session['id'],
		// ];

		## simpan order pada tabel

		$order_models = Order_model::create([
			'nomerorder' => $OrderNumber . "/" . $dateymd,
			'iduser' => $member[0]->id,
			'tanggalorder' =>  $dateymd,
			'jamorder' => $time,
			'status' => 'on-hold',
			'statustrack' => '-',
			'itemsubtotal' => $cartSubTotal,
			'discon' => $cartDiscount,
			'coupon' => $request->coupon,
			'kodekupon' => "",
			'persdiskon' => "",
			'tax' => 0,
			'shippingprice' => $request->shipping,
			'ordertotal' =>  $totalbeforediscount - $cartDiscount + $request->shipping,
			'payment' => "stripe",
			'pengiriman' => $request->shippingname,
			'namalengkap' =>  $member[0]->fullname,
			'namalengkapawb' => $member[0]->fullname,
			'firtsname' => $member[0]->firstname,
			'lastname' => $member[0]->lastname,
			'negara' => $member[0]->negara,
			'provinsi' => $member[0]->provinsi,
			'kota' => $member[0]->kota,
			'kecamatan' => $member[0]->kecamatan,
			'alamat' => $member[0]->alamat,
			'alamatawb' => $member[0]->alamat,
			'alamatdua' => $member[0]->alamat,
			'kodepos' => $member[0]->kodepos,
			'company' => $member[0]->company,
			'email' => $member[0]->email,
			'emailtanpatitik' => $member[0]->email,
			'phone' => $member[0]->notelp,
			'phoneawb' => $member[0]->notelp,
			'addcatatan' => "-",
			'addcatatanawb' => "-",
			'statusawb' => "-",
			'notifnews' => "",
			'deleted' => "false",
			'payment_id' => $session->payment_intent,
			'payment_method' => "credit card",
			'payment_status' => "incomplete"
		]);

		##kirim email
		$this->sendMail($request->shipping);

		foreach ($cartItem as $row) {
			$order_models = Order_detail_model::create([
				'nomerorder'=>$OrderNumber . "/" . $dateymd,
				'idproduct'=>$row->id,
				'iduser'=>$member[0]->id,
				'namaproduk'=>$row->name,
				'gambar'=>$row->attributes->images,
				'discon'=>0,
				'txtdiskon'=>0,
				'tax'=>0,
				'hargaproduk'=>$row->price,
				'hargabelumdiskon'=>$row->price,
				'qty'=>$row->quantity,
				'subtotalproduk'=>($row->quantity*$row->price),
				'note'=>"-",
				'review'=>"-",
				'status'=>"active",
				'deleted'=>"false"
			]);
		
		}

		

		if ($session) {
			$res = response()->json(
				[
					'status' => 'success',
					'sessionId' => $session->id,
					'paymentId' =>  $session->payment_intent,
					'cart' => $cartItem,
					'ordernumber' => $OrderNumber,
					'member' => $member
				],
				200
			);
		} else {
			$res = response()->json(
				[
					'status' => 'failed',
					'sessionId' => $session->id,
					'paymentId' =>  $session->payment_intent,
					'cart' => $cartItem
				],
				500
			);
		};

		return $res;
		// return $this->sendResponse($result, 'Session created successfully.');
	}

	public function paypalpayment(Request $request)
	{

		$data = [];
		$data['items'] = [
			[
				'name' => 'ItSolutionStuff.com',
				'price' => 100,
				'desc'  => 'Description for ItSolutionStuff.com',
				'qty' => 1
			]
		];

		$data['invoice_id'] = 1;
		$data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
		$data['return_url'] = route('paypalpayment.success');
		$data['cancel_url'] = route('paypalpayment.cancel');
		$data['total'] = 100;
		// $provider = new \ExpressCheckout;
		$provider = new PayPalClient;

		// Through facade. No need to import namespaces
		$provider = \PayPal::setProvider();
		$response = $provider->setExpressCheckout($data);
		$response = $provider->setExpressCheckout($data, true);
		return redirect($response['paypal_link']);

		// $res = response()->json(
		// 	[
		// 		'status' => 'payment with paypal'
		// 	],
		// 	500
		// );
		// return $res;

	}


	/**
	 * Responds with a welcome message with instructions
	 *
	 * @return \Illuminate\Http\Response
	 */

	//  public function paymentpaypall()
	//  {

	//  }

	/**
	 * Responds with a welcome message with instructions
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function paypalcancel()
	{
		dd('Your payment is canceled. You can create cancel page here.');
	}

	/**
	 * Responds with a welcome message with instructions
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function paypalsuccess(Request $request)
	{
		$response = $provider->getExpressCheckoutDetails($request->token);
		if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
			dd('Your payment was successfully. You can create success page here.');
		}
		dd('Something is wrong.');
	}




	##create token for stripe
	private function createToken($cardData)
	{
		$token = null;
		try {
			$token = $this->stripe->tokens->create([
				'card' => [
					'number' => $cardData['cardNumber'],
					'exp_month' => $cardData['month'],
					'exp_year' => $cardData['year'],
					'cvc' => $cardData['cvv']
				]
			]);
		} catch (CardException $e) {
			$token['error'] = $e->getError()->message;
		} catch (Exception $e) {
			$token['error'] = $e->getMessage();
		}
		return $token;
	}
	##create charge for stripe
	private function createCharge($tokenId, $amount)
	{
		$charge = null;
		try {
			$charge = $this->stripe->charges->create([
				'amount' => $amount,
				'currency' => 'usd',
				'source' => $tokenId,
				'description' => 'dev indraco test payment',
				// 'customer' => 'webdevindraco@gmail.com'
			]);
		} catch (Exception $e) {
			$charge['error'] = $e->getMessage();
		}
		return $charge;
	}

	// public function sendMail(Request $request)
	public function sendMail($shipping)
	{

		$cartItem = \Cart::getContent();
		$cartItems = $cartItem->sort();

		if (!empty(\Cart::getSubTotal())) {
			$cartSubTotal = \Cart::getSubTotal();
		} else {
			$cartSubTotal = 0;
		}
		if (!empty(\Cart::getCondition('coupon'))) {
			$cartCoupon = \Cart::getCondition('coupon');
		} else {
			$cartCoupon = 0;
		}
		if (!empty(\Cart::getCondition('Shipping'))) {
			$cartShipping = \Cart::getCondition('Shipping');
		} else {
			$cartShipping = 0;
		}
		if (!empty(\Cart::getTotal())) {
			$cartTotal = \Cart::getTotal();
		} else {
			$cartTotal = 0;
		}
		// $cartShipping = $request->shipping;

		$cartShipping = $shipping;
		$gst = (round($cartTotal * 0.08));
		$totalWithGst = $cartTotal + $gst + $cartShipping;


		// dump($cartSubTotal);
		// dump($cartCoupon);
		// dump($cartShipping);
		// dump($cartTotal);
		// dump($gst);


		// $mail = new PHPMailer\PHPMailer\PHPMailer();
		$mail = new PHPMailer(true);
		$email = "suryoatm@gmail.com";
		$nama = "suryo";
		$namalengkap = "suryo atmojo";
		$nmrorder = 123456789;
		$kurir = 'test kurir';
		$alamat = 'test alamat';
		$kota = 'test kota';
		$kodepos = 'test kodepos';
		$negara = 'test negara';
		$phone = 'test phone';

		$bodyproduct = '';

		$dsctag = 'in itag';
		$sbtotal = 'ini sbtotal';
		//dd($cartItems);
		foreach ($cartItems as $item) {
			// dump($item->attributes->gramature);
			$bodyproductt = '	
			<tr>
													<td width="25%" style="border-bottom: solid 1px #878787;">
														<img src="http://127.0.0.1:8000/files/product-images/' . $item->attributes->images . '" width="100%" height="auto" style="vertical-align: sub;" alt="">
													</td>
													<td width="50%" style="border-bottom: solid 1px #878787;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin-top: 0; margin-bottom: 5px; text-transform: uppercase;">
															' . $item->name . '
														</p>
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin-top: 0;">
															' . $item->attributes->gramature . 'g x ' . $item->quantity . '
														</p>
														<p style="font-family: sans-serif; font-size: 1em; color: #999; margin: 0;">
															<img src="https://supresso.com/sg/img/pricetag.png" width="15" height="15" style="vertical-align: sub;" alt="">
															Discount 15%
														</p>
													</td>
													<td width="25%" style="border-bottom: solid 1px #878787; padding-right: 7px;" align="right">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0; line-height: .75;">
															<del style="color: #999; text-decoration-color: red;">$ ' . $item->price . '</del>
															<br><br>
															<span>$ ' . $item->price . '</span>
														</p>
													</td>
												</tr>

							';
			$bodyproduct = $bodyproduct . $bodyproductt;
		}




		$subglobal = 'sub global';
		$adacoupon = 'test coupon';
		$shipglobal = 'test ship global';
		$adacoupon = 'test coupon';
		$gstglobal = 'test global';
		$ordertotalfix = 'test order';
		$yousavedisi = 'test yousave';
		$emal = 'emal';
		$msg = '';


		try {

			$mail->SMTPDebug = 0;
			//Server settings                   
			$mail->isSMTP();                                            // Send using SMTP
			$mail->Host     = 'mail.supresso.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'ecommerce@supresso.com';
			$mail->Password = 's{Vi(jz?B#sZ';
			$mail->SMTPSecure = 'ssl';
			$mail->Port     = 465;
			$mail->CharSet = "UTF-8";

			//Recipients
			$mail->setFrom('ecommerce@supresso.com', 'Supresso');
			$mail->addAddress('indracodev@gmail.com');
			// $mail->addAddress('sigit.develop@gmail.com', 'ok');
			$mail->addCC('suryoatmojo@uwp.ac.id');
			$mail->addBCC('sigit.develop@gmail.com');
			// $mail->addBcc('ujicobaa33@gmail.com', 'ok');
			// //$mail->addBcc('design@indraco.com', 'ok');
			// $mail->addBcc('dm@indraco.com', 'Owner');
			// $mail->addBcc('marco@indraco.com', 'Owner');
			// $mail->addBcc('pristine@indraco.com', 'Owner');




			//  $mail->addAttachment('./pdfs/'.$filename);
			// Content

			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = '[TEST] Your order confirmation!';
			$mail->Body    = '<!DOCTYPE html>
<html lang="en" style="padding: 0; margin: 0; background-color: #ffffff; width: 100%!important;">
<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="x-apple-disable-message-reformatting">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>SUPRESSO &bull; Order Confirmation</title>
		<link rel="icon" href="img/logo.ico">
	</head>
	<body style="padding: 0; margin: 0;">

		<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
			<tbody>

				<!-- bellow subject -->
				<tr style="display: none; max-height: 0; overflow: hidden;">
					<td>
						<module name="preheader" label="Preheader"></module>
						<editable name="preheader">[TEST] Confirmation Order</editable>
						&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
						&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
						&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
						&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
						&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
						&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
						&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
						&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
						&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
						&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
						&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
						&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
						&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
						&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
						&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
						&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
						&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
						&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp; &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj; &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
					</td>
				</tr>
				<!-- bellow subject -->

				<tr>
					<td>
						<table border="0" cellpadding="40" cellspacing="0" width="720" align="center">
							<tbody>
								<!-- ========================= header ========================= -->
								<tr>
									<td style="padding-bottom: 0;">
										<img src="https://supresso.com/newsletterv2/assets/supresso/img/logo.png" width="72" height="72" style="vertical-align: sub;" alt="">
									</td>
								</tr>
								<!-- ========================= edn of header ========================= -->

								<!-- ========================= body ========================= -->
								<tr>
									<td style="padding-bottom: 0;">
										<h1 style="font-family: sans-serif; font-size: 1.75em; color: #323232; margin-top: 0;">
											<strong>Your order confirmation!</strong>
										</h1>
										<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin-top: 0;">
											Hi
											<strong>Vidyadhar Kale</strong>,
										</p>
										<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
											Thank you for your order! You will receive a shipping confirmation and tracking e-mail as soon as your coffee is ready.
										</p>
									</td>
								</tr>

								<tr>
									<td style="padding-bottom: 0;">
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tbody>

												<tr>
													<td>
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin-top: 0;">
															<strong>Here`s what you ordered :</strong>
														</p>
													</td>
												</tr>

												<tr>
													<td>
														<table border="0" cellpadding="0" cellspacing="0" width="100%">
															<tbody>
																<tr>
																	<td width="25%">
																		<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
																			Order Number
																		</p>
																	</td>
																	<td width="75%">
																		<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
																			<span style="padding-right: 15px;">:</span>
																			160/20210522
																		</p>
																	</td>
																</tr>
																<tr>
																	<td width="25%">
																		<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
																			Courier
																		</p>
																	</td>
																	<td width="75%">
																		<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
																			<span style="padding-right: 15px;">:</span>
																			Qxpress - Domestic
																		</p>
																	</td>
																</tr>
																<tr>
																	<td width="25%">
																		<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
																			Billing Address
																		</p>
																	</td>
																	<td width="75%">
																		<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
																			<span style="padding-right: 15px;">:</span>
																			3 Rhu Cross, #05-16, Singapore 437433 - Singapore
																		</p>
																	</td>
																</tr>
																<tr>
																	<td width="25%">
																		<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
																			Delivery Address
																		</p>
																	</td>
																	<td width="75%">
																		<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
																			<span style="padding-right: 15px;">:</span>
																			3 Rhu Cross, #05-16, Singapore 437433 - Singapore
																		</p>
																	</td>
																</tr>
																<tr>
																	<td width="25%">
																		<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
																			Phone
																		</p>
																	</td>
																	<td width="75%">
																		<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
																			<span style="padding-right: 15px;">:</span>
																			+62 81529207
																		</p>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>

											</tbody>
										</table>
									</td>
								</tr>

								<!-- tabel -->
								<tr>
									<td style="padding-bottom: 0;">
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th width="28%" style="border-top: solid 2px #878787; border-bottom: solid 2px #878787; padding-top: 7px; padding-bottom: 7px;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;" align="left">Item</p>
													</th>
													<th width="50%" style="border-top: solid 2px #878787; border-bottom: solid 2px #878787; padding-top: 7px; padding-bottom: 7px;"></th>
													<th width="22%" style="border-top: solid 2px #878787; border-bottom: solid 2px #878787; padding-top: 7px; padding-bottom: 7px;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;" align="right">Price</p>
													</th>
												</tr>
											</thead>
											<tbody>' . $bodyproduct . '
												
											</tbody>
										</table>
									</td>
								</tr>

								<tr>
									<td style="padding-top: 0; padding-bottom: 0;">
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tbody>
												<tr>
													<td width="70%" style="border-bottom: solid 1px #878787; padding-top: 7px; padding-bottom: 7px;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															Subtotal
														</p>
													</td>
													<td width="5%" align="center" style="border-bottom: solid 1px #878787; padding-top: 7px; padding-bottom: 7px;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															$
														</p>
													</td>
													<td width="25%" align="right" style="border-bottom: solid 1px #878787; padding-top: 7px; padding-bottom: 7px;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															' . $cartSubTotal . '*
														</p>
													</td>
												</tr>

												<tr>
													<td width="70%" style="padding-top: 15px;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															<img src="https://supresso.com/sg/img/pricetag.png" width="15" height="15" style="vertical-align: sub;" alt="">
															#iamvaccinatedsg Coupon (10% off)
														</p>
													</td>
													<td width="5%" align="center" style="padding-top: 15px;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															$
														</p>
													</td>
													<td width="25%" align="right" style="padding-top: 15px; padding-right: 7px;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															25
														</p>
													</td>
												</tr>

												<tr>
													<td width="70%">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															Shipping Cost
														</p>
													</td>
													<td width="5%" align="center">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															$
														</p>
													</td>
													<td width="25%" align="right" style="padding-right: 7px;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
														' . $cartShipping . '
														</p>
													</td>
												</tr>

												<tr>
													<td width="70%">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															GST included in Price (Approx.)
														</p>
													</td>
													<td width="5%" align="center">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															$
														</p>
													</td>
													<td width="25%" align="right" style="padding-right: 2px;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															(' . $gst . ')
														</p>
													</td>
												</tr>

												<tr>
													<td width="70%" style="padding-top: 15px; padding-bottom: 15px;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															<strong>Total</strong>
														</p>
													</td>
													<td width="5%" align="center" style="padding-top: 15px; padding-bottom: 15px;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															<strong>$</strong>
														</p>
													</td>
													<td width="25%" align="right" style="padding-top: 15px; padding-bottom: 15px;">
														<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															<strong>' . $totalWithGst . '*</strong>
														</p>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>

								<tr>
									<td style="padding-top: 0; padding-bottom: 0;">
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tbody>
												<tr>
													<td style="padding-right: 7px;">
														<p align="right" style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
															You saved $
														</p>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<!-- end of tabel -->

								<tr>
									<td>
										<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin-top: 0; margin-bottom: 20px;">
											If there is any mistake with the details above, please contact us at ecommerce@supresso.com. Visit online
											<a href="#" target="_blank" style="text-decoration: none; color: #fd4f00;">My Orders</a>
											to view the most up-to-date status of your order.
										</p>
										<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin-top: 0; margin-bottom: 20px;">
											<small>
												<small>* Prices are inclusive of GST</small>
											</small>
										</p>
										<p style="font-family: sans-serif; font-size: 1em; color: #323232; margin: 0;">
											Best Regards,
											<br>
											<strong>Supresso</strong>
										</p>
									</td>
								</tr>
								<!-- ========================= end of body ========================= -->
							</tbody>
						</table>
					</td>
				</tr>

				<!-- ========================= footer ========================= -->
				<tr>
					<td bgcolor="#fafafa">
						<table border="0" cellpadding="40" cellspacing="0" width="720" align="center">
							<tbody>
								<tr>
									<td style="padding-top: 0; padding-bottom: 0;">
										<img src="https://supresso.com/newsletterv2/assets/supresso/img/signature-light.gif" width="100%" height="auto" style="vertical-align: sub;" alt="">
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="0" cellpadding="40" cellspacing="0" width="720" align="center">
							<tbody>
								<tr>
									<td style="padding-top: 30px; padding-bottom: 30px;">
										<table border="0" cellpadding="0" cellspacing="0" align="center">
											<tbody>
												<tr>
													<td style="padding-right: 20px; border-right: solid 1px #878787;">
														<table border="0" cellpadding="5" cellspacing="0">
															<tbody>
																<tr>
																	<td width="26">
																		<a href="#" target="_blank" style="text-decoration: none;">
																			<img src="https://supresso.com/newsletterv2/assets/supresso/img/ikon_fb_dark.png" width="26" height="26" style="vertical-align: sub;" alt="">
																		</a>
																	</td>
																	<td width="26">
																		<a href="#" target="_blank" style="text-decoration: none;">
																			<img src="https://supresso.com/newsletterv2/assets/supresso/img/ikon_ig_dark.png" width="26" height="26" style="vertical-align: sub;" alt="">
																		</a>
																	</td>
																	<td width="26">
																		<a href="#" target="_blank" style="text-decoration: none;">
																			<img src="https://supresso.com/newsletterv2/assets/supresso/img/ikon_phone_dark.png" width="26" height="26" style="vertical-align: sub;" alt="">
																		</a>
																	</td>
																	<td width="26" style="padding-right: 0;">
																		<a href="#" target="_blank" style="text-decoration: none;">
																			<img src="https://supresso.com/newsletterv2/assets/supresso/img/ikon_map_dark.png" width="26" height="26" style="vertical-align: sub;" alt="">
																		</a>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
													<td style="padding-left: 20px;">
														<p style="font-family: sans-serif; font-size: .6em; color: #323232; margin-top: 0; margin-bottom: .5em;">
															<a href="#" target="_blank" style="text-decoration: none; color: #323232;">
																View Web Version
															</a>
															<span style="padding-left: 5px; padding-right: 5px;">|</span>
															<a href="#" target="_blank" style="text-decoration: none; color: #323232;">
																Privacy Policy
															</a>
															<span style="padding-left: 5px; padding-right: 5px;">|</span>
															<a href="#" target="_blank" style="text-decoration: none; color: #323232;">
																Unsubscribe
															</a>
														</p>
														<p style="font-family: sans-serif; font-size: .6em; color: #323232; margin: 0;">
															333A Orchard Road #03-11 Mandarin Gallery Singapore 238897
															<br>Copyright &copy; 2022 Indraco. All Rights Reserved.
														</p>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td style="border-top: solid 1px #878787;">
						<table border="0" cellpadding="40" cellspacing="0" width="720" align="center">
							<tbody>
								<tr>
									<td style="padding-top: 20px; padding-bottom: 20px;">
										<p style="font-family: sans-serif; font-size: .6em; color: #bcbec0; margin: 0; text-align: justify;">
											<span style="color: #fd4f00;">Important warning and disclaimer :</span>
											<br><br>
											This message and any attachments are intended for the named and correctly identified addressee only. This message may contain confidential, proprietary, legally privileged or commercially sensitive information. No waiver of confidentiality or
											privilege is intended or authorized by this transmission. If you are not the intended recipient of this message you must not directly or indirectly use, reproduce, distribute, disclose, print, reply on, disseminate, or copy any part of the message or
											its attachments and if you have received this message in error, please notify the sender immediately by return e-mail and delete it from your system. The accuracy of the information in this e-mail is not guaranteed. Any opinions contained in this
											message are those of the author and are not given or endorsed unless otherwise clearly indicated in this message, and the authority of the author to act for and on behalf of Indraco Pte. Ltd. and its Subsidiaries is duly verified.
										</p>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<!-- ========================= end of footer ========================= -->

			</tbody>
		</table>

		<script type="text/javascript">
			// [].forEach.call(document.querySelectorAll("*"), function (a) { a.style.outline = "1px solid green" });
		</script>

	</body>

</html>
	';

			$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			// if ($namabelakang == "-") {
			// 	$updatedahkirimiemail = "UPDATE daftarorder SET lastname = '--' WHERE nomerorder = '" . $nmrorder . "' ";
			// 	$conn->query($updatedahkirimiemail);

			$mail->send();

			// echo $mail->Body;


			// } else {
			// }

			// if( !$mail->send() ) {
			// 	//return back()->with("failed", "Email not sent.")->withErrors($mail->ErrorInfo);
			// 	dd("failed 1");
			// 	}
			// 	else {
			// 		dd("success 1");
			// 	//return back()->with("success", "Email has been sent.");
			// 	}


			//$msg .= ' And,email has been sent';
		} catch (Exception $e) {
			$msg .= " Mail could not be sent. Mailer Error: {$mail->ErrorInfo}";
			//return back()->with('error','Message could not be sent.');
		}


		//return $charge;
	}
}
