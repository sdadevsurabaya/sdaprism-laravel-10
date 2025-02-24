<?php

namespace App\Http\Controllers\Back\OnePopularSearch;

use App\Http\Controllers\Controller;
use App\Models\PopularSearch;
use Illuminate\Http\Request;

class PopularSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("back.Onepoint_popularsearch.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("back.Onepoint_popularsearch.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //add validation
        $validatedData = $request->validate([
            'query' => 'required',
            'search_count' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        PopularSearch::create($request->all());
        return redirect()->route("onepoint_popularsearch.index")
            ->with("success", "Onepoint_popularsearch created successfully");

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

       $popularSearch = PopularSearch::find($id);

        return view("back.Onepoint_popularsearch.edit", compact("popularSearch"));

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
        //add validation 
        $validatedData = $request->validate([
            'query' => 'required',
            'search_count' => 'required',
            'status' => 'required|in:active,inactive',
        ]);
        
        PopularSearch::find($id)->update($request->all());

   
        return redirect()->route("onepoint_popularsearch.index")
            ->with("success", "Onepoint_popularsearch updated successfully");

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
