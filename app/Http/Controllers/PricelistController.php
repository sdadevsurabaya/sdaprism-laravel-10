<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\HeaderLogo;
use App\Models\PriceList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PricelistController extends Controller
{
    public function index()
    {
        $data = PriceList::all();
        return view('data.view_pricelist', compact('data'));
    }

    public function create()
    {
        $logo = HeaderLogo::all();
        $currency = Currency::all();
        return view('forms.pricelist', compact('logo', 'currency'));
    }

    public function template_pdf()
    {
        return view('template.pricelist-template');
    }

    public function store(Request $request)
    {
        $rules = [
            'header_logo_id' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'currency_id' => 'nullable|string|max:255',
            'show_payment_method' => 'required',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'datatable_data' => 'nullable|json',
        ];

        if ($request->has('date')) {
            $date = strtotime($request->date);
            $formattedDate = date('Y-m-d', $date);
            $request['date'] = $formattedDate;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = PriceList::create([
                'user_id' => Auth::id(),
                'header_logo_id' => $request->header_logo_id,
                'title' => $request->title,
                'footer_text' => $request->footer_text,
                'date' => $request->date,
                'currency_id' => $request->currency_id,
                'show_payment_method' => $request->show_payment_method,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'datatable_data' => $request->datatable_data,
            ]);

            return redirect()->route('pricelists.edit', $data)
                ->with('success', 'Data successfully created');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create data: ' . $e->getMessage());
        }
    }

    public function show(PriceList $pricelist)
    {
        // return view('data.view_pricelist_single', compact('pricelist'));
    }

    public function edit(PriceList $pricelist)
    {
        $logo = HeaderLogo::all();
        $currency = Currency::all();
        return view('forms.pricelist', compact('logo', 'currency', 'pricelist'));
    }

    public function update(Request $request, PriceList $pricelist)
    {
        $rules = [
            'header_logo_id' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'currency_id' => 'nullable|string|max:255',
            'show_payment_method' => 'required',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'datatable_data' => 'nullable|json',
        ];

        if ($request->has('date')) {
            $date = strtotime($request->date);
            $formattedDate = date('Y-m-d', $date);
            $request['date'] = $formattedDate;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $pricelist->update([
                'header_logo_id' => $request->header_logo_id,
                'title' => $request->title,
                'footer_text' => $request->footer_text,
                'date' => $request->date,
                'currency_id' => $request->currency_id,
                'show_payment_method' => $request->show_payment_method,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'datatable_data' => $request->datatable_data,
            ]);

            return redirect()->route('pricelists.edit', $pricelist)
                ->with('success', 'Data successfully updated');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update data: ' . $e->getMessage());
        }
    }

    public function destroy(PriceList $pricelist)
    {
        try {
            $pricelist->delete();
            return redirect()->route('pricelists.index')
                ->with('success', 'Data successfully deleted');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete data: ' . $e->getMessage());
        }
    }
}
