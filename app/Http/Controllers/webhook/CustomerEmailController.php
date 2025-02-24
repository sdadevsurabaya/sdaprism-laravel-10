<?php

namespace App\Http\Controllers\webhook;

use App\Http\Controllers\Controller;
use App\Models\Order_model;
use App\Models\Member_model as MemberModel;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PDF;

class CustomerEmailController extends Controller
{
    
    private function calculateGST($subtotal){
        $gst = 1.08;
        return $subtotal - ($subtotal / $gst);
    }

    public function getPdfInvoice($custData){

        $data = [

            'title' => 'Welcome to ItSolutionStuff.com',

            'date' => date('m/d/Y')

        ];

          

        $pdf = PDF::loadView('orders.invoice', $data);

    

        return $pdf->download('itsolutionstuff.pdf');
    }

    public function sendEmailConfirmation($custData){

        $order          = $custData->orderData;
        $orderDetails   = $custData->orderDetailDatas;
        $member         = MemberModel::where('id', $order->iduser)->first();
        $resultGST      = $this->calculateGST($order->ordertotal);
        $resultGST      = round($resultGST, 2, PHP_ROUND_HALF_UP);
        $jmsubtotbelum  = 0;
        $subglobal      = $order->itemsubtotal;
        
        $loopAppendProduct = '';
        if (count($orderDetails) > 0){
            foreach($orderDetails as $item){

                $subtotalbelum  = $item->hargabelumdiskon * $item->qty;
                $subtotalbelum  = number_format((float)$subtotalbelum, 2, '.', '');
                $txtdiskon      = $item->txtdiskon;
                $jmsubtotbelum  = $jmsubtotbelum + $subtotalbelum;


                // append discount 
                $prctag = '';
                $dsctag = '';
                $isDiscount = $item->hargabelumdiskon != $item->hargaproduk;
                if ($isDiscount){
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
                                            <span>Qty</span></span>&nbsp;<span>&nbsp;</span>&nbsp;<span>' .$item->qty . '</span>
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

        $selisihdisc = $jmsubtotbelum - $subglobal;
        $savedyou = number_format((float)$selisihdisc, 2, '.', '');
        $yousavedisi = '';
        if ($savedyou != 0.00 && !empty($savedyou)) {
            $yousavedisi = '<p align="right" style="font-family: sans-serif; padding: 0; margin: 0;">
                            You saved&nbsp;$&nbsp;' . $savedyou . '<span style="opacity: 0; visibility: hidden;">&nbsp;</span>
                            </p>';
        }
        
        // append coupon
        $adacoupon = '';
        if ($order->discon != null && $order->discon > 0 || $order->coupon != null){

            $hastagcoupon   = $order->kodekupon;
            $persdiskon     = $order->persdiskon;
            $jmldisconya    = $order->discon;

            if ($order->kodekupon == "freeshipping"){
                $adacoupon = '<tr>
                <td valign="top" align="left">
                <table width="500" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 500px!important;"><tbody><tr><td valign="top" align="left">
                  <p style="font-family: sans-serif; padding: 0; margin: 0;">
                <img src="'.url('ui/img/pricetag.png').'" width="15" style="width: 15px!important; height: auto; max-width: 15px;">&nbsp;' . $order->coupon . '&nbsp;Coupon&nbsp;<span>&#40;Free Shipping&#41;</span>
                    </p>
                </td></tr></tbody></table>
                    </td>
                    <td valign="top" align="left">
                    <table width="40" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 40px!important;"><tbody><tr><td valign="top" align="left">
                    <p align="center" style="font-family: sans-serif; padding: 0; margin: 0;">$</p>
                    </td></tr></tbody></table>
                    </td>
                    <td valign="top" align="left">
                    <table width="100" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100px!important;"><tbody><tr><td valign="top" align="left">
                    <p align="right" style="font-family: sans-serif; padding: 0; margin: 0;">' . $jmldisconya . '<span style="opacity: 0; visibility: hidden;">&nbsp;</span></p>
                                </td></tr></tbody></table>
                            </td>
                        </tr>';
            } else {
                $adacoupon = '<tr>
                <td valign="top" align="left">
                <table width="500" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 500px!important;"><tbody><tr><td valign="top" align="left">
                  <p style="font-family: sans-serif; padding: 0; margin: 0;">
                <img src="'.url('img/pricetag.png').'" width="15" style="width: 15px!important; height: auto; max-width: 15px;">&nbsp;' . $hastagcoupon . '&nbsp;Coupon&nbsp;<span>&#40;</span>Discount ' . $persdiskon . '<span>&#41;</span>
                    </p>
                </td></tr></tbody></table>
                    </td>
                    <td valign="top" align="left">
                    <table width="40" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 40px!important;"><tbody><tr><td valign="top" align="left">
                    <p align="center" style="font-family: sans-serif; padding: 0; margin: 0;">$</p>
                    </td></tr></tbody></table>
                    </td>
                    <td valign="top" align="left">
                    <table width="100" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100px!important;"><tbody><tr><td valign="top" align="left">
                    <p align="right" style="font-family: sans-serif; padding: 0; margin: 0;">-' . $jmldisconya . '<span style="opacity: 0; visibility: hidden;">&nbsp;</span></p>
                                </td></tr></tbody></table>
                            </td>
                        </tr>';
            }

          
        }

        $body = '
        <!DOCTYPE html>
        <html lang="en" style="padding: 0; margin: 0; background-color: #ffffff; width: 100%!important;">
        <head>
            <meta charset="utf-8">
            <title>Supresso - Order Confirmation</title>
            <link rel="stylesheet icon" type="text" href="https://supresso.com/newsletter/mtemplate/img/logo.png">
        </head>
        <body style="padding: 0; margin: 0; background-color: #ffffff; width: 100%!important;">

            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%!important;"><tbody>

                <tr style="display: none; max-height: 0; overflow: hidden"><td valign="top" align="left">
                    <module name="preheader" label="Preheader"></module>
                    <editable name="preheader">Thanks for your order. You will receive a shipping confirmation and tracking e-mail as soon as your coffee is ready.</editable>
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                </td></tr>
                
                <tr><td valign="top" align="left">
                    <table width="720" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 720px!important;"><tbody>
                        
                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
                                <tr><td valign="top" align="left" height="20"></td></tr>
                                <tr><td valign="top" align="left">
                                    <p style="font-size: 0;">
                                    <img src="https://supresso.com/newsletter/mtemplate/img/logo.png" width="71" height="71" style="vertical-align: sub;width:71px;height:71px">
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="left" height="50"></td></tr>
                                <tr><td valign="top" align="left">
                                    <h1 style="font-family: sans-serif; padding: 0; margin: 0;">
                                        <strong>Your order confirmation!</strong>
                                    </h1>
                                </td></tr>
                                <tr><td valign="top" align="left" height="20"></td></tr>
                                <tr><td valign="top" align="left">
                                    <p style="font-family: sans-serif; padding: 0; margin: 0;">
                                        Hi&nbsp;<strong>' . $order->namalengkap . '</strong>,
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="left" height="10"></td></tr>
                                <tr><td valign="top" align="left">
                                    <p style="font-family: sans-serif; padding: 0; margin: 0;">
                                        Thank you for your order! You will receive a shipping confirmation and tracking e-mail as soon as your coffee is ready.
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="left" height="30"></td></tr>
                                <tr><td valign="top" align="left">
                                    <p style="font-family: sans-serif; padding: 0; margin: 0;">
                                        <strong>Here<span>&#39;</span>s what you ordered :</strong>
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="left" height="10"></td></tr>
                            </tbody></table>
                        </td></tr>

                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important"><tbody>
                                <tr>
                                    <td valign="top" align="left">
                                        <table width="200" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 200px!important"><tbody><tr><td valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin: 0;">Order Number</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="40" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 40px!important"><tbody><tr><td valign="top" align="left">
                                            <p align="center" style="font-family: sans-serif; padding: 0; margin: 0;">:</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="400" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 400px!important"><tbody><tr><td valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin: 0;">' . $order->nomerorder . '</p>
                                        </td></tr></tbody></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" align="left">
                                        <table width="200" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 200px!important"><tbody><tr><td valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin: 0;">Courier</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="40" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 40px!important"><tbody><tr><td valign="top" align="left">
                                            <p align="center" style="font-family: sans-serif; padding: 0; margin: 0;">:</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="400" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 400px!important"><tbody><tr><td valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin: 0;"> ' . $order->pengiriman . '</p>
                                        </td></tr></tbody></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" align="left">
                                        <table width="200" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 200px!important"><tbody><tr><td valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin: 0;">Billing Address</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="40" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 40px!important"><tbody><tr><td valign="top" align="left">
                                            <p align="center" style="font-family: sans-serif; padding: 0; margin: 0;">:</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="400" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 400px!important"><tbody><tr><td valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin: 0;">' .$order->alamat. ', ' . $order->kota . ' ' .$order->kodepos. ' - ' . $order->negara . '</p>
                                        </td></tr></tbody></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" align="left">
                                        <table width="200" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 200px!important"><tbody><tr><td valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin: 0;">Delivery Address</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="40" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 40px!important"><tbody><tr><td valign="top" align="left">
                                            <p align="center" style="font-family: sans-serif; padding: 0; margin: 0;">:</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="400" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 400px!important"><tbody><tr><td valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin: 0;">' .$order->alamat. ', ' . $order->kota . ' ' .$order->kodepos. ' - ' . $order->negara . '</p>
                                        </td></tr></tbody></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" align="left">
                                        <table width="200" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 200px!important"><tbody><tr><td valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin: 0;">Phone</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="40" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 40px!important"><tbody><tr><td valign="top" align="left">
                                            <p align="center" style="font-family: sans-serif; padding: 0; margin: 0;">:</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="400" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 400px!important"><tbody><tr><td valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin: 0;">' . $order->phone . '</p>
                                        </td></tr></tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                        </td></tr>

                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
                                <tr><td valign="top" align="left" height="30"></td></tr>
                            </tbody></table>
                        </td></tr>

                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important; border-top: solid 1px #323232; border-bottom: solid 1px #323232;"><thead>
                                <tr>
                                    <th valign="top" align="left">
                                        <table width="500" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 500px!important;"><thead><tr><th valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin-top: .5em; margin-bottom: .5em;">Item</p>
                                        </th></tr></thead></table>
                                    </th>
                                    <th valign="top" align="left">
                                        <table width="140" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 140px!important;"><thead><tr><th valign="top" align="left">
                                            <p align="right" style="font-family: sans-serif; padding: 0; margin-top: .5em; margin-bottom: .5em;">Price</p>
                                        </th></tr></thead></table>
                                    </th>
                                </tr>
                            </thead></table>
                        </td></tr>

                            ' . $loopAppendProduct . '
                                
                            </tbody></table>
                        </td></tr>

                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
                                <tr><td valign="top" align="left" height="10"></td></tr>
                            </tbody></table>
                        </td></tr>

                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
                                <tr>
                                    <td valign="top" align="left">
                                        <table width="500" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 500px!important;"><tbody><tr><td valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin: 0;">Subtotal</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="40" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 40px!important;"><tbody><tr><td valign="top" align="left">
                                            <p align="center" style="font-family: sans-serif; padding: 0; margin: 0;">Rp</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="100" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100px!important;"><tbody><tr><td valign="top" align="left">
                                            <p align="right" style="font-family: sans-serif; padding: 0; margin: 0;">' . number_format($order->itemsubtotal, 0, ',', '.') .  '<span>&#42;</span></p>
                                        </td></tr></tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                        </td></tr>

                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important; border-bottom: solid 1px #878787;"><tbody>
                                <tr><td valign="top" align="left" height="10"></td></tr>
                            </tbody></table>
                        </td></tr>

                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
                                <tr><td valign="top" align="left" height="20"></td></tr>
                            </tbody></table>
                        </td></tr>

                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
                                ' . $adacoupon . ' <!-- str coupon  -->
                                <tr>
                                    <td valign="top" align="left">
                                        <table width="500" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 500px!important;"><tbody><tr><td valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin: 0;">Shipping Cost</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="40" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 40px!important;"><tbody><tr><td valign="top" align="left">
                                            <p align="center" style="font-family: sans-serif; padding: 0; margin: 0;">Rp</p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="100" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100px!important;"><tbody><tr><td valign="top" align="left">
                                            <p align="right" style="font-family: sans-serif; padding: 0; margin: 0;">' . number_format($order->shippingprice, 0, ',', '.') . '<span style="opacity: 0; visibility: hidden;">&nbsp;</span></p>
                                        </td></tr></tbody></table>
                                    </td>
                                </tr>
                              
                            
                            </tbody></table>
                        </td></tr>

                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
                                <tr><td valign="top" align="left" height="20"></td></tr>
                            </tbody></table>
                        </td></tr>

                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
                                <tr>
                                    <td valign="top" align="left">
                                        <table width="500" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 500px!important;"><tbody><tr><td valign="top" align="left">
                                            <p style="font-family: sans-serif; padding: 0; margin: 0;"><strong>Total</strong></p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="40" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 40px!important;"><tbody><tr><td valign="top" align="left">
                                            <p align="center" style="font-family: sans-serif; padding: 0; margin: 0;"><strong>Rp</strong></p>
                                        </td></tr></tbody></table>
                                    </td>
                                    <td valign="top" align="left">
                                        <table width="100" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100px!important;"><tbody><tr><td valign="top" align="left">
                                            <p align="right" style="font-family: sans-serif; padding: 0; margin: 0;"><strong>' . number_format($order->ordertotal, 0, ',', '.') . '<span>&#42;</span></strong></p>
                                        </td></tr></tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                        </td></tr>

                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
                                <tr><td valign="top" align="left" height="10"></td></tr>
                                <tr><td valign="top" align="left">
                                    ' . $yousavedisi . ' <!-- you saved isi -->
                                </td></tr>
                                <tr><td valign="top" align="left" height="30"></td></tr>
                                <tr><td valign="top" align="left">
                                    <p style="font-family: sans-serif; padding: 0; margin: 0;">
                                        If there is any mistake with the details above, please contact us at ecommerce@supresso.com. Visit online <a href="'.url('member/board').'" target="_blank" style="text-decoration: none; color: #fd4f00;">My Orders</a> to view the most up-to-date status of your order.
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="left" height="20"></td></tr>
                                <tr><td valign="top" align="left">
                                    <p style="font-family: sans-serif; padding: 0; margin: 0; font-size: 60%;">
                                        &#42; Prices are inclusive of tax
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="left" height="30"></td></tr>
                                <tr><td valign="top" align="left">
                                    <p style="font-family: sans-serif; padding: 0; margin: 0;">
                                        Best Regards,
                                        <br><strong>Supresso</strong>
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="left" height="30"></td></tr>
                            </tbody></table>
                        </td></tr>

                    </tbody></table>
                </td></tr>

            
                <tr><td valign="top" align="center">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%!important; background-color: #fafafb;"><tbody><tr><td valign="top" align="center">
                        <table width="720" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 720px!important;"><tbody><tr><td valign="top" align="center">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody><tr><td valign="top" align="center">
                                <p style="font-size: 0">
                                    <img src="https://supresso.com/newsletter/mtemplate/img/signature_light.png" width="640" style="width: 100%; height: auto; max-width: 640px">
                                </p>
                            </td></tr></tbody></table>
                        </td></tr></tbody></table>
                    </td></tr></tbody></table>
                </td></tr>

                <tr><td valign="top" align="center">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%!important;"><tbody><tr><td valign="top" align="center">
                        <table width="720" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 720px!important;"><tbody><tr><td valign="top" align="center">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
                                <tr><td valign="top" align="center" height="30"></td></tr>
                                <tr><td valign="top" align="center">
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%!important;"><tbody><tr>
                                        <td valign="middle" align="left">
                                            <table width="230" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 230px!important;"><tbody><tr>
                                                
                                                <td valign="top" align="center"><table width="66" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 66px!important;"><tbody><tr><td valign="top" align="center"></td></tr></tbody></table></td>
                                                <td valign="top" align="center">
                                                    <table width="26" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 26px!important;"><tbody><tr><td valign="top" align="center">
                                                        <p align="right" style="font-size: 0; text-align: right;">
                                                            <a href="https://web.facebook.com/supressocoffee/" target="_blank" style="text-decoration: none; margin-right: 10px;">
                                                                <img src="https://supresso.com/newsletter/mtemplate/img/ikon_fb_dark.png" width="26" style="width: 100%; height: auto; max-width: 26px">
                                                            </a>
                                                        </p>
                                                    </td></tr></tbody></table>
                                                </td>
                                                <td valign="top" align="center"><table width="10" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 10px!important;"><tbody><tr><td valign="top" align="center"></td></tr></tbody></table></td>
                                                <td valign="top" align="center">
                                                    <table width="26" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 26px!important;"><tbody><tr><td valign="top" align="center">
                                                        <p align="right" style="font-size: 0; text-align: right;">
                                                            <a href="https://www.instagram.com/supressocoffee/" target="_blank" style="text-decoration: none; margin-right: 10px;">
                                                            <img src="https://supresso.com/newsletter/mtemplate/img/ikon_ig_dark.png" width="26" style="width: 100%; height: auto; max-width: 26px">
                                                        </a>
                                                        </p>
                                                    </td></tr></tbody></table>
                                                </td>
                                                <td valign="top" align="center"><table width="10" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 10px!important;"><tbody><tr><td valign="top" align="center"></td></tr></tbody></table></td>
                                                <td valign="top" align="center">
                                                    <table width="26" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 26px!important;"><tbody><tr><td valign="top" align="center">
                                                        <p align="right" style="font-size: 0; text-align: right;">
                                                            <a href="https://api.whatsapp.com/send?phone=6281219998998&amp;text=Hi%20Supresso," target="_blank" style="text-decoration: none; margin-right: 10px;">
                                                            <img src="https://supresso.com/newsletter/mtemplate/img/ikon_phone_dark.png" width="26" style="width: 100%; height: auto; max-width: 26px">
                                                        </a>
                                                        </p>
                                                    </td></tr></tbody></table>
                                                </td>
                                                <td valign="top" align="center"><table width="10" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 10px!important;"><tbody><tr><td valign="top" align="center"></td></tr></tbody></table></td>
                                                <td valign="top" align="center">
                                                    <table width="26" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 26px!important;"><tbody><tr><td valign="top" align="center">
                                                        <p align="right" style="font-size: 0; text-align: right;">
                                                            <a href="https://g.page/supressocoffeegallery?share" target="_blank" style="text-decoration: none;">
                                                            <img src="https://supresso.com/newsletter/mtemplate/img/ikon_map_dark.png" width="26" style="width: 100%; height: auto; max-width: 26px">
                                                        </a>
                                                        </p>
                                                    </td></tr></tbody></table>
                                                </td>
                                            </tr></tbody></table>
                                        </td>
                                        <td valign="middle" align="left">
                                            <table width="27" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 27px!important;"><tbody><tr><td valign="top" align="center"></td></tr></tbody></table>
                                        </td>
                                        <td valign="middle" align="left" style="border-left: solid 1px #878787;">
                                            <table width="27" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 27px!important;"><tbody><tr><td valign="top" align="center"></td></tr></tbody></table>
                                        </td>
                                        <td valign="middle" align="left">
                                            <table width="313" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 313px!important;"><tbody>
                                                <tr><td valign="top" align="center">
                                                    <p align="left" style="font-family: sans-serif; padding: 0; margin: 0; font-size: 60%;">
                                                        <a href="'.url('/').'" target="_blank" style="text-decoration: none; color: #000000; display: inline-block; vertical-align: middle; padding-right: .5rem; margin-right: .5rem; border-right: solid 1px #565656">
                                                            View Web Version
                                                        </a>
                                                        <a href="https://www.supresso.com/id/pp.php" target="_blank" style="text-decoration: none; color: #000000; display: inline-block; vertical-align: middle; padding-right: .5rem; margin-right: .5rem; border-right: solid 1px #565656">
                                                            Privacy Policy
                                                        </a>
                                                        <a href="https://www.supresso.com/id/unsubscribed/?email=' . '" target="_blank" style="text-decoration: none; color: #000000">
                                                            Unsubscribe
                                                        </a>
                                                    </p>
                                                </td></tr>
                                                <tr><td valign="top" align="center" height="5"></td></tr>
                                                <tr><td valign="top" align="center">
                                                    <p align="left" style="font-family: sans-serif; padding: 0; margin: 0; font-size: 60%;">
                                                    Jl. Raya Mayjend. Yono Soewoyo No. 66, Pakuwon Square AK1-37, Surabaya Barat 60227 - Indonesia
                                                    </p>
                                                </td></tr>
                                                <tr><td valign="top" align="center" height="5"></td></tr>
                                                <tr><td valign="top" align="center">
                                                    <p align="left" style="font-family: sans-serif; padding: 0; margin: 0; font-size: 60%;">
                                                        Copyright &copy; 2023 Supresso. All rights reserved.
                                                    </p>
                                                </td></tr>
                                            </tbody></table>
                                        </td>
                                    </tr></tbody></table>
                                </td></tr>
                                <tr><td valign="top" align="center" height="30"></td></tr>
                            </tbody></table>
                        </td></tr></tbody></table>
                    </td></tr></tbody></table>
                </td></tr>

                <tr><td valign="top" align="center">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%!important; border-top: solid 1px #878787;"><tbody><tr><td valign="top" align="center">
                        <table width="720" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 720px!important;"><tbody><tr><td valign="top" align="center">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
                                <tr><td valign="top" align="center" height="15"></td></tr>
                                <tr><td valign="top" align="center">
                                    <p align="justify" style="font-family: sans-serif; padding: 0; margin: 0; font-size: 60%; color: #fd4f00;">
                                        Important warning and disclaimer :
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="center" height="5"></td></tr>
                                <tr><td valign="top" align="center">
                                    <p align="justify" style="font-family: sans-serif; padding: 0; margin: 0; font-size: 60%; color: #bcbec0;">
                                        This message and any attachments are intended for the named and correctly identified addressee only. This message may contain confidential, proprietary, legally privileged or commercially sensitive information. No waiver of confidentiality or privilege is intended or authorized by this transmission. If you are not the intended recipient of this message you must not directly or indirectly use, reproduce, distribute, disclose, print, reply on, disseminate, or copy any part of the message or its attachments and if you have received this message in error, please notify the sender immediately by return e-mail and delete it from your system. The accuracy of the information in this e-mail is not guaranteed. Any opinions contained in this message are those of the author and are not given or endorsed unless otherwise clearly indicated in this message, and the authority of the author to act for and on behalf of Indraco Pte. Ltd. and its Subsidiaries is duly verified.
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="center" height="15"></td></tr>
                            </tbody></table>
                        </td></tr></tbody></table>
                    </td></tr></tbody></table>
                </td></tr>
            </tbody></table>
        </body>
        </html>
        ';

        $recipient = ["email"=>$member->email, "name"=>$member->fullname];
        $this->sendEmail($body,"Your order confirmation!",$recipient, true);
    }

    public function sendResi($custData){

        $order = $custData->orderData;
        $member         = MemberModel::where('id', $order->iduser)->first();
        $expOrderNumber = explode('/', $order->nomerorder);
        $nomerorder     = $expOrderNumber[0] . "-" . $expOrderNumber[1];
        

        $body = '
        
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">

            <title>Supresso</title>
            <link rel="stylesheet icon" type="text" href="img/ikon-supresso.png">
        </head>
        <body style="padding: 0; margin: 0; background-color: #ffffff; width: 100%!important">

            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%!important"><tbody>

                <tr style="display: none; max-height: 0; overflow: hidden"><td valign="top" align="left">
                    <module name="preheader" label="Preheader"></module>
                    <editable name="preheader">To keep track of your order, use the order tracker in the link below. Please note, the order tracker may take up to 24 hours to update.</editable>
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                    &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
                    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                </td></tr>
                
                <tr><td valign="top" align="left">
                    <table width="720" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 720px!important"><tbody>

                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important"><tbody>
                                <tr><td valign="top" align="left" height="20"></td></tr>
                                <tr><td valign="top" align="left">
                                    <p style="font-size: 0;">
                                        <img src="https://supresso.com/newsletter/mtemplate/img/logo.png" width="71" style="width:100%;height:auto;max-width:71px">
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="left" height="50"></td></tr>
                                <tr><td valign="top" align="left">
                                    <h1 style="font-size: 2em; font-family: sans-serif; padding: 0; margin-top: 0;">
                                        <strong>Your order is on its way!</strong>
                                    </h1>
                                    <p style="font-family: sans-serif; padding: 0; margin-top: 0;">
                                        To keep track of your order, use the order tracker in the link below. Please note, the order tracker may take up to 24 hours to update.
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="left" height="30"></td></tr>
                            </tbody></table>
                        </td></tr>
                        
                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important; border-top: solid 1px #bcbec0;"><tbody>

                                <!-- tracking item -->
                                <tr><td valign="top" align="left">
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%!important; border-bottom: solid 1px #bcbec0;"><tbody>
                                        <tr><td valign="top" align="left" height="20"></td></tr>
                                        <tr><td valign="top" align="left">
                                            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%!important;"><tbody><tr>
                                                <td valign="center" align="left">
                                                    <table width="440" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 440px!important;"><tbody>
                                                        <tr>
                                                            <td valign="center" align="left">
                                                                <table width="110" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 110px!important;"><tbody><tr><td valign="center" align="left">
                                                                    <p style="font-family: sans-serif; padding: 0; margin-top: 0;">Courier</p>
                                                                </td></tr></tbody></table>
                                                            </td>
                                                            <td valign="center" align="left">
                                                                <table width="30" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 30px!important;"><tbody><tr><td valign="center" align="left">
                                                                    <p align="center" style="font-family: sans-serif; padding: 0; margin-top: 0;">:</p>
                                                                </td></tr></tbody></table>
                                                            </td>
                                                            <td valign="center" align="left">
                                                                <table width="300" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 300px!important;"><tbody><tr><td valign="center" align="left">
                                                                    <p style="font-family: sans-serif; padding: 0; margin-top: 0;">'.$order->pengiriman.'</p>
                                                                </td></tr></tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="center" align="left">
                                                                <table width="110" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 110px!important;"><tbody><tr><td valign="center" align="left">
                                                                    <p style="font-family: sans-serif; padding: 0; margin: 0;">Tracking No</p>
                                                                </td></tr></tbody></table>
                                                            </td>
                                                            <td valign="center" align="left">
                                                                <table width="30" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 30px!important;"><tbody><tr><td valign="center" align="left">
                                                                    <p align="center" style="font-family: sans-serif; padding: 0; margin: 0;">:</p>
                                                                </td></tr></tbody></table>
                                                            </td>
                                                            <td valign="center" align="left">
                                                                <table width="300" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 300px!important;"><tbody><tr><td valign="center" align="left">
                                                                    <p style="font-family: sans-serif; padding: 0; margin: 0;">'.$order->tracking_number.'</p>
                                                                </td></tr></tbody></table>
                                                            </td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                                <td valign="center" align="left">
                                                    <table width="200" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 200px!important;"><tbody><tr><td valign="center" align="left">
                                                     </table>
                                                </td>
                                            </tr></tbody></table>
                                        </td></tr>
                                        <tr><td valign="top" align="left" height="20"></td></tr>
                                    </tbody></table>
                                </td></tr>
                                <!-- end tracking item -->

                            </tbody></table>
                        </td></tr>

                        <tr><td valign="top" align="left">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important"><tbody>
                                <tr><td valign="top" align="left" height="30"></td></tr>
                                <tr><td valign="top" align="left">
                                    <p style="font-family: sans-serif; padding: 0; margin-top: 0;">
                                        You also can visit online <a href="'.url('member/board').'" target="_blank" style="text-decoration: none; color: #fd4f00;">My Orders</a> to view the most up-to-date status of your order. 
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="left" height="30"></td></tr>
                                <tr><td valign="top" align="left">
                                    <p style="font-family: sans-serif; padding: 0; margin: 0;">
                                        Best Regards,
                                        <br><strong>Supresso</strong>
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="left" height="30"></td></tr>
                            </tbody></table>
                        </td></tr>

                    </tbody></table>
                </td></tr>

            <tr><td valign="top" align="center">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%!important; background-color: #fafafb;"><tbody><tr><td valign="top" align="center">
                        <table width="720" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 720px!important;"><tbody><tr><td valign="top" align="center">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody><tr><td valign="top" align="center">
                                <p style="font-size: 0">
                                    <img src="https://supresso.com/newsletter/mtemplate/img/signature_light.png" width="640" style="width: 100%; height: auto; max-width: 640px">
                                </p>
                            </td></tr></tbody></table>
                        </td></tr></tbody></table>
                    </td></tr></tbody></table>
                </td></tr>

                <tr><td valign="top" align="center">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%!important;"><tbody><tr><td valign="top" align="center">
                        <table width="720" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 720px!important;"><tbody><tr><td valign="top" align="center">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
                                <tr><td valign="top" align="center" height="30"></td></tr>
                                <tr><td valign="top" align="center">
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%!important;"><tbody><tr>
                                        <td valign="middle" align="left">
                                            <table width="230" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 230px!important;"><tbody><tr>
                                                
                                                <td valign="top" align="center"><table width="66" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 66px!important;"><tbody><tr><td valign="top" align="center"></td></tr></tbody></table></td>
                                                <td valign="top" align="center">
                                                    <table width="26" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 26px!important;"><tbody><tr><td valign="top" align="center">
                                                        <p align="right" style="font-size: 0; text-align: right;">
                                                            <a href="https://web.facebook.com/supressocoffee/" target="_blank" style="text-decoration: none; margin-right: 10px;">
                                                                <img src="https://supresso.com/newsletter/mtemplate/img/ikon_fb_dark.png" width="26" style="width: 100%; height: auto; max-width: 26px">
                                                            </a>
                                                        </p>
                                                    </td></tr></tbody></table>
                                                </td>
                                                <td valign="top" align="center"><table width="10" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 10px!important;"><tbody><tr><td valign="top" align="center"></td></tr></tbody></table></td>
                                                <td valign="top" align="center">
                                                    <table width="26" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 26px!important;"><tbody><tr><td valign="top" align="center">
                                                        <p align="right" style="font-size: 0; text-align: right;">
                                                            <a href="https://www.instagram.com/supressocoffee/" target="_blank" style="text-decoration: none; margin-right: 10px;">
                                                            <img src="https://supresso.com/newsletter/mtemplate/img/ikon_ig_dark.png" width="26" style="width: 100%; height: auto; max-width: 26px">
                                                        </a>
                                                        </p>
                                                    </td></tr></tbody></table>
                                                </td>
                                                <td valign="top" align="center"><table width="10" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 10px!important;"><tbody><tr><td valign="top" align="center"></td></tr></tbody></table></td>
                                                <td valign="top" align="center">
                                                    <table width="26" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 26px!important;"><tbody><tr><td valign="top" align="center">
                                                        <p align="right" style="font-size: 0; text-align: right;">
                                                            <a href="https://api.whatsapp.com/send?phone=6281219998998&amp;text=Hi%20Supresso," target="_blank" style="text-decoration: none; margin-right: 10px;">
                                                            <img src="https://supresso.com/newsletter/mtemplate/img/ikon_phone_dark.png" width="26" style="width: 100%; height: auto; max-width: 26px">
                                                        </a>
                                                        </p>
                                                    </td></tr></tbody></table>
                                                </td>
                                                <td valign="top" align="center"><table width="10" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 10px!important;"><tbody><tr><td valign="top" align="center"></td></tr></tbody></table></td>
                                                <td valign="top" align="center">
                                                    <table width="26" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 26px!important;"><tbody><tr><td valign="top" align="center">
                                                        <p align="right" style="font-size: 0; text-align: right;">
                                                            <a href="https://g.page/supressocoffeegallery?share" target="_blank" style="text-decoration: none;">
                                                            <img src="https://supresso.com/newsletter/mtemplate/img/ikon_map_dark.png" width="26" style="width: 100%; height: auto; max-width: 26px">
                                                        </a>
                                                        </p>
                                                    </td></tr></tbody></table>
                                                </td>


                                            </tr></tbody></table>
                                        </td>
                                        <td valign="middle" align="left">
                                            <table width="27" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 27px!important;"><tbody><tr><td valign="top" align="center"></td></tr></tbody></table>
                                        </td>
                                        <td valign="middle" align="left" style="border-left: solid 1px #878787;">
                                            <table width="27" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 27px!important;"><tbody><tr><td valign="top" align="center"></td></tr></tbody></table>
                                        </td>
                                        <td valign="middle" align="left">
                                            <table width="313" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 313px!important;"><tbody>
                                                <tr><td valign="top" align="center">
                                                    <p align="left" style="font-family: sans-serif; padding: 0; margin: 0; font-size: 60%;">
                                                        <a href="'.url('/').'" target="_blank" style="text-decoration: none; color: #000000; display: inline-block; vertical-align: middle; padding-right: .5rem; margin-right: .5rem; border-right: solid 1px #565656">
                                                            View Web Version
                                                        </a>
                                                        <a href="'.url('member/board').'" target="_blank" style="text-decoration: none; color: #000000; display: inline-block; vertical-align: middle; padding-right: .5rem; margin-right: .5rem; border-right: solid 1px #565656">
                                                            Privacy Policy
                                                        </a>
                                                        <a href="'.url('member/board').'" target="_blank" style="text-decoration: none; color: #000000">
                                                            Unsubscribe
                                                        </a>
                                                    </p>
                                                </td></tr>
                                                <tr><td valign="top" align="center" height="5"></td></tr>
                                                <tr><td valign="top" align="center">
                                                    <p align="left" style="font-family: sans-serif; padding: 0; margin: 0; font-size: 60%;">
                                                        Jl. Raya Mayjend. Yono Soewoyo No. 66, Pakuwon Square AK1-37, Surabaya Barat 60227 - Indonesia
                                                    </p>
                                                </td></tr>
                                                <tr><td valign="top" align="center" height="5"></td></tr>
                                                <tr><td valign="top" align="center">
                                                    <p align="left" style="font-family: sans-serif; padding: 0; margin: 0; font-size: 60%;">
                                                        Copyright &copy; 2023 Supresso. All rights reserved.
                                                    </p>
                                                </td></tr>
                                            </tbody></table>
                                        </td>
                                    </tr></tbody></table>
                                </td></tr>
                                <tr><td valign="top" align="center" height="30"></td></tr>
                            </tbody></table>
                        </td></tr></tbody></table>
                    </td></tr></tbody></table>
                </td></tr>

                <tr><td valign="top" align="center">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%!important; border-top: solid 1px #878787;"><tbody><tr><td valign="top" align="center">
                        <table width="720" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 720px!important;"><tbody><tr><td valign="top" align="center">
                            <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 640px!important;"><tbody>
                                <tr><td valign="top" align="center" height="15"></td></tr>
                                <tr><td valign="top" align="center">
                                    <p align="justify" style="font-family: sans-serif; padding: 0; margin: 0; font-size: 60%; color: #fd4f00;">
                                        Important warning and disclaimer :
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="center" height="5"></td></tr>
                                <tr><td valign="top" align="center">
                                    <p align="justify" style="font-family: sans-serif; padding: 0; margin: 0; font-size: 60%; color: #bcbec0;">
                                        This message and any attachments are intended for the named and correctly identified addressee only. This message may contain confidential, proprietary, legally privileged or commercially sensitive information. No waiver of confidentiality or privilege is intended or authorized by this transmission. If you are not the intended recipient of this message you must not directly or indirectly use, reproduce, distribute, disclose, print, reply on, disseminate, or copy any part of the message or its attachments and if you have received this message in error, please notify the sender immediately by return e-mail and delete it from your system. The accuracy of the information in this e-mail is not guaranteed. Any opinions contained in this message are those of the author and are not given or endorsed unless otherwise clearly indicated in this message, and the authority of the author to act for and on behalf of Indraco Pte. Ltd. and its Subsidiaries is duly verified.
                                    </p>
                                </td></tr>
                                <tr><td valign="top" align="center" height="15"></td></tr>
                            </tbody></table>
                        </td></tr></tbody></table>
                    </td></tr></tbody></table>
                </td></tr>

            </tbody></table>

        </body>
        </html>
        
        ';

        // $recipient = ["email"=>"sigit.develop@gmail.com", "name"=>"Sigit"];
        $recipient = ["email"=>$member->email, "name"=>$member->fullname];
        $this->sendEmail($body, "Your order is on its way!", $recipient, true);
    }

    private function sendEmail($emailBody = "", $emailSubject = "", $recipient = [], $withBcc = false){
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        
        //Server settings                   
        $mail->isSMTP();                                            
        
        // Send using SMTP
        $mail->Host     = 'mail.supresso.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'no-reply@supresso.com';
        $mail->Password = '6LTbd8.98Wfh]dV';
        $mail->SMTPSecure = 'ssl';
        $mail->Port     = 465;
        $mail->CharSet 	= "UTF-8";

        //Recipients
        $mail->setFrom('ecommerce@supresso.com', 'Supresso');
        $mail->addAddress($recipient['email'], $recipient['name']);
        //$mail->addAddress('indis.mp.adm@gmail.com', $recipient['name']);
        $mail->isHTML(true);                          
        
        if ($withBcc){
            $mail->addBcc('sigit.develop@gmail.com', 'dev');
            $mail->addBcc('indracodev@gmail.com', 'dev');
            // $mail->addBcc('dm@indraco.com', 'dm');
        }
        
        // Set email format to HTML
        $mail->Subject = $emailSubject;
        $mail->Body    = $emailBody;
        $mail->send();
    }
}
