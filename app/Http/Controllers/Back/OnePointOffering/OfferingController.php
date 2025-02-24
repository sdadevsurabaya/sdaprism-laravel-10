<?php

namespace App\Http\Controllers\Back\OnePointOffering;

use App\Models\Member;
use App\Models\Product;
use App\Models\ItemOffering;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OfferingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("back.Onepoint_offering.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $member = Member::pluck('email', 'id');
        $produk = Product::selectRaw("concat(namaproduk, ' - ', kemasan, ' - ', harga) as full_name, id")->pluck('full_name', 'id');
          
          return view("back.Onepoint_offering.create", compact('member', 'produk'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'id_member' => 'nullable',
            'id_produk' => 'required',
            'recom_start_date' => 'required',
            'recom_end_date' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        $offering = new ItemOffering();

        // Assign values from the validated data to the model
        $offering->id_member = $validatedData['id_member'] ?? null;
        $offering->id_produk = $validatedData['id_produk'];
        $offering->recom_start_date = $validatedData['recom_start_date'];
        $offering->recom_end_date = $validatedData['recom_end_date'];
        $offering->status = $validatedData['status'];

        $offering->save();

        return redirect()->route('onepoint_offering.index')->with('success', 'Offering created successfully.');
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
        // Retrieve the offering based on the given $id
    $offering = ItemOffering::findOrFail($id);

    // Retrieve data for dropdowns
    $members = Member::pluck('email', 'id');
    $products = Product::selectRaw("concat(namaproduk, ' - ', kemasan, ' - ', harga) as full_name, id")->pluck('full_name', 'id');

    // Return the view with the offering and dropdown data
    return view('back.Onepoint_offering.edit', compact('offering', 'members', 'products'));
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
        
        $validatedData = $request->validate([
            'id_member' => 'nullable',
            'id_produk' => 'required',
            'recom_start_date' => 'required|date',
            'recom_end_date' => 'required|date|after:recom_start_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Retrieve the offering based on the given $id
        $offering = ItemOffering::findOrFail($id);

        // Update the offering with the validated data
        $offering->id_member = $validatedData['id_member'] ?? null;
        $offering->id_produk = $validatedData['id_produk'];
        $offering->recom_start_date = $validatedData['recom_start_date'];
        $offering->recom_end_date = $validatedData['recom_end_date'];
        $offering->status = $validatedData['status'];

        // Save the updated offering
        $offering->save();

        return redirect()->route('onepoint_offering.index')->with('success', 'Offering updated successfully.');

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
