<?php

namespace App\Http\Controllers\Front;


use App\Models\Product_model;
use App\Models\Product_collection_model;
use App\Models\Product_type_model;
use App\Models\Product_form_model;
use App\Models\Product_package_model;
use App\Models\File;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class FrontProductController extends Controller
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
        // DB::enableQueryLog();
        $product_collection = [];
        $product_collection_models_res =  DB::table('product_collection_models')
            ->where('deleted', 'false')
            ->orderBy('id', 'desc')
            ->get();
        //dump($product_collection_models_res);
        $tes = "MONTH(discount_cluster_models.active_date)";
        for ($c = 0; $c < count($product_collection_models_res); $c++) 
        {
            $cp = $product_collection_models_res[$c];
            $product_mdls[$c] = [];

            $product_collection_models_res[$c] =  DB::select('	select pm.*,
            (select discount_models.discount from discount_models 
            LEFT JOIN discount_cluster_models as dcm on discount_models.discount_cluster_id = dcm.id
            where  (NOW() >= dcm.active_date AND NOW() <= dcm.off_date) and discount_models.product_id = pm.id) as disc_event,
            (select dcm.nama from discount_models 
            LEFT JOIN discount_cluster_models as dcm on discount_models.discount_cluster_id = dcm.id
            where  (NOW() >= dcm.active_date AND NOW() <= dcm.off_date) and discount_models.product_id = pm.id) as event_promo	
            from product_models as pm
            where product_collection =' . $cp->id);

            for ($pr = 0; $pr < count($product_collection_models_res[$c]); $pr++) {
                $prdct[$pr] = $product_collection_models_res[$c][$pr];
                if (!empty($prdct[$pr]->fileimages)) {
                    $prdct[$pr]->images = json_decode($product_collection_models_res[$c][$pr]->fileimages);
                } else {
                    $prdct[$pr]->images = null;
                }

                if (!empty($prdct[$pr]->disc_event)) {
                    $prdct[$pr]->product_price_after_disc =  ($prdct[$pr]->product_price) - (($prdct[$pr]->product_price) * ($prdct[$pr]->disc_event) / 100);
                } else {
                    $prdct[$pr]->product_price_after_disc =  $prdct[$pr]->product_price;
                }

                array_push($product_mdls[$c], $prdct[$pr]);
            }
            
            $cp->product = $product_mdls[$c];
            array_push($product_collection, $cp);
        }

        //dd($product_collection);
        // $product_models = Product_model::latest()->get();
        $product_models = [];
        // $product_models_res =  DB::table('product_models')->where('deleted', 'false')
        $product_models_res =  DB::select('select pm.*,pcm.product_collection_name, ptm.product_type_name,
                pfm.product_form_name,
                ppm.product_package_name,
            (select discount_models.discount from discount_models 
            LEFT JOIN discount_cluster_models as dcm on discount_models.discount_cluster_id = dcm.id
            where  (NOW() >= dcm.active_date AND NOW() <= dcm.off_date) and discount_models.product_id = pm.id and discount_models.status ="active"
						 ) 
						as disc_event,
						(select dcm.nama from discount_models 
            LEFT JOIN discount_cluster_models as dcm on discount_models.discount_cluster_id = dcm.id
            where  (NOW() >= dcm.active_date AND NOW() <= dcm.off_date) and discount_models.product_id = pm.id and discount_models.status ="active"
						 ) 
						as event_promo
            from product_models as pm
             LEFT JOIN product_collection_models as pcm on pcm.id = pm.product_collection
            LEFT JOIN product_type_models as ptm on ptm.id = pm.product_type
            LEFT JOIN product_form_models as pfm on pfm.id = pm.product_form
            LEFT JOIN product_package_models as ppm on ppm.id = pm.product_package
            where pm.status = "active"
            ');

        for ($p = 0; $p < count($product_models_res); $p++) {
            $prdct = $product_models_res[$p];
            if (!empty($prdct->fileimages)) {
                $prdct->images = json_decode($product_models_res[$p]->fileimages);
            } else {
                $prdct->images = null;
            }
            if (!empty($prdct->disc_event)) {
                $prdct->product_price_after_disc =   ($prdct->product_price) - (($prdct->product_price) * ($prdct->disc_event) / 100);
            } else {
                $prdct->product_price_after_disc =  $prdct->product_price;
            }
            array_push($product_models, $prdct);
        }
        
        $product_models_modal = $product_models;

        $product_forms = DB::table("product_form_models")
        ->where("deleted", 'false')
        ->orderBy('id', 'asc')
        ->get();

        foreach ($product_forms as $form) {
            $form->visible  = "Y";
            $form->selected = "Y"; 
        }

        foreach ($product_collection as $collection){
            $arrForms = DB::select("SELECT product_form FROM product_models pm 
                                    WHERE product_collection = '".$collection->id."' 
                                    AND product_form is not null
                                    GROUP BY product_form
                                    ORDER BY product_form ASC");
            $collection->forms = $arrForms;
        }


        $title = 'Collection';
        $pages = 'product';

        return view('front/coffee', compact('product_models', 'product_models_modal', 'product_collection', 'product_forms', 'title', 'pages'))
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

        //dd("front show detail product");
        $product_models = [];
        $product_models_array = [];

        $product_models_res =  DB::select('select pm.*, pcm.product_collection_name, ptm.product_type_name,
                pfm.product_form_name,
                ppm.product_package_name,
            (select discount_models.discount from discount_models 
            LEFT JOIN discount_cluster_models as dcm on discount_models.discount_cluster_id = dcm.id
            where  (NOW() >= dcm.active_date AND NOW() <= dcm.off_date) and discount_models.product_id = pm.id and discount_models.status ="active"
						 ) 
						as disc_event,
						(select dcm.nama from discount_models 
            LEFT JOIN discount_cluster_models as dcm on discount_models.discount_cluster_id = dcm.id
            where  (NOW() >= dcm.active_date AND NOW() <= dcm.off_date) and discount_models.product_id = pm.id  and discount_models.status ="active"
						 ) 
						as event_promo
            from product_models as pm
            LEFT JOIN product_collection_models as pcm on pcm.id = pm.product_collection
            LEFT JOIN product_type_models as ptm on ptm.id = pm.product_type
            LEFT JOIN product_form_models as pfm on pfm.id = pm.product_form
            LEFT JOIN product_package_models as ppm on ppm.id = pm.product_package
            where pm.id = 
            ' . $id);

        for ($p = 0; $p < count($product_models_res); $p++) {
            $prdct = $product_models_res[$p];
            $aciditydesc = explode("/", $product_models_res[$p]->product_aciditydesc);
            $bodydesc = explode("/", $product_models_res[$p]->product_bodydesc);
            $prdct->acidity_desc = $aciditydesc[0];
            $prdct->body_desc = $bodydesc[0];

            if (!empty($prdct->fileimages)) {
                $prdct->images = json_decode($product_models_res[$p]->fileimages);
            } else {
                $prdct->images = null;
            }

            if (!empty($prdct->disc_event)) {
                $prdct->product_price_after_disc =   ($prdct->product_price) - (($prdct->product_price) * ($prdct->disc_event) / 100);
            } else {
                $prdct->product_price_after_disc =  $prdct->product_price;
            }

            $prdct->variant = null;
            array_push($product_models_array, $prdct);
        }

        $product_form = [];
        $product_weight = [];
        $product_variant_res = [];

        if ($product_models_array[0]->product_variant != null && $product_models_array[0]->product_variant !== "")
        {
            $product_form = app('App\Http\Controllers\Api\Product\GetFormController')->GetFormsGroupByVariant($product_models_array[0]->product_variant);
            $product_weight = app('App\Http\Controllers\Api\Product\GetWeightController')->GetWeightByForm($product_models_array[0]->product_collection, $product_models_array[0]->product_variant, $product_models_array[0]->product_form); 
            
            //dd($product_form);
            //dd($product_weight);
            //get variant coffee models
            $product_variant_res =  DB::select('select pvm.product_variant_name,pm.*, pcm.product_collection_name, ptm.product_type_name,
                    pfm.product_form_name,
                    ppm.product_package_name,
                (select discount_models.discount from discount_models 
                LEFT JOIN discount_cluster_models as dcm on discount_models.discount_cluster_id = dcm.id
                where  (NOW() >= dcm.active_date AND NOW() <= dcm.off_date) and discount_models.product_id = pm.id
                            ) 
                            as disc_event,
                            (select dcm.nama from discount_models 
                LEFT JOIN discount_cluster_models as dcm on discount_models.discount_cluster_id = dcm.id
                where  (NOW() >= dcm.active_date AND NOW() <= dcm.off_date) and discount_models.product_id = pm.id
                            ) 
                            as event_promo
                from product_models as pm
                LEFT JOIN product_collection_models as pcm on pcm.id = pm.product_collection
                LEFT JOIN product_type_models as ptm on ptm.id = pm.product_type
                LEFT JOIN product_form_models as pfm on pfm.id = pm.product_form
                LEFT JOIN product_package_models as ppm on ppm.id = pm.product_package
                            LEFT JOIN product_variant_models as pvm on pvm.id = pm.product_variant
                            where pm.product_variant = 
                ' . $product_models_array[0]->product_variant);
        }
      
        $product_models_array[0]->variant = $product_variant_res;
        //dd($product_models_array[0]);

        $product_models = $product_models_array[0];
        $product_models_modal = $product_models_array;

        //dd($product_models);
        //dd($product_models);

        $title = 'test';
        $pages = 'detail';
        return view('front/detail-coffee', compact('product_models', 'product_models_modal', 'product_form', 'product_weight', 'title', 'pages'));
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
                'status_stock' => $request->status_stock,
                'status' => $request->status
            ]);
        }
        
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

    /**
     * load product data pada halaman coffe
     */
    public function loadProductData(Request $req){
        
        $isHasCollectionFilter = $req->collection != null && $req->collection != "all";
        $isHasFormFilter       = $req->form != null && $req->form != "all";
        $isHasSortingFilter    = $req->sorting != null && $req->sorting != "all";

        $extendedSql = "WHERE deleted = 'false' ";
        if ($isHasCollectionFilter)
            $extendedSql .= "AND product_collection = '".$req->collection."' ";
        if ($isHasFormFilter)
            $extendedSql .= "AND product_form = '".$req->form."' ";
        if ($isHasSortingFilter)
            $extendedSql .= $this->getSortingSqlProduct($req->sorting);

        $products = DB::select("SELECT * FROM product_models ".$extendedSql."");
        $discounts = $this->getActiveDiscount();

        foreach ($products as $item) {

            $item->product_price_after_disc = $item->product_price;

            $id = $item->id;
            $filteredDiscount = Arr::where($discounts, function ($value, $key) use ($id){
                return $value->product_id == $id;
            }); 
            
            if (count($filteredDiscount) > 0){
                foreach ($discounts as $discount){
                    $item->st_discount = $discount->discount;
                    $priceAfterDisc = ($item->product_price) - (($item->product_price) * ($discount->discount) / 100);
                    $item->product_price_after_disc = round($priceAfterDisc, 2, PHP_ROUND_HALF_UP);
                }
            }
        }

        return $products;
    }

    private function getSortingSqlProduct($id) {

        // tambahkan 1 array disini jika menambahkan kategori sorting & sql nya
        $arrSorting = [
            ["id" => "latest", "sql" => "ORDER BY id DESC"],
            ["id" => "price_high_low", "sql" => "ORDER BY product_price DESC"],
            ["id" => "price_low_high", "sql" => "ORDER BY product_price DESC"]
        ];

        $arrSortingSelected = Arr::where($arrSorting, function($value, $key) use ($id) {
            return $value['id'] == $id;
        });

        if (count($arrSortingSelected) > 0){
            foreach ($arrSortingSelected as $sort) {
                return $sort['sql'];
            }
        }

        return "";
    }

    private function getActiveDiscount(){
        $discounts = DB::select("SELECT 
            dm.product_id,
            dm.discount 
            FROM discount_models dm 
            INNER JOIN (
                SELECT 
                id 
                FROM discount_cluster_models dcm 
                WHERE date(now()) >= active_date AND date(now()) <= active_date
                AND status = 'active'
            ) dcm ON dm.discount_cluster_id = dcm.id 
            WHERE dm.status = 'active'");
        return $discounts;
    }
}