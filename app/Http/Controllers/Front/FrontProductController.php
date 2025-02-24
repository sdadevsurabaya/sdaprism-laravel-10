<?php

namespace App\Http\Controllers\Front;


use App\Models\File;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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
    public function menu(Request $request)
    {
        $res_allproduct = DB::select('select p.*, b.name as brand, c.name as category, sc.name as subcategory from pos_products as p
        LEFT JOIN pos_brand as b on b.id = p.id_brand
        LEFT JOIN pos_category as c on c.id = p.id_category
        LEFT JOIN pos_sub_category as sc on sc.id = p.id_sub_category where p.deleted="false"');

        $category = DB::select('select * from pos_category');
        $subcategory = DB::select('select * from pos_sub_category');
        $product = DB::select('select p.*, b.name as brand, c.name as category, sc.name as subcategory from pos_products as p
        LEFT JOIN pos_brand as b on b.id = p.id_brand
        LEFT JOIN pos_category as c on c.id = p.id_category
        LEFT JOIN pos_sub_category as sc on sc.id = p.id_sub_category where p.deleted="false"');
        
        
        $allproduct = [];
        for ($p=0; $p < count($res_allproduct); $p++) {
            $res_alladditional = DB::select("select * from pos_additional_products as ap INNER JOIN pos_additional as a on ap.id_additional = a.id where ap.id_product=".$res_allproduct[$p]->id." and ap.deleted='false'");
            $res_alloptional = DB::select("select * from pos_optional_products as op INNER JOIN pos_optional as a on op.id_optional = a.id where op.id_product=".$res_allproduct[$p]->id." and op.deleted='false'");
            
            $res_allvariant = [];
            if ($res_allproduct[$p]->variant<>"") {
                $res_allvariant = DB::select("select * from pos_products where deleted='false' and variant=".$res_allproduct[$p]->variant);
            }
          
            $res_allproduct[$p]->additional = $res_alladditional;
            $res_allproduct[$p]->optional = $res_alloptional;
            
            $res_allproduct[$p]->variant = $res_allvariant;
            array_push($allproduct, $res_allproduct[$p]);
        }


        $title = "Menu";
		$pages = "landing";

        //dd($category);
        return view('waiters/landing',compact('title','pages','category','subcategory','product','res_allproduct', 'allproduct'));
    }

    public function submenu(Request $request)
    {

        $res_allproduct = DB::select('select p.*, b.name as brand, c.name as category, sc.name as subcategory from pos_products as p
        LEFT JOIN pos_brand as b on b.id = p.id_brand
        LEFT JOIN pos_category as c on c.id = p.id_category
        LEFT JOIN pos_sub_category as sc on sc.id = p.id_sub_category where p.deleted="false"');
        
        $menu = $_GET["menu"];
        $category = DB::select('select * from pos_category where id = '.$menu);
        $subcategory = DB::select('select * from pos_sub_category where category_id = '.$menu . ' and deleted = "false"');
        if ((isset($_GET["menu"]))) {
            if(isset($_GET["id_sub_category"]))
            {
                $menusubcategory = DB::select('select * from pos_sub_category where deleted="false" and category_id = '.$menu);
                $subcategory = DB::select('select * from pos_sub_category where category_id = '.$menu.' and id='.$_GET["id_sub_category"]. ' and deleted = "false"');
                $res_product = DB::select('select p.*, b.name as brand, c.name as category, sc.name as subcategory from pos_products as p
                LEFT JOIN pos_brand as b on b.id = p.id_brand
                LEFT JOIN pos_category as c on c.id = p.id_category
                LEFT JOIN pos_sub_category as sc on sc.id = p.id_sub_category where p.deleted="false" and p.id_category = '.$menu.' and p.id_sub_category='.$_GET["id_sub_category"]);
            }
            else
            {
                $menusubcategory = DB::select('select * from pos_sub_category where deleted="false" and category_id = '.$menu);
                $subcategory = DB::select('select * from pos_sub_category where category_id = '.$menu. ' and deleted = "false"');
                $res_product = DB::select('select p.*, b.name as brand, c.name as category, sc.name as subcategory from pos_products as p
                LEFT JOIN pos_brand as b on b.id = p.id_brand
                LEFT JOIN pos_category as c on c.id = p.id_category
                LEFT JOIN pos_sub_category as sc on sc.id = p.id_sub_category where p.deleted="false" and p.id_category = '.$menu);

            }
           
        } else {

            if(isset($_GET["id_sub_category"]))
            {
                //$id_sub_category = $_GET["id_sub_category"];
                $subcategory = DB::select('select * from pos_sub_category where id='.$_GET["id_sub_category"]);
                $res_product = DB::select('select p.*, b.name as brand, c.name as category, sc.name as subcategory from pos_products as p
                LEFT JOIN pos_brand as b on b.id = p.id_brand
                LEFT JOIN pos_category as c on c.id = p.id_category
                LEFT JOIN pos_sub_category as sc on sc.id = p.id_sub_category where p.deleted="false"');
            }
            else
            {
                //$id_sub_category = "";
                $subcategory = DB::select('select * from pos_sub_category');
                $res_product = DB::select('select p.*, b.name as brand, c.name as category, sc.name as subcategory from pos_products as p
                LEFT JOIN pos_brand as b on b.id = p.id_brand
                LEFT JOIN pos_category as c on c.id = p.id_category
                LEFT JOIN pos_sub_category as sc on sc.id = p.id_sub_category where p.deleted="false"');
            }


            // $subcategory = DB::select('select * from pos_sub_category');
           
        }

        $product = [];
        for ($p=0; $p < count($res_product); $p++) {
            $res_additional = DB::select("select * from pos_additional_products as ap INNER JOIN pos_additional as a on ap.id_additional = a.id where ap.id_product=".$res_product[$p]->id." and ap.deleted='false'");
            $res_optional = DB::select("select * from pos_optional_products as op INNER JOIN pos_optional as a on op.id_optional = a.id where op.id_product=".$res_product[$p]->id." and op.deleted='false'");
            
            $res_variant = [];
            if ($res_product[$p]->variant<>"") {
                $res_variant = DB::select("select * from pos_products where deleted='false' and variant=".$res_product[$p]->variant);
            }
          
            $res_product[$p]->additional = $res_additional;
            $res_product[$p]->optional = $res_optional;
            
            $res_product[$p]->variant = $res_variant;
            array_push($product, $res_product[$p]);
        }

        $allproduct = [];
        for ($p=0; $p < count($res_allproduct); $p++) {
            $res_alladditional = DB::select("select * from pos_additional_products as ap INNER JOIN pos_additional as a on ap.id_additional = a.id where ap.id_product=".$res_allproduct[$p]->id." and ap.deleted='false'");
            $res_alloptional = DB::select("select * from pos_optional_products as op INNER JOIN pos_optional as a on op.id_optional = a.id where op.id_product=".$res_allproduct[$p]->id." and op.deleted='false'");
            
            $res_allvariant = [];
            if ($res_allproduct[$p]->variant<>"") {
                $res_allvariant = DB::select("select * from pos_products where deleted='false' and variant=".$res_allproduct[$p]->variant);
            }
          
            $res_allproduct[$p]->additional = $res_alladditional;
            $res_allproduct[$p]->optional = $res_alloptional;
            
            $res_allproduct[$p]->variant = $res_allvariant;
            array_push($allproduct, $res_allproduct[$p]);
        }

        //dd($res_allproduct);
 
        $title = "Submenu";
		$pages = "Submenu";

        return view('waiters/submenu',compact('title','pages','category','subcategory', 'product', 'menu', 'menusubcategory', 'res_allproduct', 'allproduct'));
    }

    
}