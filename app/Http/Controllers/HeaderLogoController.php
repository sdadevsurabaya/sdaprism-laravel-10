<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HeaderLogoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = HeaderLogo::all();
        return view('master.list-brand-logo', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('forms.form-brand-logo');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Ensure the public/logos directory exists
        $path = 'assets/img/brand';
        $directory = public_path($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Generate a unique filename to avoid conflicts
        $fileName = time() . '_' . $request->file('logo')->getClientOriginalName();
        $logoPath = $path . '/' . $fileName;

        // Move the file to public/logos
        $request->file('logo')->move($directory, $fileName);

        $brand = HeaderLogo::create([
            'name' => $request->name,
            'logo_path' => $logoPath,
        ]);

        ActivityLogger::log(
            'Brand Logo Master',
            'Upload',
            "Mengunggah Brand Logo baru: {$brand->name}",
            ['name' => $brand->name, 'file_path' => $logoPath]
        );

        return redirect()->route('brand.index')
            ->with('success', 'Brand logo created successfully.');
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
        $brand = HeaderLogo::findOrFail($id);
        return view('forms.form-brand-logo', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $logo = HeaderLogo::findOrFail($id);
        $oldName = $logo->name;

        $data = ['name' => $request->name];

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($logo->logo_path && File::exists(public_path($logo->logo_path))) {
                File::delete(public_path($logo->logo_path));
            }

            $path = 'assets/img/brand';
            // Ensure the public/logos directory exists
            $directory = public_path($path);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Generate a unique filename
            $fileName = time() . '_' . $request->file('logo')->getClientOriginalName();
            $data['logo_path'] = $path . '/' . $fileName;

            // Move the file to public/logos
            $request->file('logo')->move($directory, $fileName);
        }

        $logo->update($data);

        ActivityLogger::log(
            'Brand Logo Master',
            'Update',
            "Mengubah Brand Logo: {$logo->name}",
            ['old_name' => $oldName, 'new_name' => $logo->name, 'new_file' => isset($data['logo_path'])]
        );

        return redirect()->route('brand.index')
            ->with('success', 'Brand logo updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $logo = HeaderLogo::findOrFail($id);
        $name = $logo->name;

        // Delete logo file from public/logos
        if ($logo->logo_path && File::exists(public_path($logo->logo_path))) {
            File::delete(public_path($logo->logo_path));
        }

        $logo->delete();

        ActivityLogger::log(
            'Brand Logo Master',
            'Delete',
            "Menghapus Brand Logo: {$name}",
            ['deleted_name' => $name]
        );

        return redirect()->route('brand.index')
            ->with('success', 'Brand logo deleted successfully.');
    }
}
