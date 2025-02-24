<?php

namespace App\Http\Controllers\Back\OnepointProduct;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCreateRequest;
use App\Models\Merchant;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('back.Onepoint_product.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brand = Brand::pluck('brand', 'id');
        $merchant = Merchant::pluck('merchant_name', 'id');
        return view('back.Onepoint_product.create', compact('brand', 'merchant'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductCreateRequest $request)
    {
        $data = $request->validated();
        $product = new Product($data);

        $fileName = null;
        $folderName = 'OTHERS';

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');


            $fileName = time() . '_' . $file->getClientOriginalName();


            $idBrand = $request->id_brand;

            $brandFolders = [
                1 => 'UANGEMAS',
                2 => 'UCAFE',
                5 => 'RASASAYANG',
                6 => 'BROCHOCO',
                8 => 'HAOCAFE',
                9 => 'TUGUBUAYA',
                10 => 'JAHEKU',
                12 => 'MESIN',
            ];
            $folderName = $brandFolders[$idBrand] ?? 'OTHERS';


            $file->move(public_path('files/' . $folderName), $fileName);
        }
        $product->gambar = $fileName ? $folderName . '/' . $fileName : null;

        $product->save();

        return redirect()->route('onepoint_product.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = Brand::pluck('brand', 'id');
        $product = Product::findOrFail($id);
        $merchant = Merchant::pluck('merchant_name', 'id');
        return view('back.Onepoint_product.edit', compact('brand', 'product', 'merchant'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductCreateRequest $request, $id)
    {
        $data = $request->validated();
        $product = Product::findOrFail($id);
        $product->fill($data);
        // Store the current image file path
        $productOld = Product::findOrFail($id);
        $oldImagePath = public_path('files/' . $productOld->gambar);
        // dd($oldImagePath);
        $fileName = $product->gambar;
        $folderName = 'OTHERS';

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');

            $fileName = time() . '_' . $file->getClientOriginalName();

            $idBrand = $request->id_brand;

            $brandFolders = [
                1 => 'UANGEMAS',
                2 => 'UCAFE',
                5 => 'RASASAYANG',
                6 => 'BROCHOCO',
                8 => 'HAOCAFE',
                9 => 'TUGUBUAYA',
                10 => 'JAHEKU',
                12 => 'MESIN',
            ];
            $folderName = $brandFolders[$idBrand] ?? 'OTHERS';

            // Delete the old image file
            // Delete the old image file
            if (!empty($product->gambar) && file_exists($oldImagePath)) {

                unlink($oldImagePath);
            }

            $file->move(public_path('files/' . $folderName), $fileName);
            $product->gambar = $fileName ? $folderName . '/' . $fileName : null;
        }


        $product->save();

        return redirect()->route('onepoint_product.index')
            ->with('success', 'Product updated successfully');
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
