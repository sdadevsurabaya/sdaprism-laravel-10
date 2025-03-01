<?php

namespace App\Http\Controllers\Api\Quotation;

use App\Http\Controllers\Controller;
use App\Models\DetailQuotationProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DetailQuotationProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $detailQuotationProducts = DetailQuotationProduct::all();
        return response()->json($detailQuotationProducts);
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
            'quotation_id' => 'required|exists:quotations,id',
            'product_id' => 'required|exists:products,id',
            'unit' => 'required|in:PC/PCS,Meter',
            'qty' => 'required|integer|min:1',
            'price' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $detailQuotationProduct = DetailQuotationProduct::create($request->all());
        return response()->json($detailQuotationProduct, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DetailQuotationProduct  $detailQuotationProduct
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $detailQuotationProduct = DetailQuotationProduct::find($id);

        if (!$detailQuotationProduct) {
            return response()->json(['message' => 'Detail Quotation Product not found'], 404);
        }

        return response()->json($detailQuotationProduct);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DetailQuotationProduct  $detailQuotationProduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $detailQuotationProduct = DetailQuotationProduct::find($id);

        if (!$detailQuotationProduct) {
            return response()->json(['message' => 'Detail Quotation Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'quotation_id' => 'sometimes|required|exists:quotations,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'unit' => 'sometimes|required|in:PC/PCS,Meter',
            'qty' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $detailQuotationProduct->update($request->all());
        return response()->json($detailQuotationProduct);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DetailQuotationProduct  $detailQuotationProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detailQuotationProduct = DetailQuotationProduct::find($id);

        if (!$detailQuotationProduct) {
            return response()->json(['message' => 'Detail Quotation Product not found'], 404);
        }

        $detailQuotationProduct->delete();
        return response()->json(['message' => 'Detail Quotation Product deleted successfully']);
    }
}
