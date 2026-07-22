<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Currency;
use App\Models\HeaderLogo;
use App\Models\PriceList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PricelistController extends Controller
{
    public function index()
    {
        $data = PriceList::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        // if (Auth::user()->rolesUsers->first()?->roles->name === 'admin') {
            $data = PriceList::all();
        // }
        return view('data.view_pricelist', compact('data'));
    }

    public function create()
    {

        $logo     = HeaderLogo::all();
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
            'header_logo_id'      => 'nullable|string|max:255',
            'title'               => 'nullable|string|max:255',
            'footer_text'         => 'nullable|string|max:255',
            'date'                => 'nullable|date',
            'currency_id'         => 'nullable|string|max:255',
            'show_payment_method' => 'required',
            'payment_method'      => 'nullable|string|max:255',
            'notes'               => 'nullable|string',
            'datatable_data'      => 'nullable|json',
        ];

        if ($request->has('date')) {
            $date            = strtotime($request->date);
            $formattedDate   = date('Y-m-d', $date);
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
                'user_id'             => Auth::id(),
                'header_logo_id'      => $request->header_logo_id,
                'title'               => $request->title,
                'footer_text'         => $request->footer_text,
                'date'                => $request->date,
                'currency_id'         => $request->currency_id,
                'show_payment_method' => $request->show_payment_method,
                'payment_method'      => $request->payment_method,
                'notes'               => $request->notes,
                'datatable_data'      => $request->datatable_data,
            ]);

            ActivityLogger::log(
                'Pricelist Data',
                'Create',
                "Membuat Pricelist baru: " . ($data->title ?? "ID #{$data->id}"),
                ['pricelist_id' => $data->id, 'title' => $data->title]
            );

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
         $pl = PriceList::where('id', $pricelist->id)
            ->firstOrFail();

        $data = collect([$pl]);
        return view('data.view_pricelist_single', compact('data'));
    }

    public function edit(PriceList $pricelist)
    {
        $logo     = HeaderLogo::all();
        $currency = Currency::all();
        return view('forms.pricelist', compact('logo', 'currency', 'pricelist'));
    }

    public function update(Request $request, PriceList $pricelist)
    {
        $rules = [
            'header_logo_id'      => 'nullable|string|max:255',
            'title'               => 'nullable|string|max:255',
            'footer_text'         => 'nullable|string|max:255',
            'date'                => 'nullable|date',
            'currency_id'         => 'nullable|string|max:255',
            'show_payment_method' => 'required',
            'payment_method'      => 'nullable|string|max:255',
            'notes'               => 'nullable|string',
            'datatable_data'      => 'nullable|json',
        ];

        if ($request->has('date')) {
            $date            = strtotime($request->date);
            $formattedDate   = date('Y-m-d', $date);
            $request['date'] = $formattedDate;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $oldData = $pricelist->only(['title', 'footer_text', 'date', 'currency_id', 'payment_method', 'notes']);

            $dataToUpdate = [
                'user_id'             => Auth::id(),
                'header_logo_id'      => $request->header_logo_id,
                'title'               => $request->title,
                'footer_text'         => $request->footer_text,
                'date'                => $request->date,
                'currency_id'         => $request->currency_id,
                'show_payment_method' => $request->show_payment_method,
                'payment_method'      => $request->payment_method,
                'notes'               => $request->notes,
                'datatable_data'      => $request->datatable_data,
            ];

            $changes = collect($dataToUpdate)->filter(function ($value, $key) use ($pricelist) {
                return $pricelist->$key != $value;
            });

            if ($changes->isNotEmpty()) {
                $pricelist->update($dataToUpdate);

                ActivityLogger::log(
                    'Pricelist Data',
                    'Update',
                    "Mengubah data Pricelist: " . ($pricelist->title ?? "ID #{$pricelist->id}"),
                    [
                        'pricelist_id' => $pricelist->id,
                        'old_values'   => $oldData,
                        'new_values'   => collect($dataToUpdate)->only(['title', 'footer_text', 'date', 'currency_id', 'payment_method', 'notes'])->toArray()
                    ]
                );

                return redirect()->route('pricelists.edit', $pricelist)
                    ->with('success', 'Data successfully updated')
                    ->with('open_pdf', route('pricelist.pdf', $pricelist->id));
            } else {
                return redirect()->route('pricelists.edit', $pricelist)
                    ->with('success', 'No changes made, opened latest PDF')
                    ->with('open_pdf', route('pricelist.pdf', $pricelist->id));
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update data: ' . $e->getMessage());
        }
    }

    public function destroy(PriceList $pricelist)
    {
        try {
            $title = $pricelist->title ?? "ID #{$pricelist->id}";
            $id = $pricelist->id;
            $pricelist->delete();

            ActivityLogger::log(
                'Pricelist Data',
                'Delete',
                "Menghapus Pricelist: {$title}",
                ['deleted_id' => $id, 'title' => $title]
            );

            return redirect()->route('pricelists.index')
                ->with('success', 'Data successfully deleted');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete data: ' . $e->getMessage());
        }
    }
}
