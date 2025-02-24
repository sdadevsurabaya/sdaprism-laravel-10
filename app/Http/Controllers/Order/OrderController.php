<?php

namespace App\Http\Controllers\Order;


use App\Models\Order_model as OrderModel;
use App\Models\Order_detail_model as OrderDetailModel;

use App\Models\File;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Http\Controllers\webhook\CustomerEmailController;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;
use PDF;
use App\Models\Member_model as MemberModel;
use App\Models\User;

class OrderController extends Controller
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

    private function calculateGST($subtotal)
    {
        $gst = 1.08;
        return $subtotal - ($subtotal / $gst);
    }

    public function index(Request $request)
    {


        if ((!empty($request->start)) && (!empty($request->end))) {
            $arraydatestart = explode('-', $request->start);
            $arraydateend = explode('-', $request->end);


            $datefrom = $arraydatestart[2] . "-" . $arraydatestart[1] . "-" . $arraydatestart[0];
            $dateto = $arraydateend[2] . "-" . $arraydateend[1] . "-" . $arraydateend[0];

            // $datefrom = (str_replace("/","-",$sdatefrom));
            // $dateto = (str_replace("/","-",$sdateto));
            $order_models = DB::select("SELECT * from order_models where (tanggalorder BETWEEN '" . $datefrom . "' AND '" . $dateto . "') order by id desc");
        } else {
            $order_models = DB::select("SELECT * from order_models order by id desc");
        }

        // $order_models =  DB::table('order_models')
        //     ->get();

        //$order_models = DB::select("SELECT * from order_models where (tanggalorder BETWEEN '".$datefrom."' AND '".$dateto."') order by id desc");

        // dump($order_models);
        // dd($order_modelss);

        $order_models_vend = array_filter($order_models, function ($p) {
            return $p->company == "vend";
        });

        $order_models = array_filter($order_models, function ($p) {
            return $p->company != "vend";
        });

        $userEmail = auth()->user()->email;

        return view('orders/index', compact('order_models', 'order_models_vend', 'userEmail'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product_collection_models =  DB::table('product_collection_models')->get();
        $product_type_models =  DB::table('product_type_models')->get();
        $product_form_models =  DB::table('product_form_models')->get();
        $product_package_models =  DB::table('product_package_models')->get();
        return view('products/create', compact('product_collection_models', 'product_type_models', 'product_form_models', 'product_package_models'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'product_name' => 'required'
        ]);

        $files = [];
        if ($request->hasfile('filenames')) {
            foreach ($request->file('filenames') as $file) {
                $name = time() . rand(1, 100) . '.' . $file->extension();
                $file->move(public_path('files/product-images'), $name);
                $files[] = $name;
            }
        }

        $product_models = Product_model::create([
            'sku' => $request->sku,
            'product_name' => $request->product_name,
            'product_detail' => $request->product_detail,
            'product_shortdetail' => $request->product_shortdetail,
            'product_brand' => $request->product_brand,
            'product_collection' => $request->product_collection,
            'product_type' => $request->product_type,
            'product_form' => $request->product_form,
            'product_package' => $request->product_package,
            'product_price' => $request->product_price,
            'product_price_currency' => $request->product_price_currency,
            'product_weight' => $request->product_weight,
            'product_width' => $request->product_width,
            'product_height' => $request->product_height,
            'product_length' => $request->product_length,
            'product_acidityscore' => $request->product_acidityscore,
            'product_aciditydesc' => $request->product_aciditydesc,
            'product_bodyscore' => $request->product_bodyscore,
            'product_bodydesc' => $request->product_bodydesc,
            'product_roastdesc' => $request->product_roastdesc,
            'product_typedesc' => $request->product_typedesc,
            'product_intensity' => $request->product_intensity,
            'product_default_discount' => $request->product_default_discount,
            'fileimages' => $files,
            'status_stock' => $request->status_stock,
            'status' => $request->status
        ]);

        if ($product_models) {
            return redirect()
                ->route('products.index')
                ->with([
                    'success' => 'New post has been created successfully'
                ]);
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'error' => 'Some problem occurred, please try again'
                ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pict = [];
        $images = DB::table('files')->where('files.id_product', '=', $id)->get();

        for ($p = 0; $p < count($images); $p++) {
            $pct = $images[$p]->filenames;
            $fileimages = json_decode($images[$p]->filenames);

            for ($gimg = 0; $gimg < count($fileimages); $gimg++) {
                $imgs =  $fileimages[$gimg];
                array_push($pict, $imgs);
            }
            //dump($images[$p]->filenames[1]);

            //array_push($pict, $pct);
        }
        $product_models =  DB::table('product_models')
            ->where('product_models.id', '=', $id)
            // ->leftjoin(
            //     'product_kind_models',
            //     'product_models.product_kind',
            //     '=',
            //     'product_kind_models.id'
            // )
            // ->leftjoin(
            //     'product_variant_models',
            //     'product_models.product_variant',
            //     '=',
            //     'product_variant_models.id'
            // )
            // ->leftjoin(
            //     'product_category_models',
            //     'product_models.product_category',
            //     '=',
            //     'product_category_models.id'
            // )
            // ->leftjoin(
            //     'product_collection_models',
            //     'product_models.product_collection',
            //     '=',
            //     'product_collection_models.id'
            // )
            // ->select(
            //     'product_models.*',
            //     'product_kind_models.product_kind_name',
            //     'product_variant_models.product_variant_name',
            //     'product_category_models.product_category_name',
            //     'product_collection_models.product_collection_name'
            // )

            ->get();
        //dd($product_models);
        // return view('posts.index', compact('posts'));
        return view('products/show', compact('product_models', 'pict'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product_collection_models =  DB::table('product_collection_models')->get();
        $product_type_models =  DB::table('product_type_models')->get();
        $product_form_models =  DB::table('product_form_models')->get();
        $product_package_models =  DB::table('product_package_models')->get();

        $product_models = Product_model::findOrFail($id);
        $images = json_decode($product_models->fileimages);
        //dd($images);
        return view('products.edit', compact(
            'product_models',
            'product_collection_models',
            'product_type_models',
            'product_form_models',
            'product_package_models',
            'images'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //dd('test');
        $this->validate($request, [
            'product_name' => 'required'
        ]);


        $product_models = Product_model::findOrFail($id);

        $files = [];

        if ($request->hasfile('filenames')) {
            foreach ($request->file('filenames') as $file) {
                //dd($file->getClientOriginalName());
                $name = $file->getClientOriginalName();
                $file->move(public_path('files/product-images'), $name);
                $files[] = $name;
            }
            //dd($files);

            $product_models->update([
                'sku' => $request->sku,
                'product_name' => $request->product_name,
                'product_detail' => $request->product_detail,
                'product_shortdetail' => $request->product_shortdetail,
                'product_brand' => $request->product_brand,
                'product_collection' => $request->product_collection,
                'product_type' => $request->product_type,
                'product_form' => $request->product_form,
                'product_package' => $request->product_package,
                'product_price' => $request->product_price,
                'product_price_currency' => $request->product_price_currency,
                'product_weight' => $request->product_weight,
                'product_width' => $request->product_width,
                'product_height' => $request->product_height,
                'product_length' => $request->product_length,
                'product_acidityscore' => $request->product_acidityscore,
                'product_aciditydesc' => $request->product_aciditydesc,
                'product_bodyscore' => $request->product_bodyscore,
                'product_bodydesc' => $request->product_bodydesc,
                'product_roastdesc' => $request->product_roastdesc,
                'product_typedesc' => $request->product_typedesc,
                'product_intensity' => $request->product_intensity,
                'product_default_discount' => $request->product_default_discount,
                'fileimages' => $files,
                'status_stock' => $request->status_stock,
                'status' => $request->status
            ]);
        } else {

            dd($files);

            $product_models->update([
                'sku' => $request->sku,
                'product_name' => $request->product_name,
                'product_detail' => $request->product_detail,
                'product_shortdetail' => $request->product_shortdetail,
                'product_brand' => $request->product_brand,
                'product_collection' => $request->product_collection,
                'product_type' => $request->product_type,
                'product_form' => $request->product_form,
                'product_package' => $request->product_package,
                'product_price' => $request->product_price,
                'product_price_currency' => $request->product_price_currency,
                'product_weight' => $request->product_weight,
                'product_width' => $request->product_width,
                'product_height' => $request->product_height,
                'product_length' => $request->product_length,
                'product_acidityscore' => $request->product_acidityscore,
                'product_aciditydesc' => $request->product_aciditydesc,
                'product_bodyscore' => $request->product_bodyscore,
                'product_bodydesc' => $request->product_bodydesc,
                'product_roastdesc' => $request->product_roastdesc,
                'product_typedesc' => $request->product_typedesc,
                'product_intensity' => $request->product_intensity,
                'product_default_discount' => $request->product_default_discount,
                'status_stock' => $request->status_stock,
                'status' => $request->status
            ]);
        }

        // if ($request->hasfile('filenames')) {
        //     foreach ($request->file('filenames') as $file) {
        //         $name = time() . rand(1, 100) . '.' . $file->extension();
        //         $file->move(public_path('files/product-images'), $name);
        //         $files[] = $name;
        //     }
        // }



        if ($product_models) {
            return redirect()
                ->route('products.index')
                ->with([
                    'success' => 'Post has been updated successfully'
                ]);
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'error' => 'Some problem has occured, please try again'
                ]);
        }
    }

    public function updatetrackingnumber(Request $request)
    {
        dump($request->nomerorder);
        dump($request->trackingnumber);
        //dd("update tracking number");
        $update = DB::update('UPDATE order_models SET tracking_number = "' . $request->trackingnumber . '" WHERE nomerorder ="' . $request->nomerorder . '"');
        if ($update) {
            return redirect()
                ->route('orders.index')
                ->with([
                    'success' => 'New post has been created successfully'
                ]);
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'error' => 'Some problem occurred, please try again'
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $product_models = Product_model::findOrFail($id);

        $product_models->update([
            'deleted' => 'true',
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product Types deleted successfully');
    }

    public function getOrderNumber()
    {
        $LastOrder = DB::select("select nomerorder from order_models WHERE company != 'local_payment' AND nomerorder NOT LIKE '%.%' ORDER BY id desc limit 1");
       
        if (count($LastOrder)>0) {
            $lastOrderArr = explode("-", $LastOrder[0]->nomerorder);
            $OrderNumber = ($lastOrderArr[2]) + 1;
        }else
        {
            $OrderNumber = 1;
        }
        return $OrderNumber;
    }

    public function getOrderData(Request $r)
    {
        $orderModel = new OrderModel();
        $orderData  = $orderModel->where('id', $r->id)->first();
        $orderDetails = OrderDetailModel::where('nomerorder', $orderData->nomerorder)->get();
        return response([
            "msg" => "success",
            "data" => [
                "order" => $orderData,
                "details" => $orderDetails
            ]
        ], 200);
    }

    public function updateTracking(Request $r)
    {

        $orderModel = new OrderModel();
        $orderData  = $orderModel->where('id', $r->id)->first();
        $orderData->tracking_number = $r->tracking;

        try {
            $orderData->save();
        } catch (\Throwable $th) {
            return response([
                "msg" => "error",
                "data" => $th->getMessage()
            ], 500);
        }

        return response([
            "msg" => "success",
            "data" => "Tracking ID has successfully update"
        ], 200);
    }

    public function updateStatus(Request $r)
    {

        $orderModel = new OrderModel();
        $orderData  = $orderModel->where('id', $r->id)->first();
        $orderData->status = $r->status;

        try {
            $orderData->save();
        } catch (\Throwable $th) {
            return response([
                "msg" => "error",
                "data" => $th->getMessage()
            ], 500);
        }

        return response([
            "msg" => "success",
            "data" => "Tracking ID has successfully update"
        ], 200);
    }

    public function updateShipingStatus(Request $r)
    {
        $orderModel = new OrderModel();
        $orderData  = $orderModel->where('id', $r->id)->first();
        $orderData->shiping_status = $r->status;

        # if shipping status is delivered then order status is complete
        $msgComplete = "";
        if ($r->status == "Delivered") {
            $orderData->status = "complete";
            $msgComplete = " and order status changed to complete. refresh to see the different";
        }

        try {
            $orderData->save();
        } catch (\Throwable $th) {
            return response([
                "msg" => "error",
                "data" => $th->getMessage()
            ], 500);
        }

        return response([
            "msg" => "success",
            "data" => "Shiping status has successfully update" . $msgComplete
        ], 200);
    }

    public function showAwb(Request $r)
    {
        $orderModel     = new OrderModel();
        $expOrderNumber = explode('-', $r->id);
        $nomerorder     = $expOrderNumber[0] . "/" . $expOrderNumber[1];

        $prop = DB::select("
                            SELECT
                            SUM(odm.qty) as jumlah_qty,
                            SUM(pm.product_weight) as jumlah_weight
                            FROM 
                                order_detail_models odm INNER JOIN 
                                product_models pm ON pm.id = odm.idproduct 
                            WHERE 
                                nomerorder = '" . $nomerorder . "';
                            ");

        $orderData  = $orderModel->where('nomerorder', $nomerorder)->first();
        return view('ui/awb', ["data" => $orderData, "prop" => $prop[0]]);
    }

    public function sendResi(Request $r)
    {

        // get order data
        $orderId = $r->id;
        $orderModel = OrderModel::where('id', $orderId)->first();

        // create parameter for sending email
        $custData = new stdClass;
        $custData->orderData = $orderModel;
        // end

        // sending resi or tracking shipping email
        $customerEmailController = new CustomerEmailController;
        try {
            $customerEmailController->sendResi($custData);
        } catch (\Throwable $th) {
            return response([
                "msg" => "error",
                "data" => $th->getMessage()
            ], 500);
        }

        return response([
            "msg" => "success",
            "data" => "Tracking email has been sent to customer"
        ], 200);
    }

    public function sendEmailConfirmation(Request $r)
    {

        // get order data
        $orderId = $r->id;
        $getOrderData = OrderModel::where('id', $orderId)->first();
        $getOrderDetailDatas = OrderDetailModel::where('nomerorder', $getOrderData->nomerorder)->get();

        // create parameter for sending email
        $custData = new stdClass;
        $custData->orderData = $getOrderData;
        $custData->orderDetailDatas = $getOrderDetailDatas;

        // sending resi or tracking shipping email
        $customerEmailController = new CustomerEmailController;
        try {
            $customerEmailController->sendEmailConfirmation($custData);
        } catch (\Throwable $th) {
            return response([
                "msg" => "error",
                "data" => $th->getMessage()
            ], 500);
        }

        return response([
            "msg" => "success",
            "data" => "Confirmation email has been sent to customer"
        ], 200);
    }

    public function getPdfInvoice(Request $r)
    {
        // dd("get pdf");

        $orderId = $r->idorder;
        $getOrderData = OrderModel::where('id', $orderId)->first();
        $getOrderDetailDatas = OrderDetailModel::where('nomerorder', $getOrderData->nomerorder)->get();

        // create parameter for sending email
        $custData = new stdClass;
        $custData->orderData = $getOrderData;
        $custData->orderDetailDatas = $getOrderDetailDatas;

        $customerEmailController = new CustomerEmailController;

        $order          = $custData->orderData;
        $orderDetails   = $custData->orderDetailDatas;

        $order          = $custData->orderData;
        $orderDetails   = $custData->orderDetailDatas;
        $member         = MemberModel::where('id', $order->iduser)->first();
        $resultGST      = $this->calculateGST($order->ordertotal);
        $resultGST      = round($resultGST, 2, PHP_ROUND_HALF_UP);
        $jmsubtotbelum  = 0;
        $subglobal      = $order->itemsubtotal;

        $loopAppendProduct = '';
        if (count($orderDetails) > 0) {
            foreach ($orderDetails as $item) {

                $subtotalbelum  = $item->hargabelumdiskon * $item->qty;
                $subtotalbelum  = number_format((float)$subtotalbelum, 2, '.', '');
                $txtdiskon      = $item->txtdiskon;
                $jmsubtotbelum  = $jmsubtotbelum + $subtotalbelum;


                // append discount 
                $prctag = '';
                $dsctag = '';
                $isDiscount = $item->hargabelumdiskon != $item->hargaproduk;
                if ($isDiscount) {
                    $prctag = '<p align="right" style="font-family: sans-serif; padding: 0; margin-top: 0; margin-bottom: .5em; opacity: .5;"><del>Rp ' . number_format($subtotalbelum, 0, ',', '.') . '</del></p>';

                    $dsctag = '<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%!important;"><tbody><tr>
                            <td valign="middle" align="left"><table width="15" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 15px!important;"><tbody><tr><td valign="top" align="center">
                                <p style="padding: 0; margin: 0; font-size: 0; opacity: .5;">
                                    <img src="https://supresso.com/sg/img/pricetag.png" width="15" style="width: 15px!important; height: auto; max-width: 15px;">
                                </p>
                            </td></tr></tbody></table></td>
                            <td valign="middle" align="left"><table width="335" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 335px!important;"><tbody><tr><td valign="top" align="center">
                                <p align="left" style="font-family: sans-serif; padding: 0; margin: 0; opacity: .5;">
                                    &nbsp;Discount&nbsp;' . $txtdiskon . '
                                </p>
                            </td></tr></tbody></table></td>
                        </tr></tbody></table>';
                }

                // append body product
                $loopAppendProduct .= '
        <tr><td valign="top" align="left">
        <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
            <!-- produk item -->
            <tr><td valign="top" align="left">
                <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important; border-bottom: solid 1px #bcbec0;"><tbody>
                    <tr>
                        <td valign="center" align="left">
                            <table width="150" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 150px!important;"><tbody><tr><td valign="center" align="left">
                                <p style="font-size: 0;">
                                    <img src="' . url('files/product-images/') . '/' . $item->gambar . '" width="150" style="width: 100%; height: auto; max-width: 150px">
                                </p>
                            </td></tr></tbody></table>
                        </td>
                        <td valign="center" align="left">
                            <table width="350" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 350px!important;"><tbody><tr><td valign="center" align="left">
                                <p style="font-family: sans-serif; padding: 0; margin: 0; text-transform: uppercase; max-width: 75%;">
                                    ' . $item->namaproduk . '
                                </p>
                                <p style="font-family: sans-serif; padding: 0; margin-top: 0; margin-bottom: .5em; text-transform: uppercase;">
                                    <span>Qty</span></span>&nbsp;<span>&nbsp;</span>&nbsp;<span>' . $item->qty . '</span>
                                </p>
                                
                                ' . $dsctag . ' <!-- disctag -->
                                
                            </td></tr></tbody></table>
                        </td>
                        
                        
                    <td valign="center" align="left">
                            <table width="140" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 140px!important;"><tbody><tr><td valign="center" align="left">
                                ' . $prctag . ' <!-- $prctag -->
                                <p align="right" style="font-family: sans-serif; padding: 0; margin: 0;">Rp ' . number_format($item->subtotalproduk, 0, ',', '.') . '</p>
                            </td></tr></tbody></table>
                        </td>
                        
                    </tr>
                </tbody></table>
                </td></tr>
        ';
            }
        }


        $data = [
            'title' => 'Welcome to ItSolutionStuff.com',
            'date' => date('m/d/Y'),
            'order' => $getOrderData,
            'loopAppendProduct' => $loopAppendProduct,
            'orderDetails' => $orderDetails
        ];

        //dd($loopAppendProduct);

        // $order = $getOrderData;

        //dd($order->namalengkap);

        //return view('orders.invoice', $data);
        $pdf = PDF::loadView('orders.invoice', $data)->setPaper('a4', 'portrait');
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed' => TRUE,
                    'verify_peer' => FALSE,
                    'verify_peer_name' => FALSE,
                ]
            ])
        );
        // PDF::setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        ini_set('memory_limit', '128M');
        // $pdf = $pdf->setOption(['dpi' => 72, 'defaultFont' => 'sans-serif']);
        return $pdf->download('invoice-' . $getOrderData->nomerorder . '.pdf');

        // // get order data
        // $orderId = $r->id;
        // $getOrderData = OrderModel::where('id', $orderId)->first();
        // $getOrderDetailDatas = OrderDetailModel::where('nomerorder', $getOrderData->nomerorder)->get();

        // // create parameter for sending email
        // $custData = new stdClass;
        // $custData->orderData = $getOrderData;
        // $custData->orderDetailDatas = $getOrderDetailDatas;

        // // sending resi or tracking shipping email
        // $customerEmailController = new CustomerEmailController;
        // try {
        //     $customerEmailController->getPdfInvoice($custData);
        // } catch (\Throwable $th) {
        //     return response([
        //         "msg" => "error creating pdf",
        //         "data" => $th->getMessage()
        //     ], 500);
        // }

        // return response([
        //     "msg" => "success",
        //     "data" => "PDF has been created"
        // ], 200);
    }
}
