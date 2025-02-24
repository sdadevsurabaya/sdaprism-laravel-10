<?php

namespace App\Http\Controllers\Front;


use App\Models\File;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FrontUniquecodeController extends Controller
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

        // $userkey = '084e76601977';
        // $passkey = '90483253a12d14ef3196c72a';
        // $telepon = '081333322610';
        // $message = 'pak yogi nakal';
        // $url = 'https://console.zenziva.net/wareguler/api/sendWA/';
        // $curlHandle = curl_init();
        // curl_setopt($curlHandle, CURLOPT_URL, $url);
        // curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        // curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        // curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
        // curl_setopt($curlHandle, CURLOPT_POST, 1);
        // curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
        //     'userkey' => $userkey,
        //     'passkey' => $passkey,
        //     'to' => $telepon,
        //     'message' => $message
        // ));
        // $results = json_decode(curl_exec($curlHandle), true);
        // curl_close($curlHandle);

//         $key='db63f52c1a00d33cf143524083dd3ffd025d672e255cc688'; //this is demo key please change with your own key
// $url='http://116.203.191.58/api/send_message';
// $data = array(
//   "phone_no"  => '+6281217173406',
//   "key"       => $key,
//   "message"   => 'DEMO AKUN WOOWA. tes woowa api v3.0 mohon di abaikan',
//   "skip_link" => True // This optional for skip snapshot of link in message
// );
// $data_string = json_encode($data);

// $ch = curl_init($url);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
// curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_VERBOSE, 0);
// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
// curl_setopt($ch, CURLOPT_TIMEOUT, 360);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//   'Content-Type: application/json',
//   'Content-Length: ' . strlen($data_string))
// );
// echo $res=curl_exec($ch);
// curl_close($ch);

$userkey = '084e76601977';
$passkey = '90483253a12d14ef3196c72a';
$telepon = '081217173406';
$message = 'Hi John Doe, have a nice day.';
$url = 'https://console.zenziva.net/reguler/api/sendsms/';
$curlHandle = curl_init();
curl_setopt($curlHandle, CURLOPT_URL, $url);
curl_setopt($curlHandle, CURLOPT_HEADER, 0);
curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
curl_setopt($curlHandle, CURLOPT_POST, 1);
curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
    'userkey' => $userkey,
    'passkey' => $passkey,
    'to' => $telepon,
    'message' => $message
));
$results = json_decode(curl_exec($curlHandle), true);
curl_close($curlHandle);

      
        $user = Auth::user();
        #tampung semua data user pada variable
        if ($user->hasRole('Member')) {        
            $iduser = auth()->user()->id;
            $email = auth()->user()->email;
            $nameuser = auth()->user()->name;
            $phone = auth()->user()->phone;
            $joindate = auth()->user()->created_at;
            $lastupdated = auth()->user()->updated_at;
        }
            #Get date hari ini
            $day = date('d');
            $month_int = date('m');
            $month_string = date('F');
            $year = date('Y');

        $res_news = DB::select("SELECT * from news order by id desc");

        $title = 'home';
        $pages = 'home';
        return view('front.members.uniquecode',compact('user','title', 'pages','iduser', 'email','nameuser','phone','joindate','lastupdated','day','month_int','month_string','year'));
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $res_news = DB::select("SELECT * from news order by id desc");
        // $res_news_detail = DB::select("SELECT * from news where id = ".$id);
        // $news_detail  = $res_news_detail[0];
        // $title = "News & Reviews";
        // $pages = 'detail';
        // return view('front/detail-news',compact('title','pages','news_detail', 'res_news'));
    }

    
}