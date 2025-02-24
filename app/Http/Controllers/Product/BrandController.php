<?php

namespace App\Http\Controllers\Product;

use App\Models\Product_brand_model;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Validator;

class BrandController extends Controller
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
        // dd("brand");

        $brands =  DB::select("SELECT * from pos_brand");

        return view('brand/index', compact('brands'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required',
            'description' => 'required',
        ]);

        if ($validator->passes()) {
            $Brand = Product_brand_model::create([
                'name'   => $request->name,
                'description'    => $request->description,
            ]);

            return response()->json(['success'=>'1']);
        }

        return response()->json(['error'=>$validator->errors()->all()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Brand = Product_brand_model::find($id);

        return response()->json([
            'success' => true,
            'data'    => $Brand
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $validator = Validator::make($request->all(), [
            'name_update'   => 'required',
            'description_update' => 'required',
        ]);

        if ($validator->passes()) {
            $data = Product_brand_model::find($request->id_brand);
            $data->name = $request->name_update;
            $data->description = $request->description_update;
            $data->save();

            return response()->json(['success'=>'1']);
        }

        return response()->json(['error'=>$validator->errors()->all()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product_brand_model::find($id)->delete();
        return response()->json(['success'=>'1']);
    }
}
