<?php

namespace App\Http\Controllers\Front;


use App\Models\File;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FrontNewsController extends Controller
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
      
        $res_news = DB::select("SELECT * from news order by id desc");

        $title = "News & Reviews";
		$pages = "news";
        return view('front/news',compact('title','pages','res_news'));
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $res_news = DB::select("SELECT * from news order by id desc");
        $res_news_detail = DB::select("SELECT * from news where id = ".$id);
        $news_detail  = $res_news_detail[0];
        $title = "News & Reviews";
        $pages = 'detail';
        return view('front/detail-news',compact('title','pages','news_detail', 'res_news'));
    }

    
}