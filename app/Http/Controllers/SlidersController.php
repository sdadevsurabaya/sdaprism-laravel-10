<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SlidersModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class SlidersController extends Controller
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

    public function index(){
        $data = DB::table('sliders')
                ->orderBy('created_at', 'desc')
                ->orderBy('flag', 'desc')
                ->get();

        return view('sliders.index', ['sliders' => $data]);
    }

    public function create(){
        return view('sliders.create');
    }

    public function store(Request $request){

        $r = $request;
        $isAvailImageDekstop    = $r->hasfile('dekstop_image');
        $isAvailImageMobile     = $r->hasfile('mobile_image');

        $obj = new stdClass;

        $namaDekstop = "";
        if ($isAvailImageDekstop) {
            $file = $r->dekstop_image;
            $name = $file->getClientOriginalName();
            $file->move(public_path('sliders-image'), $name);
            $namaDekstop = $name;
        }
        
        $namaMobile = "";
        if ($isAvailImageMobile) {
            $file = $r->mobile_image;
            $name = $file->getClientOriginalName();
            $file->move(public_path('sliders-image'), $name);
            $namaMobile = $name;
        }

        $userLogin = Auth::user()->name;

        $obj->desktop_img    = $namaDekstop;
        $obj->mobile_img     = $namaMobile;
        $obj->custom_script  = $r->custom_script == null ? "" : $r->custom_script;
        $obj->shop_url       = $r->url == null ? "#" : $r->url;
        $obj->title          = $r->title;
        $obj->below_title    = $r->below_title == null ? "" : $r->below_title;
        $obj->desc           = $r->desc == null ? "" : $r->desc;
        $obj->type           = $r->type;
        $obj->flag           = "Y";
        $obj->created_who    = $userLogin;
        $obj->updated_who    = $userLogin;
        $obj->active_date    = $r->active_date;
        $obj->text_color     = $r->text_color;
        $obj->json_text_form = $r->json_text_form;

        $arrObj = (array) $obj;

        try {
            SlidersModel::create($arrObj);
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with([
                        'error' => 'Error , ' . $th->getMessage()
                    ]);
        }

        return redirect()
                ->route('sliders.index')
                ->with([
                    'success' => 'New post has been created successfully'
                ]);
    }

    public function update(Request $request){
        $r = $request;
        $isAvailImageDekstop    = $r->hasfile('dekstop_image');
        $isAvailImageMobile     = $r->hasfile('mobile_image');

        $obj = SlidersModel::find($r->id);

        $namaDekstop = "";
        if ($isAvailImageDekstop) {
            $file = $r->dekstop_image;
            $name = $file->getClientOriginalName();
            $file->move(public_path('sliders-image'), $name);
            $namaDekstop = $name;
            $obj->desktop_img = $namaDekstop;

        }
        
        $namaMobile = "";
        if ($isAvailImageMobile) {
            $file = $r->mobile_image;
            $name = $file->getClientOriginalName();
            $file->move(public_path('sliders-image'), $name);
            $namaMobile = $name;
            $obj->mobile_img = $namaMobile;
        }
        
        $userLogin = Auth::user()->name;

        $obj->custom_script  = $r->custom_script == null ? "" : $r->custom_script;
        $obj->shop_url       = $r->url == null ? "#" : $r->url;
        $obj->title          = $r->title;
        $obj->below_title    = $r->below_title == null ? "" : $r->below_title;
        $obj->desc           = $r->desc == null ? "" : $r->desc;
        $obj->type           = $r->type;
        $obj->flag           = $r->display;
        $obj->updated_who    = $userLogin;
        $obj->active_date    = $r->active_date;
        $obj->text_color     = $r->text_color;
        $obj->json_text_form = $r->json_text_form;

        try {
            $obj->save();
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with([
                        'error' => 'Error , ' . $th->getMessage()
                    ]);
        }

        return redirect()
                ->route('sliders.index')
                ->with([
                    'success' => 'New post has been updated successfully'
                ]);
    }

    public function load(){
        return "load slider";
    }

    public function edit(Request $req){
        $slidersModels = new SlidersModel();
        return view('sliders.edit', [
            "slider" => $slidersModels->find($req->id)
        ]);
    }
}
