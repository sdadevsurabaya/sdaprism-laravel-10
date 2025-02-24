<?php

namespace App\Http\Controllers;

use App\Models\Product_model;
use App\Models\Product_kind_model;
use App\Models\Product_category_model;
use App\Models\Product_collection_model;
use App\Models\Product_variant_model;




use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use DB;

class SyncProductController extends Controller
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

    function searchForId($id, $array)
    {

        foreach ($array as $key => $val) {
            $stat = 0;
            if ($val->id === $id) {
                $stat = 1;
                return $stat;
            }
        }
        return null;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $product_models = Product_model::latest()->get();
        $product = DB::connection('mysql')->select("select * from product_models ORDER BY id");
        $product_old = DB::connection('mysql_old')->select("select * from masterproduk ORDER BY idproduk");

        //$sku = array_search('10397', array_column($product_old, 'namaproduk'));
        $diffdata = 0;
        // $statussync = false;

        $statussync = $this->searchForId(2, $product);
        $resultdiff = [];
        $resultproduct_old = [];
        foreach ($product_old as $value) {
            $p = $value;
            $statussync = $this->searchForId($value->idproduk, $product);
            if ($statussync == 1) {
                //dump("test");
                $p->sync = false;
            } else {
                array_push($resultdiff, $value->idproduk);
                $p->sync = true;
                //dump($value->idproduk);
                $diffdata++;
            }

            array_push($resultproduct_old, $p);
        }
        //dd($resultproduct_old);
        //dd($resultdiff);

        return view('sync-products/index', compact('product', 'resultproduct_old', 'diffdata'))
            ->with('i', ($request->input('page', 1) - 1) * 5)
            ->with('a', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd("ayam");
        // $this->validate($request, [
        //     'product' => 'required'
        // ]);

        //$id_category = Product_category_model::find($request->categoryname);
        $id_category = DB::connection('mysql')->select("select id from product_category_models where product_category_name like '%" . $request->categoryname . "%'");
        // dump($id_category[0]->id);
        $idcategory = $id_category[0]->id;
        $id_kind = DB::connection('mysql')->select("select id from product_kind_models where product_kind_name like '%" . $request->kind . "%'");
        // dump($id_kind[0]->id);
        $idkind = $id_kind[0]->id;
        $id_collection = DB::connection('mysql')->select("select id from product_collection_models where product_collection_name like '%" . $request->collection . "%'");
        $idcollection = $id_collection[0]->id;
        //dd($idcollection);
        if ($request->sku == null) {
            $skuvalue = '';
        } else {
            $skuvalue = $request->sku;
        }

        $product_models = Product_model::create([

            'sku' => $skuvalue,
            'product_brand'    => '',
            'product_name' => $request->namaproduk,
            'product_variant' => $request->kind,
            'product_shortdesc' => $request->shortdescription,
            'product_desc' => '',
            'product_weight' => $request->beratasli,
            'product_length' => $request->panjang,
            'product_width' => $request->lebar,
            'product_height' => $request->tinggi,
            'product_category' => $idcategory,
            'product_collection' => $idcollection,
            'product_kind' => $id_kind[0]->id,
            'status_stock' => $request->statusstock,
            'status' => $request->status,
        ]);

        if ($product_models) {
            return redirect()
                ->route('sync-product.index')
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
        $product_models =  DB::table('product_models')
            ->where('product_models.id', '=', $id)
            ->leftjoin(
                'product_kind_models',
                'product_models.product_kind',
                '=',
                'product_kind_models.id'
            )
            ->leftjoin(
                'product_variant_models',
                'product_models.product_variant',
                '=',
                'product_variant_models.id'
            )
            ->leftjoin(
                'product_category_models',
                'product_models.product_category',
                '=',
                'product_category_models.id'
            )
            ->leftjoin(
                'product_collection_models',
                'product_models.product_collection',
                '=',
                'product_collection_models.id'
            )
            ->select(
                'product_models.*',
                'product_kind_models.product_kind_name',
                'product_variant_models.product_variant_name',
                'product_category_models.product_category_name',
                'product_collection_models.product_collection_name'
            )

            ->get();
        //dd($product_models);
        // return view('posts.index', compact('posts'));
        return view('products/show', compact('product_models'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product_models = Product_model::findOrFail($id);
        return view('products.edit', compact('product_models'));
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
        $this->validate($request, [
            'product' => 'required'
        ]);

        $product_models = Product_model::findOrFail($id);

        $product_models->update([
            'id_category_product' => $request->id_category_product,
            'product' => $request->product,
            'description' => $request->description,
        ]);

        if ($product_models) {
            return redirect()
                ->route('product.index')
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
    }
}