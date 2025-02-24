<?php

namespace App\Http\Controllers\Product;

use App\Models\Product_sub_category_model;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Validator;

class SubCategoryController extends Controller
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
        // dd("sub category");

        // $subCategorys =  DB::select("SELECT * from pos_sub_category");

        $subCategorys = DB::table('pos_sub_category')
                        ->leftJoin('pos_category', 'pos_category.id', '=', 'pos_sub_category.category_id')
                        ->select('*','pos_sub_category.id as id_sub_category', 'pos_sub_category.name as name_sub_category', 'pos_category.name as name_category')
                        ->get();

        $getCategorys = DB::table('pos_category')->get();

        return view('sub-category/index', compact('subCategorys', 'getCategorys'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
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
        $validator = Validator::make($request->all(), [
            'category_id'   => 'required|not_in:0',
            'name'   => 'required',
        ]);

        if ($validator->passes()) {
            if ($request->hasfile('filenames')) {
                foreach ($request->file('filenames') as $file) {
                    $name = time() . rand(1, 100) . '-' . $file->getClientOriginalName();
                    // $file->move(public_path('files'), $name);
                    Image::make($file)->resize(850, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(public_path('images/subcategory/' . $name));

                    $files[] = $name;
                    // $namaFiles[] = $request->namaFile;
                }
            } else {
                $files[] = null;
            }

            $CreateSubCategory = Product_sub_category_model::create([
                'category_id'   => $request->category_id,
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
        $SubCategory = Product_sub_category_model::find($id);

        return response()->json([
            'success' => true,
            'data'    => $SubCategory
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
            'category_id_update'  => 'required|not_in:0',
            'name_update'         => 'required',
        ]);

        if ($validator->passes()) {
            if ($request->hasfile('filenames')) {
                foreach ($request->file('filenames') as $file) {
                    $name = time() . rand(1, 100) . '-' . $file->getClientOriginalName();

                    Image::make($file)->resize(850, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(public_path('images/subcategory/' . $name));

                    $files[] = $name;
                }

                $data = Product_sub_category_model::find($request->id_subcategory);

                if ($data->fileimages) {
                    $images = explode("," ,$data->fileimages);

                    foreach ($images as $image) {
                        unlink("images/subcategory/".$image);
                    }
                }

            } else {
                $files[] = null;
                $data = Product_sub_category_model::find($request->id_subcategory);
            }

            $data->category_id = $request->category_id_update;
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
        $getImagesSubCategorys = Product_sub_category_model::find($id);

        if ($getImagesSubCategorys->fileimages) {
            $images = explode("," ,$getImagesSubCategorys->fileimages);

            foreach ($images as $image) {
                unlink("images/subcategory/".$image);
            }
        }

        Product_sub_category_model::find($id)->delete();

        return response()->json(['success'=>'1']);
    }
}
