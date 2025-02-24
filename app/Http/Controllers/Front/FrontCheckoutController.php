<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade;

use App\Models\Order_detail_model as OrderDetailModel;
use App\Models\Order_model as OrderModel;
use App\Models\Product_model as ProductModel;


use Illuminate\Support\Facades\Validator;
use Stripe\Exception\CardException;
use Stripe\StripeClient;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Stripe\Exception\ApiErrorException;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Redirect;

class FrontCheckoutController extends Controller
{
    private $stripe;
    public function __construct()
    {
        
    }

    public function index(Request $request)
    {

        $statuscheckout = $request->checkout;

        //dd($statuscheckout);
        
        $res_allproduct = DB::select('select p.*, b.name as brand, c.name as category, sc.name as subcategory from pos_products as p
        LEFT JOIN pos_brand as b on b.id = p.id_brand
        LEFT JOIN pos_category as c on c.id = p.id_category
        LEFT JOIN pos_sub_category as sc on sc.id = p.id_sub_category where p.deleted="false"');

        $cartItem = Cart::getContent();
        //dd($cartItem);
        //\Cart::clear();
        Cart::clearCartConditions();
        session(['totalbeforediscount' => Cart::getTotal()]);

       
        $conditiontax = new \Darryldecode\Cart\CartCondition(array(
            'name' => 'TAX 10.0%',
            'type' => 'tax',
            'target' => 'subtotal', // this condition will be applied to cart's subtotal when getSubTotal() is called.
            'value' => '10.0%',
            'attributes' => array( // attributes field is optional
                'description' => 'Value added tax',
                'more_data' => 'more data here'
            )
        )); 
        Cart::condition($conditiontax);
        $total = Cart::getTotal();
        $subtotal = Cart::getSubTotal();
        $subtotalwithoutconditions = Cart::getSubTotalWithoutConditions();
        
        $tax = ($subtotalwithoutconditions * 10/100);
        $cartItems = Cart::getContent();

        #ambil session id gift yang dipilih
        $gift_box_id_chosen = Session::get('gift');

        ## get data cart
        $cartItem = Cart::getContent();
        $cartItems = $cartItem->sort();

        # mencari cart sub total
        $cartSubTotal = CartFacade::getSubTotal();

        #normalisasi product dan additional dalam satu array object
        $product = [];
        $keranjang = [];
        foreach ($cartItems as $key => $items) {
        if (strpos($items->id, 'menu-') !== false) {
            $normalisasi_id_item = str_replace('menu-', '', $items->id);
            $iditem = ((explode("-",$normalisasi_id_item))[0]);
            $sql = "select * from pos_additional_products as ap INNER JOIN pos_additional as a on ap.id_additional = a.id where ap.id_product='".$iditem."' and ap.deleted='false'";
            $items->additional=DB::select("select * from pos_additional_products as ap INNER JOIN pos_additional as a on ap.id_additional = a.id where ap.id_product='".$iditem."' and ap.deleted='false'");
            $items->optional = DB::select("select * from pos_optional_products as op INNER JOIN pos_optional as a on op.id_optional = a.id where op.id_product=".$iditem." and op.deleted='false'");
            $items->productid = $iditem;
            array_push($product, $items);
        }
        }
        #end

        # generate order number
		$OrderNumber = app('App\Http\Controllers\Order\OrderController')->getOrderNumber();
		// $member 	 = app('App\Http\Controllers\Member\MemberController')->GetMemberInformation();

        # date time generate
        date_default_timezone_set('Asia/Jakarta');
        $date       = date('Y-m-d');
        $dateymd    = date('Ymd');
        $time       = date("H:i:s");

        # generate nomer order
        $nomerOrder = ((Auth::user()->name))."-".$dateymd."-".$OrderNumber;

        $cartDiscount = 0;
        $ShippingPrice = 0;
        $totalPrice = $cartSubTotal - $cartDiscount + $ShippingPrice;

        //dd($nomerOrder);

        # simpan order pada tabel
        $orderModel = new OrderModel();
        $orderModel->nomerorder = $nomerOrder;
        $orderModel->iduser = 1;
        $orderModel->tanggalorder = $dateymd;
        $orderModel->jamorder = $time;
        $orderModel->status = 'on-hold';
        $orderModel->meja = Str::ucfirst(Auth::user()->name);
        $orderModel->itemsubtotal = $cartSubTotal;
        $orderModel->discon = 0;
        $orderModel->coupon = '-';
        $orderModel->kodekupon = '-';
        $orderModel->persdiskon = '-';
        $orderModel->tax = 0;
        $orderModel->shippingprice = 0;
        $orderModel->ordertotal = $totalPrice;
        $orderModel->payment = "-";
        $orderModel->pengiriman = "-";
        $orderModel->namalengkap = "-";
        $orderModel->namalengkapawb = "-";
        $orderModel->firtsname = '';
        $orderModel->lastname = '';
        $orderModel->negara = "Indonesia";
        $orderModel->provinsi = '-';
        $orderModel->kota = '-';
        $orderModel->kecamatan = '';
        $orderModel->alamat = '-';
        $orderModel->alamatawb = '-';
        $orderModel->alamatdua = '-';
        $orderModel->kodepos = '-';
        $orderModel->email = '-';
        $orderModel->emailtanpatitik = '-';
        $orderModel->phoneawb = '-';
        $orderModel->phone = '-';
        $orderModel->addcatatan = '-';
        $orderModel->addcatatanawb = "-";
        $orderModel->statusawb = "-";
        $orderModel->notifnews = "";
        $orderModel->deleted = "false";

        $orderModel->payment_id = '-';
        $orderModel->payment_method = '-';
		$orderModel->payment_status = "incomplete";
		$orderModel->shipping_id = '-';
		$orderModel->country_code = "ID";
		$orderModel->company = "Indraco";

        $orderCount = (Cart::getTotalQuantity());

        if (Cart::getTotalQuantity()!=0) {
            try {
                $orderModel->save();
            } catch (\Throwable $th) {
                return response(["msg" => "Error save order detail" . $th->getMessage()], 500 );
            }
        }
        else
        {
        # return to menu, if cart is null
        return Redirect::to('/menu');
        }
        # end


 dump($cartItems);


         # save to order detail
         foreach ($cartItems as $row) {

			# check cart kategori khusus (disable this feature because this feature only on supresso)
			// $webhookController  = new WebhookController;
			// $isGiftSet 			= $webhookController->isGiftSet($row->id);
            
			$productprice 	= $row->price;
			$idProduct 		= $row->id;
			// $giftsetId		= null;
			// $giftsetProp	= null;
			$productName	= $row->name;

		

			OrderDetailModel::create([
				'nomerorder' => $nomerOrder,
				'idproduct' => $idProduct,
				'iduser' => 0,
				'namaproduk' => $productName,
				'gambar' => $row->attributes->images,
				'discon' => $row->attributes->discount == null ? 0 : $row->attributes->discount,
				'txtdiskon' => $row->attributes->discount . "%",
				'tax' => 0,
				'hargabelumdiskon' => $productprice,
				'hargaproduk' => $row->price,
				'qty' => $row->quantity,
				'subtotalproduk' => ($row->quantity * $row->price),
				'note' => "-",
				'review' => "-",
				'status' => "active",
				'deleted' => "false",
				'giftset_id' => 0,
				'giftset_prop' => 0
			]);

            
		}
        # end
        
       
        $total = Cart::getTotal();
        $subtotal = Cart::getSubTotal();
        $subtotalwithoutconditions = Cart::getSubTotalWithoutConditions();
        
        $tax = ($subtotalwithoutconditions * 10/100);

        // dump($total);
        // dump($subtotal);
        // dump($subtotalwithoutconditions);
        // dump($tax);
        CartFacade::clear();
        $title = "Cart";
        $pages = "cart";
        return view('waiters/resumeorder', compact('cartItems', 'title', 'pages', 'product','total','subtotal','subtotalwithoutconditions','tax', 'res_allproduct','nomerOrder'));      
      
    }

    public function AddShipping(Request $request)
    {
      
    }

    public function getFreeGift()
    {
     
    }


    public function getCountries()
    {
       
    }

    public function getShippingVendor()
    {
      
    }

    public function indexshipping()
    {

      
    }

    public function paymentstripe(Request $request)
    {
      
    }

    public function paymentpaypal(Request $request)
    {
       
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
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
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

    public function createSession(Request $request)
    {
        $session = $this->stripe->checkout->sessions->create([
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'customer_email'       => 'webdevtest@gmail.com',
            'client_reference_id'  => '1234',
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [
                [
                    'price_data' =>
                    [
                        'currency' => 'sgd', 
                        'product_data' => ['name' => 'Supresso Coffee',], 
                        'unit_amount' => 45000,
                    ],
                    'quantity' => 1,
                ]
            ],
        ]);
      

        if ($session) {
            $res = response()->json(
                [
                    'status' => 'success',
                    'sessionId' => $session->id,

                ],
                200
            );
        } else {
            $res = response()->json(
                [
                    'status' => 'failed',
                    'sessionId' => $session->id,
                ],
                500
            );
        };
        return $res;
       
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



















    public function payment(Request $request)
    {
        // dump("ini payment");
        // dump("cfullname = " . $request->cfullname);
        // dump("cemail = " . $request->cemail);
        // dump("cphone = " . $request->cphone);
        // dump("cshippingto = " . $request->cshippingto);
        // dump("crecepientsFullName = " . $request->crecepientsFullName);
        // dump("crecepientsEmail = " . $request->crecepientsEmail);
        // dump("crecepientsPhone = " . $request->crecepientsPhone);
        // dump("crecepientsState = " . $request->crecepientsState);
        // dump("crecepientsProvince = " . $request->crecepientsProvince);
        // dump("crecepientsCity = " . $request->crecepientsCity);
        // dump("cpostcode = " . $request->cpostcode);
        // dump("caddress1 = " . $request->caddress1);
        // dump("caddress2 = " . $request->caddress2);
        // dump("cmessage = " . $request->cmessage);
        // dump("cpayment = " . $request->cpayment);
        // dump("cnotifikasiNews = " . $request->cnotifikasiNews);
        // dump("cnotifikasiTnc = " . $request->cnotifikasiTnc);
        // dump("cShipperName = " . $request->cShipperName);
        // dump("cShippingPrice = " . $request->cShippingPrice);
        // dump("total = " . $request->total);
        $total = ((\Cart::getTotal()));
        // dump($total);


        $session = $this->stripe->checkout->sessions->create([
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'customer_email'       => $request->cemail,
            'client_reference_id'  => '1234',
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [
                [
                    'price_data' =>
                    [
                        'currency' => 'sgd', 
                        'product_data' => ['name' => 'Supresso Coffee',], 
                        'unit_amount' => 45000,
                    ],
                    'quantity' => 1,
                ]
            ],
        ]);
      

        if ($session) {
            $res = response()->json(
                [
                    'status' => 'success',
                    'sessionId' => $session->id,

                ],
                200
            );
        } else {
            $res = response()->json(
                [
                    'status' => 'failed',
                    'sessionId' => $session->id,
                ],
                500
            );
        };
        return $res;











        

        // dd("ini FrontCheckoutController@payment");

        // $cfullname = $request->cfullname;
        // $cemail = $request->cemail;
        // $cphone = $request->cphone;
        // $cshippingto = $request->cshippingto;
        // $crecepientsFullName = $request->crecepientsFullName;
        // $crecepientsEmail = $request->crecepientsEmail;
        // $crecepientsPhone = $request->crecepientsPhone;
        // $crecepientsState = $request->crecepientsState;
        // $crecepientsProvince = $request->crecepientsProvince;
        // $crecepientsCity = $request->crecepientsCity;
        // $cpostcode = $request->cpostcode;
        // $caddress1 = $request->caddress1;
        // $caddress2 = $request->caddress2;
        // $cmessage = $request->cmessage;
        // $cpayment = $request->cpayment;
        // $cnotifikasiNews = $request->cnotifikasiNews;
        // $cnotifikasiTnc = $request->cnotifikasiTnc;
        // $cShipperName = $request->cShipperName;
        // $cShippingPrice = $request->cShippingPrice;

        // $condition = new \Darryldecode\Cart\CartCondition(array(
        //     'name' => $request->cShipperName,
        //     'type' => 'shipping',
        //     'target' => 'total', // this condition will be applied to cart's total when getTotal() is called.
        //     'value' => '+' . $request->cShippingPrice,
        //     'order' => 1 // the order of calculation of cart base conditions. The bigger the later to be applied.
        // ));
        // \Cart::condition($condition);

        // $cartSubTotal = \Cart::getSubTotal();
        // $totalbeforediscount = session('totalbeforediscount');

        // dump("totalbeforediscount = " . $totalbeforediscount);
        // dump("SubTotal = " . $cartSubTotal);

        // dump(\Cart::getContent());
        // dump(\Cart::getConditions());

        // $cartDiscount = ((\Cart::getCondition('coupon'))->getValue()) * -1;
        // dump($cartDiscount);
        // $cartTotal = \Cart::getTotal();

        // dump("Total = " . $cartTotal);
        // //==============================================================================================================================================================================
        // // $this->validate($request, [
        // //     'product_name' => 'required'
        // // ]);

        // // $files = [];
        // // if ($request->hasfile('filenames')) {
        // //     foreach ($request->file('filenames') as $file) {
        // //         $name = time() . rand(1, 100) . '.' . $file->extension();
        // //         $file->move(public_path('files/product-images'), $name);
        // //         $files[] = $name;
        // //     }
        // // }

        // $OrderNumber = app('App\Http\Controllers\Order\OrderController')->getOrderNumber();
        // $member = app('App\Http\Controllers\Member\MemberController')->GetMemberInformation();
        // date_default_timezone_set('Asia/Jakarta');
        // $date = date('Y-m-d');
        // $time = date("H:i:s");
        // dump($time);

        // $couponDiscount = (session('coupon'));

        // dump($couponDiscount);
        // dd("end payment");

        // $order_models = Order_model::create([
        //     'nomerorder' => $OrderNumber,
        //     'iduser' => $member[0]->id_users_login,
        //     'tanggalorder' => $date,
        //     'jamorder' => $time,
        //     'status' => 'on-hold',
        //     'statustrack' => '-',
        //     'itemsubtotal' => $cartSubTotal,
        //     'discon' => $cartDiscount,

        //     'coupon' => $request->coupon,
        //     'kodekupon' => $request->kodekupon,
        //     'persdiskon' => $request->persdiskon,
        //     'tax' => $request->tax,
        //     'shippingprice' => $request->shippingprice,
        //     'ordertotal' => $request->ordertotal,
        //     'payment' => $request->payment,
        //     'pengiriman' => $request->pengiriman,
        //     'namalengkap' => $request->namalengkap,
        //     'namalengkapawb' => $request->namalengkapawb,
        //     'firtsname' => $request->firtsname,
        //     'lastname' => $request->lastname,
        //     'negara' => $request->negara,
        //     'provinsi' => $request->provinsi,
        //     'kota' => $request->kota,
        //     'kecamatan' => $request->kecamatan,
        //     'alamat' => $request->alamat,
        //     'alamatawb' => $request->alamatawb,
        //     'alamatdua' => $request->alamatdua,
        //     'kodepos' => $request->kodepos,
        //     'company' => $request->company,
        //     'email' => $request->email,
        //     'emailtanpatitik' => $request->emailtanpatitik,
        //     'phone' => $request->phone,
        //     'phoneawb' => $request->phoneawb,
        //     'addcatatan' => $request->addcatatan,
        //     'addcatatanawb' => $request->addcatatanawb,
        //     'statusawb' => $request->statusawb,
        //     'notifnews' => $request->notifnews,
        //     'deleted' => $request->deleted



        // ]);

        // if ($order_models) {
        //     return redirect()
        //         ->route('products.index')
        //         ->with([
        //             'success' => 'New post has been created successfully'
        //         ]);
        // } else {
        //     return redirect()
        //         ->back()
        //         ->withInput()
        //         ->with([
        //             'error' => 'Some problem occurred, please try again'
        //         ]);
        // }


        // dd("end payment");
    }
}