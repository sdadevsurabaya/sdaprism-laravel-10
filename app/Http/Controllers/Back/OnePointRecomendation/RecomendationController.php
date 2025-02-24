<?php

namespace App\Http\Controllers\Back\OnePointRecomendation;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ItemRecommendation;
use App\Models\Member;
use App\Models\Product;

class RecomendationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("back.Onepoint_recomendation.index");
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
          
          return view("back.Onepoint_recomendation.create", compact('member', 'produk'));
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
            'recom_start_date' => 'required|date',
            'recom_end_date' => 'required|date|after:recom_start_date',
            'status' => 'required|in:active,inactive',
        ]);
    
    
        $recommendation = new ItemRecommendation();
        
        // Assign values from the validated data to the model
        $recommendation->id_member = $validatedData['id_member'] ?? null;
        $recommendation->id_produk = $validatedData['id_produk']; // corrected variable name
        $recommendation->recom_start_date = $validatedData['recom_start_date'];
        $recommendation->recom_end_date = $validatedData['recom_end_date'];
        $recommendation->status = $validatedData['status'];

        $recommendation->save();
    
        return redirect()->route('onepoint_recomendation.index')->with('success', 'Recommendation created successfully.');
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
         // Retrieve the recommendation based on the given $id
    $recommendation = ItemRecommendation::findOrFail($id);

    // Retrieve data for dropdowns
    $members = Member::pluck('email', 'id');
    $products = Product::selectRaw("concat(namaproduk, ' - ', kemasan, ' - ', harga) as full_name, id")->pluck('full_name', 'id');

    // Return the view with the recommendation and dropdown data
    return view('back.Onepoint_recomendation.edit', compact('recommendation', 'members', 'products'));
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
        // Validate the request
        $validatedData = $request->validate([
            'id_member' => 'nullable',
            'id_produk' => 'required',
            'recom_start_date' => 'required|date',
            'recom_end_date' => 'required|date|after:recom_start_date',
            'status' => 'required|in:active,inactive',
        ]);
    
        // Retrieve the recommendation based on the given $id
        $recommendation = ItemRecommendation::findOrFail($id);
    
        // Update the recommendation with the validated data
        $recommendation->id_member = $validatedData['id_member'] ?? null;
        $recommendation->id_produk = $validatedData['id_produk'];
        $recommendation->recom_start_date = $validatedData['recom_start_date'];
        $recommendation->recom_end_date = $validatedData['recom_end_date'];
        $recommendation->status = $validatedData['status'];
    
        // Save the updated recommendation
        $recommendation->save();
    
        return redirect()->route('onepoint_recomendation.index')->with('success', 'Recommendation updated successfully.');
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
