<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRole;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        #Get Data Auth user
        $user = Auth::user();
        #tampung semua data user pada variable
        if ($user->hasRole('Member')) {        
            $iduser = auth()->user()->id;
            $email = auth()->user()->email;
            $nameuser = auth()->user()->name;
            $phone = auth()->user()->phone;
            $joindate = auth()->user()->created_at;
            $lastupdated = auth()->user()->updated_at;

            #Get date hari ini
            $day = date('d');
            $month_int = date('m');
            $month_string = date('F');
            $year = date('Y');

            $news = [];
        
            $response =  DB::select('select * from news_category as c');
    
            if (!empty($idcategory)) {
                $response =  DB::select('select * from news_category as c where id='.$idcategory);
            }
    
          
    
    
            for ($m = 0; $m < count($response); $m++) {
               
                $res = $response[$m];
                // $responsevouchers =  DB::select('select * from onepoint_voucher as m where m.id_merchant = '.$res->id);
                $resnews = DB::table('news')
                ->join('news_category', 'news_category.id', '=', 'news.category_id')           
                ->where('news_category.id','=', $res->id)  
                ->where('status','=', 'published')                         
                ->select('news.id',
                'news.title',
                'news.short_desc',
                'news.text',
                'news.type',
                'news.image',
                'news.file',
                'news.video',
                'news.quote_text',
                'news.quote_author',
                'news.author',
                'news.slug',
                'news.status',
                'news.images_code',
                'news.order',
                'news.category_id',
                )
                ->get();
                
                $res->news = $resnews;
                array_push($news, $res);
            }

          
            
            $title = 'home';
            $pages = 'home';
            return view('front.members.news',compact('user','title', 'pages','iduser', 'email','nameuser','phone','joindate','lastupdated','day','month_int','month_string','year','news'
        ));

        } else if ($user->hasRole('Admin')) {
            if (view()->exists($request->path())) {
                return view($request->path());
            }
        }



        return abort(404);
    }

    
}