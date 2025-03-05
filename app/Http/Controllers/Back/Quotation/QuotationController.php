<?php

namespace App\Http\Controllers\Back\Quotation;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quotations = Quotation::all();
        return view('back.quotations.index', compact('quotations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('back.quotations.create');
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
            'no' => 'required|string|unique:quotation,no',
            'address_letter' => 'nullable|string',
            'customer_id' => 'nullable|string',
            'phone' => 'nullable|string',
            'payment_type' => 'required|in:CASH,BANK TRANSFER,PAY NOW',
            'currency' => 'required|in:IDR,USD,SGD',

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        // dd($request->all());
        Quotation::create($request->all());

        return redirect()->route('quotations.index')->with('success', 'Quotation created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function show(Quotation $quotation)
    {
        return view('back.quotations.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function edit(Quotation $quotation)
    {
        return view('back.quotations.edit', compact('quotation'));
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $quotation->update($request->all());

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation updated successfully.');
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

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully.');
    }

    public function printPDF($id)
    {
        // $quotation = Quotation::with(['detailQuotationProducts.customer', 'detailQuotationProducts.product'])->find($id);
        $quotation = Quotation::with(['customer','QuotationProduct'])->find($id);
        // $quotation = Quotation::with(['customer', 'quotationProducts'])->find($id);

        dd($quotation->customers);
        // Ambil data dari database
        // $pdf = Pdf::loadView('back.quotations.pdf', compact('quotations'));
        // return $pdf->download('quotation-list.pdf');
        return view('back.quotations.pdf', compact('quotation'));
    }
}
