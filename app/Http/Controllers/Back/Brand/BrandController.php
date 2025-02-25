<?php

namespace App\Http\Controllers\Back\Brand;

use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ImageUploadTraits;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;



class BrandController extends Controller
{
    use ImageUploadTraits;
    protected $pathImage;

    public function __construct()
    {
        $this->pathImage = 'images/brand/';
    }


    public function index()
    {
        $brands = Brand::all();
        $pathimg = $this->pathImage;

        return view('back.brand.index', compact('brands', 'pathimg'));
    }

    public function create()
    {
        return view('back.brand.create');
    }

    public function store(Request $request)
    {


        $validate = $request->validate([
            'name' => 'required',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'slug' => 'required',
            'status' => 'required',
            'deleted' => 'required',

        ]);

        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request, 'image', $validate['name'], $this->pathImage);
            $validate['image'] = $imagePath;
        }

        if ($request->has('slug')) {
            $validate['slug'] = Str::slug(strtolower($request->input('slug')));
        }

        Brand::create($validate);
        return redirect()->route('brands.index');
    }

    public function show($id)
    {
        $brand = Brand::find($id);
        $pathimg = $this->pathImage;
        return view('back.brand.show', compact('brand','pathimg'));
    }

    public function edit($id)
    {
        $brand = Brand::find($id);
        $pathimg = $this->pathImage;
        return view('back.brand.edit', compact('brand','pathimg'));
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);
        $validate = $request->validate([
            'name' => 'required',
            'slug' => 'required',
            'image' => 'nullable',
            'status' => 'required',
            'deleted' => 'required',

        ]);

        if ($request->hasFile('image')) {
            $imagePath = $this->updateImage($request, 'image', $validate['name'], $this->pathImage, $brand->image);
            $validate['image'] = $imagePath;
        }

        if ($request->has('slug')) {
            $validate['slug'] = Str::slug(strtolower($request->input('slug')));
        }

        $brand->update($validate);
        return redirect()->route('brands.index');
    }

    public function destroy($id)
    {
        $brand = Brand::find($id);
        $path = $this->pathImage . $brand->image;
        $this->deleteImage($path);
        $brand->delete();
        return redirect()->route('brands.index');
    }
}
