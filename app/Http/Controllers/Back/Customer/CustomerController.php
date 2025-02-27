<?php

namespace App\Http\Controllers\Back\Customer;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::all();
        return view('back.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // For API, you might not need this method
        // If you are using views, you can return a view here
        return view('back.customers.create');
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
            'business_name' => 'required|string|unique:customers,business_name',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'postcode' => 'nullable|string',
            'country' => 'nullable|string',
            'telephone' => 'nullable|string',
            'fax' => 'nullable|string',
            'pic' => 'nullable|string',
            'mobile' => 'nullable|string',
            'email' => 'nullable|email',
            'terms' => 'nullable|string',
            'business_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('customers.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        Customer::create($request->all());

        return redirect()->route('customers.index')
                        ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return view('back.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('back.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|unique:customers,business_name,' . $customer->id,
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'postcode' => 'nullable|string',
            'country' => 'nullable|string',
            'telephone' => 'nullable|string',
            'fax' => 'nullable|string',
            'pic' => 'nullable|string',
            'mobile' => 'nullable|string',
            'email' => 'nullable|email',
            'terms' => 'nullable|string',
            'business_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('customers.edit', $customer->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $customer->update($request->all());

        return redirect()->route('customers.index')
                        ->with('success', 'Pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
                        ->with('success', 'Pelanggan berhasil dihapus.');
    }
}
