<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Currency::all();
        return view('master.list-currency', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('forms.form-currency');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:currencies,code',
            'symbol' => 'nullable|string|max:10',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $currency = Currency::create([
            'code' => $request->code,
            'symbol' => $request->symbol,
            'name' => $request->name,
            'is_active' => $request->is_active ?? 0,
        ]);

        ActivityLogger::log(
            'Currency Master',
            'Create',
            "Menambahkan Currency baru: {$currency->name} ({$currency->code})",
            ['code' => $currency->code, 'name' => $currency->name, 'symbol' => $currency->symbol]
        );

        return redirect()->route('currency.index')
            ->with('success', 'Currency created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $currency = Currency::findOrFail($id);
        return view('forms.form-currency', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:currencies,code,' . $id,
            'symbol' => 'nullable|string|max:10',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $currency = Currency::findOrFail($id);
        $oldData = ['code' => $currency->code, 'name' => $currency->name, 'symbol' => $currency->symbol];

        $currency->update([
            'code' => $request->code,
            'symbol' => $request->symbol,
            'name' => $request->name,
            'is_active' => $request->is_active ?? 0,
        ]);

        ActivityLogger::log(
            'Currency Master',
            'Update',
            "Mengubah Currency: {$currency->name} ({$currency->code})",
            [
                'old_values' => $oldData,
                'new_values' => ['code' => $currency->code, 'name' => $currency->name, 'symbol' => $currency->symbol]
            ]
        );

        return redirect()->route('currency.index')
            ->with('success', 'Currency updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $currency = Currency::findOrFail($id);
        $name = $currency->name;
        $code = $currency->code;
        $currency->delete();

        ActivityLogger::log(
            'Currency Master',
            'Delete',
            "Menghapus Currency: {$name} ({$code})",
            ['deleted_name' => $name, 'deleted_code' => $code]
        );

        return redirect()->route('currency.index')
            ->with('success', 'Currency deleted successfully.');
    }
}
