<?php

namespace App\Http\Controllers\Product;

use App\Models\Product_category_model;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Validator;

class CategoryController extends Controller
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
        // dd("category");
        // $product_models = Product_model::latest()->get();

        $pos_categorys =  DB::select("SELECT * from pos_category");

        return view('category/index', compact('pos_categorys'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // dd("cetare category");
        return view('category/create');
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
        ]);

        if ($validator->passes()) {
            if ($request->hasfile('filenames')) {
                foreach ($request->file('filenames') as $file) {
                    $name = time() . rand(1, 100) . '-' . $file->getClientOriginalName();
                    // $file->move(public_path('files'), $name);
                    Image::make($file)->resize(850, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(public_path('images/category/' . $name));

                    $files[] = $name;
                    // $namaFiles[] = $request->namaFile;
                }
            } else {
                $files[] = null;
            }

            $CreateCategory = Product_category_model::create([
                'name'          => $request->name,
                'fileimages'    => implode(",",$files),
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
        $Category = Product_category_model::find($id);

        return response()->json([
            'success' => true,
            'data'    => $Category
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
        ]);

        if ($validator->passes()) {
            if ($request->hasfile('filenames')) {
                foreach ($request->file('filenames') as $file) {
                    $name = time() . rand(1, 100) . '-' . $file->getClientOriginalName();

                    Image::make($file)->resize(850, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(public_path('images/category/' . $name));

                    $files[] = $name;
                }

                $data = Product_category_model::find($request->id_category);

                if ($data->fileimages) {
                    $images = explode("," ,$data->fileimages);

                    foreach ($images as $image) {
                        unlink("images/category/".$image);
                    }
                }

            } else {
                $files[] = null;
                $data = Product_category_model::find($request->id_category);
            }

            $data->name = $request->name_update;
            $data->fileimages = implode(",",$files);
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

        $getImagesCategorys = Product_category_model::find($id);

        if ($getImagesCategorys->fileimages) {
            $images = explode("," ,$getImagesCategorys->fileimages);

            foreach ($images as $image) {
                unlink("images/category/".$image);
            }
        }

        Product_category_model::find($id)->delete();

        return response()->json(['success'=>'1']);
    }
}
