<?php

namespace App\Http\Controllers\Api\Quotation;

use App\Http\Controllers\Controller;
use App\Models\DetailQuotationProduct;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllQuotations()
    {
        $quotations = Quotation::with('QuotationProduct')->get();
        return response()->json($quotations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postQuotation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no' => 'required|string|unique:quotation',
            'address_letter' => 'nullable|string',
            'customer_id' => 'nullable|string',
            'phone' => 'nullable|string',
            'payment_type' => 'required|in:CASH,BANK TRANSFER,PAY NOW',
            'currency' => 'required|in:IDR,USD,SGD',
            'date' => 'nullable|date',
            'valid_until' => 'nullable|date',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'descriptions' => 'nullable|string',
            'remarks' => 'nullable|string',
            'account_options' => 'nullable|string',
            'made_by' => 'nullable|string',
            'sub_total' => 'nullable|numeric',
            'additional_discount' => 'nullable|numeric',
            'additional_cost' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'deposit' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $quotation = Quotation::create($request->all());
        return response()->json($quotation, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function getQuotationById(Quotation $quotation)
    {
        return response()->json($quotation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quotation $quotation)
    {
        $validator = Validator::make($request->all(), [
            'no' => 'required|string|unique:quotation,no,' . $quotation->id,
            'address_letter' => 'nullable|string',
            'customer_id' => 'nullable|string',
            'phone' => 'nullable|string',
            'payment_type' => 'required|in:CASH,BANK TRANSFER,PAY NOW',
            'currency' => 'required|in:IDR,USD,SGD',
            'date' => 'nullable|date',
            'valid_until' => 'nullable|date',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'descriptions' => 'nullable|string',
            'remarks' => 'nullable|string',
            'account_options' => 'nullable|string',
            'made_by' => 'nullable|string',
            'sub_total' => 'nullable|numeric',
            'additional_discount' => 'nullable|numeric',
            'additional_cost' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'deposit' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $quotation->update($request->all());
        return response()->json($quotation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return response()->json(['message' => 'Quotation deleted successfully']);
    }
}
