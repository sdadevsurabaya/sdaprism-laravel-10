<?php

namespace App\Http\Controllers\Back\Onepoint_brand;

use App\Models\Brand;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("back.Onepoint_brand.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $merchants = Merchant::pluck('merchant_name', 'id');
        return view("back.Onepoint_brand.create", compact('merchants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'status' => 'required',
            'id_merchant' => 'required',
            'brand' => 'required|string|max:255',

        ]);

        // Simpan data baru ke dalam database menggunakan metode create
        Brand::updateOrCreate(
            [
                'status' => $request->input('status'),
                'id_merchant' => $request->input('id_merchant'),
                'brand' => $request->input('brand'),
            ]
        );


        return redirect()->route('onepoint_brand.index')->with('success', 'Brand berhasil disimpan');
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

        $brand = Brand::find($id);


        if (!$brand) {
            return redirect()->route('onepoint_brand.index')->with('error', 'Brand tidak ditemukan');
        }
        $merchants = Merchant::pluck('merchant_name', 'id');

        return view('back.Onepoint_brand.edit', compact('brand', 'merchants'));
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
        // Validasi input
        $request->validate([
            'id_merchant' => 'required',
            'brand' => 'required|string|max:255',
            'status' => 'required|in:Aktif,Inactive',
            // Tambahkan aturan validasi lainnya sesuai kebutuhan Anda
        ]);

        // Temukan data brand berdasarkan ID
        $brand = Brand::find($id);

        // Jika brand tidak ditemukan, mungkin hendak ditangani secara khusus, misalnya, dengan redirect atau pesan error
        if (!$brand) {
            return redirect()->route('onepoint_brand.index')->with('error', 'Brand tidak ditemukan');
        }

        // Perbarui data brand
        $brand->update([
            'id_merchant' => $request->input('id_merchant'),
            'brand' => $request->input('brand'),
            'status' => $request->input('status'),
            // Tambahkan atribut lainnya sesuai kebutuhan Anda
        ]);

        // Redirect atau tampilkan pesan sukses sesuai kebijakan Anda
        return redirect()->route('onepoint_brand.index')->with('success', 'Brand berhasil diperbarui');
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
